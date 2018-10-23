<?php

namespace Abbyy\Cloud\Agent;

use Abbyy\Cloud\Helper\IblockHelper;
use Abbyy\Cloud\Helper\StringHelper;
use Abbyy\Cloud\ProfileIblockTable;
use Abbyy\Cloud\ProfileTable;
use Abbyy\Cloud\TaskFileTable;
use Abbyy\Cloud\TaskTable;
use ABBYY\CloudAPI\API\Model\FileInfoViewModel;
use ABBYY\CloudAPI\API\Model\FullOrderViewModel;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

class Task
{

    public static function Check()
    {
        self::log("Start Check");

        self::log("Start CheckNewTasks");
        self::CheckNewTasks();

        self::log("Start CheckUploadedTasks");
        self::CheckUploadedTasks();

        self::log("Start CheckInProgressTasks");
        self::CheckInProgressTasks();

        self::log("Done");
        return '\\' . __METHOD__ . '();';
    }

    public static function CheckNewTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_NEW,
            ]
        ]);

        if ($rsTasks->getSelectedRowsCount() > 0) {

            $apiId = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'api_id');
            $apiSecret = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'api_secret');
            $cloudApi = new \ABBYY\CloudAPI\SmartCAT($apiId, $apiSecret);
            $fileManager = $cloudApi->getFileManager();

            while ($arTask = $rsTasks->fetch()) {

                $arProfile = ProfileTable::getById($arTask['PROFILE_ID'])->fetch();

                $sFilePath = tempnam(sys_get_temp_dir(), 'TRANSLATE-');

                file_put_contents($sFilePath, '<html><head></head><body>' . $arTask['CONTENT'] . '</body></html>');

                try {
                    $result = $fileManager->fileUploadFile([
                        'fileName' => $arProfile['ID'] . '_' . $arTask['ID'] . '.html',
                        'filePath' => $sFilePath,
                    ]);
                } catch (\Exception $e) {
                    self::log($e->getMessage() . ' ' . $e->getResponse()->getBody()->getContents(), __METHOD__, __LINE__);
                }

                if (!empty($result) && is_array($result)) {
                    $result = reset($result);
                    TaskTable::update($arTask['ID'], [
                        'FILE_ID' => $result->getId(),
                        'FILE_TOKEN' => $result->getToken(),
                        'STATUS' => TaskTable::STATUS_UPLOADED,
                    ]);
                }

            }
        }
    }

    public static function CheckUploadedTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_UPLOADED,
            ]
        ]);

        if ($rsTasks->getSelectedRowsCount() > 0) {

            $apiId = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'api_id');
            $apiSecret = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'api_secret');
            $notifyEmail = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'notify_email');
            $cloudApi = new \ABBYY\CloudAPI\SmartCAT($apiId, $apiSecret);
            $orderManager = $cloudApi->getOrderManager();
            $fileManager = $cloudApi->getFileManager();

            if (empty($notifyEmail)) {
                $notifyEmail = \Bitrix\Main\Config\Option::get('main', 'email_from');
            }

            while ($arTask = $rsTasks->fetch()) {

                try {
                    $result = $fileManager->fileGetFileInfo($arTask['FILE_ID'], $arTask['FILE_TOKEN']);
                } catch (\Exception $e) {
                    self::log($e->getMessage() . ' ' . $e->getResponse()->getBody()->getContents(), __METHOD__, __LINE__);
                }
                if ($result && $result instanceof FileInfoViewModel && $result->getReadingStatus() == 'Done') {

                    $arProfile = ProfileTable::getById($arTask['PROFILE_ID'])->fetch();

                    $rsFiles = TaskFileTable::getList([
                        'filter' => [
                            '=TASK_ID' => $arTask['ID'],
                        ],
                    ]);

                    $arLangTo = [];
                    while ($arFile = $rsFiles->fetch()) {
                        $arLangTo[] = $arFile['LANG_TO'];
                    }

                    $file = new \ABBYY\CloudAPI\API\Model\GetFileModel();
                    $file->setId($arTask['FILE_ID']);
                    $file->setToken($arTask['FILE_TOKEN']);

                    $order = new \ABBYY\CloudAPI\API\Model\SubmitOrderModel();
                    $order->setFiles([$file]);
                    $order->setFrom($arProfile['LANG']);
                    $order->setTo($arLangTo);
                    $order->setCostType('Default');
                    $order->setUnitType('Words');
                    $order->setCurrency('RUB');
                    $order->setEmail($notifyEmail);
                    $order->setApprovalRequired(false);
                    $order->setIsManualEstimation(false);
                    if ($arTask['DEADLINE'] instanceof DateTime) {
                        $timestamp = $arTask['DEADLINE']->getTimestamp();
                        if (($timestamp - time()) < 2 * 3600) $timestamp = time() + 2 * 3600 + 20;
                        $order->setDeadline(date('Y-m-d\\TH:i:s.0\\Z', $timestamp));
                    }

                    $order->setType($arTask['TYPE']);

                    try {
                        $result = $orderManager->orderSubmitOrder($order);
                    } catch (\Exception $e) {
                        self::log($e->getMessage() . ' ' . $e->getResponse()->getBody()->getContents(), __METHOD__, __LINE__);
                    }

                    if ($result && $result instanceof FullOrderViewModel) {
                        TaskTable::update($arTask['ID'], [
                            'STATUS' => TaskTable::STATUS_PROCESS,
                            'ORDER_ID' => $result->getId(),
                            'ORDER_NUMBER' => $result->getNumber(),
                            'AMOUNT' => $result->getAmount(),
                            'CURRENCY' => $result->getCurrency(),
                            'DEADLINE' => $result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : ''
                        ]);
                    }

                }

            }
        }
    }

    public static function CheckWaitingTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_WAITING,
                '!ORDER_ID' => false,
            ]
        ]);
    }

    public static function CheckInProgressTasks()
    {
        $rsTasks = TaskTable::getList([
            'order' => ['ID' => 'asc'],
            'filter' => [
                '=STATUS' => TaskTable::STATUS_PROCESS,
                '!ORDER_ID' => false,
            ]
        ]);

        if ($rsTasks->getSelectedRowsCount() > 0) {

            $apiId = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'api_id');
            $apiSecret = \Bitrix\Main\Config\Option::get('abbyy.cloud', 'api_secret');
            $cloudApi = new \ABBYY\CloudAPI\SmartCAT($apiId, $apiSecret);
            $orderManager = $cloudApi->getOrderManager();
            $fileManager = $cloudApi->getFileManager();

            Loader::includeModule('iblock');
            $CIBlockElement = new \CIBlockElement();

            while ($arTask = $rsTasks->fetch()) {
                self::log("Task", $arTask['ID']);
                $bHasErrors = false;
                $bWaiting = false;
                $sErrorComment = '';

                try {
                    self::log("GetOrder", $arTask['ORDER_ID']);
                    $result = $orderManager->orderGetOrder($arTask['ORDER_ID']);
                    self::log("End GetOrder", $arTask['ORDER_ID']);
                } catch (\Exception $e) {
                    $bHasErrors = true;
                    self::log($e->getMessage() . ' ' . $e->getResponse()->getBody()->getContents(), __METHOD__, __LINE__);
                }

                if ($result && $result instanceof FullOrderViewModel) {
                    self::log('Current status:', $result->getStatus());
                    if ($result->getStatus() == 'Done') {

                        $arProfile = ProfileTable::getById($arTask['PROFILE_ID'])->fetch();


                        foreach ($result->getTranslations() as $translation) {
                            if ($translation->getStatus() !== 'Done') {
                                self::log("Status of task is", $translation->getStatus(), "skipping");
                                continue;
                            }

                            self::log("Translation", $translation->getTargetFile()->getLanguage());

                            $arFile = TaskFileTable::getList([
                                'filter' => [
                                    '=TASK_ID' => $arTask['ID'],
                                    '=LANG_TO' => $translation->getTargetFile()->getLanguage(),
                                ],
                            ])->fetch();

                            if ($arFile) {

                                if ($arFile['STATUS'] == TaskFileTable::STATUS_SUCCESS) {
                                    //   self::log("Skip by status", $arFile['STATUS']);
                                    // continue;
                                }

                                if ($arFile['STATUS'] == TaskFileTable::STATUS_FAILED) {
                                    self::log("Skip by status", $arFile['STATUS']);
                                    $bHasErrors = true;
                                    continue;
                                }

                                self::log("Start DownloadFile");
                                $targetFile = $translation->getTargetFile();
                                self::log($targetFile->getId(), $targetFile->getToken());

                                $translate = $fileManager->fileDownloadFile($targetFile->getId(), $targetFile->getToken());

                                $stream = $translate->getBody();
                                $translateText = '';
                                while (!$stream->eof()) {
                                    $translateText .= $stream->read(1024);
                                }
                                $stream->close();

                                self::log("End DownloadFile");


                                self::log("Parse file content");

                                preg_match_all('/<field id="(.+?)">(.*?)<\/field>/is', $translateText, $matches);
                                $arFields = [];
                                $arProps = [];
                                $arSections = [];
                                foreach ($matches[1] as $i => $sField) {
                                    if (substr($sField, 0, 4) == 'PROP') {
                                        $arProps[substr($sField, 5)] = html_entity_decode($matches[2][$i]);
                                    } elseif (substr($sField, 0, 17) == 'IBLOCK_SECTION_ID') {
                                        $arSections[] = $matches[2][$i];
                                    } else {
                                        $arFields[$sField] = StringHelper::specialcharsDecode($matches[2][$i]);
                                    }
                                }
                                self::log("End parse file content");

                                $arProfileIblock = ProfileIblockTable::getList([
                                    'filter' => [
                                        '=PROFILE_ID' => $arProfile['ID'],
                                        '=LANG' => $arFile['LANG_TO'],
                                    ],
                                ])->fetch();

                                $arElement = [
                                    'IBLOCK_ID' => $arProfileIblock['IBLOCK_ID'],
                                    'ACTIVE' => $arProfile['PUBLISH'] == 'Y' ? 'Y' : 'N',
                                    'PREVIEW_TEXT_TYPE' => 'html',
                                    'DETAIL_TEXT_TYPE' => 'html',
                                ];

                                IblockHelper::copyIBlockProps($arProfile['IBLOCK_ID'], $arProfileIblock['IBLOCK_ID']);

                                $elementID = 0;

                                try {
                                    $elementID = IblockHelper::copyElementToIB($arTask['ELEMENT_ID'], $arProfileIblock['IBLOCK_ID'], $arFile['ELEMENT_ID']);
                                } catch (\Exception $e) {

                                    /**
                                     * ≈сли один из св€занных элементов в свойствах не найден,
                                     * оставл€ем таск в статусе PROCESS и ждем, пока все св€занные элементы будут переведены
                                     */
                                    if ($e->getCode() == IblockHelper::ERROR_LINKED_ELEMENT_NOT_FOUND) {
                                        TaskTable::update($arTask['ID'], [
                                            'STATUS' => TaskTable::STATUS_PROCESS,
                                            'COMMENT' => $e->getMessage(),
                                        ]);
                                        $bWaiting = true;
                                        continue;
                                    }

                                    self::log('Copy Error', $e->getMessage(), $arElement);
                                    $sErrorComment = $e->getMessage();
                                    $bHasErrors = true;
                                }
                                foreach ($arProfile['FIELDS']['FIELDS'] as $sField) {
                                    $arElement[$sField] = $arFields[$sField];
                                }

                                unset($arElement['IBLOCK_SECTION_ID']);

                                if ($elementID > 0) {
                                    unset($arElement['ACTIVE']);
                                    $CIBlockElement->Update($elementID, $arElement);
                                } else {
                                    //$elementID = $CIBlockElement->Add($arElement);
                                }

                                if ($CIBlockElement->LAST_ERROR) {
                                    self::log('IB Error', $CIBlockElement->LAST_ERROR, $arElement);
                                    $bHasErrors = true;
                                    $sErrorComment .= ' ' . $CIBlockElement->LAST_ERROR;
                                }


                                self::log("New element ID", $elementID);

                                if ($elementID > 0) {

                                    \CIBlockElement::SetPropertyValuesEx($elementID, $arElement['IBLOCK_ID'], $arProps);

                                    if (!empty($arSections)) {
                                        $CIBlockSection = new \CIBlockSection();
                                        $arElement = \CIBlockElement::GetByID($elementID)->Fetch();
                                        if ($arElement['IBLOCK_SECTION_ID'] > 0) {
                                            $rsSections = \CIBlockSection::GetNavChain($arElement['IBLOCK_ID'], $arElement['IBLOCK_SECTION_ID'], ['ID', 'NAME', 'XML_ID']);

                                            $i = 0;
                                            while ($arSection = $rsSections->Fetch()) {
                                                if (empty($arSection['XML_ID'])) continue;
                                                if (!empty($arSections[$i])) {
                                                    $res = $CIBlockSection->Update($arSection['ID'], [
                                                        'NAME' => trim($arSections[$i]),
                                                    ]);
                                                    if (!$res) {
                                                        $sErrorComment .= ' ' . $CIBlockSection->LAST_ERROR;
                                                        $bHasErrors = true;
                                                        self::log($CIBlockSection->LAST_ERROR, __LINE__);
                                                    }
                                                }

                                                $i++;
                                            }

                                        }
                                    }

                                    TaskFileTable::update($arFile['ID'], [
                                        'TRANSLATION' => $translateText,
                                        'STATUS' => TaskFileTable::STATUS_SUCCESS,
                                        'ELEMENT_ID' => $elementID,
                                    ]);
                                } else {
                                    TaskFileTable::update($arFile['ID'], [
                                        'STATUS' => TaskFileTable::STATUS_FAILED,
                                    ]);
                                    $bHasErrors = true;
                                    //$sErrorComment = $CIBlockElement->LAST_ERROR;
                                }

                            }


                        }

                        if (!$bWaiting) {
                            TaskTable::update($arTask['ID'], [
                                'STATUS' => $bHasErrors ? TaskTable::STATUS_FAILED : TaskTable::STATUS_SUCCESS,
                                'COMMENT' => $sErrorComment,
                                'DEADLINE' => $result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : ''
                            ]);
                        }
                    } elseif ($result->getStatus() == 'PaymentRequired') {

                        TaskTable::update($arTask['ID'], [
                            'STATUS' => TaskTable::STATUS_FAILED,
                            'COMMENT' => GetMessage("ABBYY_CLOUD_TREBUETSA_OPLATA"),
                            'DEADLINE' => $result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : ''
                        ]);
                    } elseif (in_array($result->getStatus(), ['InProgress', 'Paid', 'Submitted', 'New'])) {

                        TaskTable::update($arTask['ID'], [
                            'STATUS' => TaskTable::STATUS_PROCESS,
                            'COMMENT' => $result->getStatus(),
                            'DEADLINE' => $result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : ''
                        ]);

                    } elseif ($result->getStatus() == 'Canceled') {

                        TaskTable::update($arTask['ID'], [
                            'STATUS' => TaskTable::STATUS_CANCELED,
                            'COMMENT' => '',
                            'DEADLINE' => $result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : ''
                        ]);

                    } else {
                        TaskTable::update($arTask['ID'], [
                            'STATUS' => TaskTable::STATUS_FAILED,
                            'COMMENT' => $result->getStatus(),
                            'DEADLINE' => $result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : ''
                        ]);

                    }
                } else {
                    self::log($result);
                    TaskTable::update($arTask['ID'], [
                        'STATUS' => TaskTable::STATUS_FAILED,
                        'COMMENT' => $result ? $result->getReasonPhrase() : 'null response',
                        'DEADLINE' => $result ? ($result->getDeadline() instanceof \DateTime ? DateTime::createFromTimestamp($result->getDeadline()->getTimestamp()) : '') : null
                    ]);

                }

            }
        }
    }

    public static function log()
    {
        $arMessage = func_get_args();
        $arOutput = [];
        foreach ($arMessage as $mess) {
            if (is_array($mess) || is_object($mess)) $mess = print_r($mess, true);
            $arOutput[] = $mess;
        }
        $mess = implode(', ', $arOutput) . PHP_EOL;
        //echo date('d.m.Y H:i:s') . ': ' . $mess;
        //fwrite(STDERR, date('d.m.Y H:i:s') . ': ' . $mess);
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/task_log.txt', date('d.m.Y H:i:s') . ': ' . $mess . "\n", FILE_APPEND);
    }

}