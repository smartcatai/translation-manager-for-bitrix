<?php

namespace Smartcat\Connector\Helper;


use Smartcat\Connector\Agent\Task;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class IblockHelper
{

    const ERROR_LINKED_ELEMENT_NOT_FOUND = 1;

    /**
     *
     * @param $elementID
     * @param $iblockID
     * @param $targetElementID
     * @return bool|int
     */
    public static function copyElementToIB($elementID, $iblockID, $targetElementID = null)
    {
        global $APPLICATION;

        Loader::includeModule('iblock');
        $bCatalogAvailable = Loader::includeModule('catalog');
        if ($bCatalogAvailable) {
            $arCatalog = \CCatalog::GetByID($iblockID);
            if (!$arCatalog) $bCatalogAvailable = false;
        }

        if ($targetElementID > 0) {
            $rsTargetElement = \CIBlockElement::GetByID($targetElementID);
            if ($rsTargetElement->SelectedRowsCount() <= 0) {
                $targetElementID = null;
            }
        }

        $bWaitLinkedElements = Option::get('smartcat.connector', 'wait_linked_elements', 'N');

        $obElement = \CIBlockElement::GetByID($elementID)->GetNextElement(true, false);

        $arElement = $obElement->GetFields();

        if ($arElement['IBLOCK_SECTION_ID'] > 0) {
            $arElement['IBLOCK_SECTION_ID'] = self::copySectionToIB($arElement['IBLOCK_SECTION_ID'], $iblockID);
        }

        Task::log("NEW ELEMENT SECTION IS: ", $arElement['IBLOCK_SECTION_ID']);

        $arTargetIblockProps = [];
        $rsTargetIblockProps = \CIBlockProperty::GetList([], [
            'IBLOCK_ID' => $iblockID,
        ]);
        while ($arProp = $rsTargetIblockProps->Fetch()) {
            $arTargetIblockProps[$arProp['CODE']] = $arProp;
        }


        $arElement['IBLOCK_ID'] = $iblockID;
        unset($arElement['ID']);
        unset($arElement['IBLOCK_TYPE_ID']);
        unset($arElement['IBLOCK_CODE']);
        unset($arElement['IBLOCK_NAME']);
        unset($arElement['IBLOCK_EXTERNAL_ID']);
        unset($arElement['DETAIL_PAGE_URL']);
        unset($arElement['LIST_PAGE_URL']);
        unset($arElement['CANONICAL_PAGE_URL']);
        unset($arElement['CREATED_DATE']);
        unset($arElement['CREATED_USER_NAME']);
        unset($arElement['LOCKED_USER_NAME']);
        unset($arElement['USER_NAME']);
        unset($arElement['LOCK_STATUS']);
        unset($arElement['TIMESTAMP_X']);
        unset($arElement['DATE_CREATE_UNIX']);
        unset($arElement['EXTERNAL_ID']);

        $arElement['PROPERTY_VALUES'] = [];

        $arPropValues = $obElement->GetProperties();
        foreach ($arPropValues as $sPropCode => $arProp) {
            if ($arProp['PROPERTY_TYPE'] == 'E') {

                $arTargetProp = $arTargetIblockProps[$sPropCode];
                if ($bWaitLinkedElements == 'Y') {
                    if ($arProp['MULTIPLE'] == 'Y') {
                        foreach ($arProp['VALUE'] as &$iOriginalID) {
                            $arOriginalElement = \CIBlockElement::GetByID($iOriginalID)->Fetch();
                            if ($arOriginalElement) {
                                $sXMLID = $arOriginalElement['XML_ID'] ?: 'FROM_' . $arOriginalElement['IBLOCK_ID'] . '_' . $iOriginalID;
                                $arNewElement = \CIBlockElement::GetList([], [
                                    'XML_ID' => $sXMLID,
                                    'IBLOCK_ID' => $arTargetProp['LINK_IBLOCK_ID'],
                                ], false, false, ['ID'])->Fetch();
                                if ($arNewElement) {
                                    $iOriginalID = $arNewElement['ID'];
                                } else {
                                    if ($arOriginalElement['IBLOCK_ID'] != $arTargetProp['LINK_IBLOCK_ID'])
                                        throw new \Exception('Not found linked element with IBLOCK_ID ' . $arTargetProp['LINK_IBLOCK_ID'] . ' and XML_ID=' . $sXMLID, self::ERROR_LINKED_ELEMENT_NOT_FOUND);
                                }
                            }
                        }
                        unset($iOriginalID);
                    } else {
                        if (!empty($arProp['VALUE'])) {
                            $arOriginalElement = \CIBlockElement::GetByID($arProp['VALUE'])->Fetch();
                            if ($arOriginalElement) {
                                $sXMLID = $arOriginalElement['XML_ID'] ?: 'FROM_' . $arOriginalElement['IBLOCK_ID'] . '_' . $arOriginalElement['ID'];
                                $arNewElement = \CIBlockElement::GetList([], [
                                    'XML_ID' => $sXMLID,
                                    'IBLOCK_ID' => $arTargetProp['LINK_IBLOCK_ID'],
                                ], false, false, ['ID'])->Fetch();
                                if ($arNewElement) {
                                    $arProp['VALUE'] = $arNewElement['ID'];
                                } else {
                                    if ($arOriginalElement['IBLOCK_ID'] != $arTargetProp['LINK_IBLOCK_ID'])
                                        throw new \Exception('Not found linked element with IBLOCK_ID ' . $arTargetProp['LINK_IBLOCK_ID'] . ' and XML_ID=' . $sXMLID, self::ERROR_LINKED_ELEMENT_NOT_FOUND);
                                }
                            }
                        }
                    }
                }

                $arElement['PROPERTY_VALUES'][$sPropCode] = $arProp['VALUE'];

            } elseif ($arProp['PROPERTY_TYPE'] == 'L') {
                if (!empty($arProp['VALUE_XML_ID'])) {
                    if ($arProp['MULTIPLE'] == 'Y') {
                        foreach ($arProp['VALUE_XML_ID'] as $valueXMLID) {
                            $arTargetValue = \CIBlockPropertyEnum::GetList([], [
                                'IBLOCK_ID' => $iblockID,
                                'XML_ID' => $valueXMLID,
                                'CODE' => $arProp['CODE'],
                            ])->Fetch();
                            $arElement['PROPERTY_VALUES'][$sPropCode][] = $arTargetValue['ID'];
                        }

                    } else {
                        $arTargetValue = \CIBlockPropertyEnum::GetList([], [
                            'IBLOCK_ID' => $iblockID,
                            'XML_ID' => $arProp['VALUE_XML_ID'],
                            'CODE' => $arProp['CODE'],
                        ])->Fetch();
                        $arElement['PROPERTY_VALUES'][$sPropCode] = $arTargetValue['ID'];
                    }
                }
            } else {
                $arElement['PROPERTY_VALUES'][$sPropCode] = $arProp['VALUE'];
            }
        }


        if ($arElement['DETAIL_PICTURE'] > 0) {
            $arElement['DETAIL_PICTURE'] = \CFile::MakeFileArray($arElement['DETAIL_PICTURE']);
            if (!file_exists($arElement['DETAIL_PICTURE']['tmp_name'])) {
                unset($arElement['DETAIL_PICTURE']);
            }
        }

        if ($arElement['PREVIEW_PICTURE'] > 0) {
            $arElement['PREVIEW_PICTURE'] = \CFile::MakeFileArray($arElement['PREVIEW_PICTURE']);
            if (!file_exists($arElement['PREVIEW_PICTURE']['tmp_name'])) {
                unset($arElement['PREVIEW_PICTURE']);
            }
        }


        $CIBLockElement = new \CIBlockElement();

        $createdElementID = null;

        if ($targetElementID > 0) {
            if (!$CIBLockElement->Update($targetElementID, $arElement)) {
                throw new \Exception($CIBLockElement->LAST_ERROR);
            }
            $createdElementID = $targetElementID;
        } else {
            $ID = $CIBLockElement->Add($arElement);
            if (!$ID) {
                throw new \Exception($CIBLockElement->LAST_ERROR);
            }
            $createdElementID = $ID;
        }

        if ($createdElementID > 0) {

            if ($bCatalogAvailable) {
                $arProduct = \CCatalogProduct::GetByID($elementID);
                if ($arProduct) {
                    $arProduct['ID'] = $createdElementID;
                    unset($arProduct['PURCHASING_PRICE']);
                    unset($arProduct['PURCHASING_CURRENCY']);
                    $cCatalogProduct = new \CCatalogProduct();
                    if (!$cCatalogProduct->Add($arProduct)) {
                        $sMessage = '';
                        foreach ($APPLICATION->GetException()->GetMessages() as $arMessage) {
                            $sMessage .= $arMessage['text'] . PHP_EOL;
                        }
                        throw new \Exception($sMessage);
                    }

                    $rsPrices = \CPrice::GetList(
                        [],
                        [
                            "PRODUCT_ID" => $elementID,
                        ]
                    );
                    $cPrice = new \CPrice();
                    \CPrice::DeleteByProduct($createdElementID);
                    while ($arPrice = $rsPrices->Fetch()) {
                        unset($arPrice['ID']);
                        $arPrice['PRODUCT_ID'] = $createdElementID;
                        $cPrice->Add($arPrice);
                    }

                    $rsCatalogStoreProduct = \CCatalogStoreProduct::GetList([], [
                        'PRODUCT_ID' => $elementID,
                    ], false, false, ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']);

                    while ($arCatalogStoreProduct = $rsCatalogStoreProduct->Fetch()) {
                        unset($arCatalogStoreProduct['ID']);
                        $arCatalogStoreProduct['PRODUCT_ID'] = $createdElementID;

                        $arStock = \CCatalogStoreProduct::GetList([], [
                            'PRODUCT_ID' => $createdElementID,
                            'STORE_ID' => $arCatalogStoreProduct['STORE_ID'],
                        ], false, false, ['ID'])->Fetch();


                        if ($arStock) {
                            \CCatalogStoreProduct::Update($arStock['ID'], $arCatalogStoreProduct);
                        } else {
                            \CCatalogStoreProduct::Add($arCatalogStoreProduct);
                        }

                    }

                }
            }


        }

        return $createdElementID;
    }

    public static function copySectionToIB($sectionID, $iblockID, $targetSectionID = null)
    {
        Loader::includeModule('iblock');

        if (!empty($targetSectionID)) {
            $rsTargetSection = \CIBlockSection::GetByID($targetSectionID);
            if ($rsTargetSection->SelectedRowsCount() <= 0) {
                $targetSectionID = null;
            }
        }

        $obSection = \CIBlockSection::GetByID($sectionID)->GetNextElement(true, false);

        $arSection = $obSection->GetFields();
        $arSection['XML_ID'] = 'FROM_' . $arSection['IBLOCK_ID'] . '_' . $sectionID;

        $arSection['IBLOCK_ID'] = $iblockID;

        if ($arSection['IBLOCK_SECTION_ID'] > 0) {
            $arSection['IBLOCK_SECTION_ID'] = self::copySectionToIB($arSection['IBLOCK_SECTION_ID'], $iblockID);
        } else {
            unset($arSection['IBLOCK_SECTION_ID']);
        }

        $arExisting = \CIBlockSection::GetList([], [
            'IBLOCK_ID' => $iblockID,
            'XML_ID' => $arSection['XML_ID'],
            'CHECK_PERMISSIONS' => 'N',
        ], false, ['ID', 'NAME'])->Fetch();

        if ($arExisting) {
            $targetSectionID = $arExisting['ID'];
        }

        if (empty($targetSectionID)) {
            $arExisting = \CIBlockSection::GetList([], [
                'IBLOCK_ID' => $arSection['IBLOCK_ID'],
                'DEPTH_LEVEL' => $arSection['DEPTH_LEVEL'],
                'NAME' => $arSection['NAME'],
                'CHECK_PERMISSIONS' => 'N',
            ], false, ['ID', 'NAME', 'IBLOCK_ID'])->Fetch();
            if ($arExisting) {
                $targetSectionID = $arExisting['ID'];
            }
        }

        if ($targetSectionID > 0) {
            return $targetSectionID;
        }

        unset($arSection['ID']);
        unset($arSection['IBLOCK_TYPE_ID']);
        unset($arSection['IBLOCK_CODE']);
        unset($arSection['IBLOCK_NAME']);
        unset($arSection['IBLOCK_EXTERNAL_ID']);
        unset($arSection['SECTION_PAGE_URL']);
        unset($arSection['LIST_PAGE_URL']);
        unset($arSection['DATE_CREATE']);
        unset($arSection['TIMESTAMP_X']);
        unset($arSection['GLOBAL_ACTIVE']);
        unset($arSection['SEARCHABLE_CONTENT']);
        unset($arSection['LEFT_MARGIN']);
        unset($arSection['RIGHT_MARGIN']);
        unset($arSection['DEPTH_LEVEL']);
        unset($arSection['TMP_ID']);
        unset($arSection['EXTERNAL_ID']);

        if ($arSection['PICTURE'] > 0) {
            $arSection['PICTURE'] = \CFile::MakeFileArray($arSection['PICTURE']);
            if (!file_exists($arSection['PICTURE']['tmp_name'])) {
                unset($arSection['PICTURE']);
            }
        }
        if ($arSection['DETAIL_PICTURE'] > 0) {
            $arSection['DETAIL_PICTURE'] = \CFile::MakeFileArray($arSection['DETAIL_PICTURE']);
            if (!file_exists($arSection['DETAIL_PICTURE']['tmp_name'])) {
                unset($arSection['DETAIL_PICTURE']);
            }
        }

        $CIBlockSection = new \CIBlockSection();

        if (!empty($targetSectionID)) {
            if (!$CIBlockSection->Update($targetSectionID, $arSection)) {
                throw new \Exception($CIBlockSection->LAST_ERROR);
            }
            return $targetSectionID;
        } else {
            $ID = $CIBlockSection->Add($arSection);
            if (!$ID) {
                throw new \Exception($CIBlockSection->LAST_ERROR);
            }
            return $ID;
        }
    }

    /**
     * @param $iblockID
     * @param $lang
     * @return int $iblockID
     * @throws \Exception
     */
    public static function createIBForLang($iblockID, $lang)
    {
        $CIBlock = new \CIBlock();
        $CIBlockType = new \CIBlockType();

        $arIblockFrom = \CIBlock::GetByID($iblockID)->Fetch();
        if (!$arIblockFrom) {
            throw new \Exception(GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_NAYTI_ISH"));
        }

        $sTargetTypeID = $arIblockFrom['IBLOCK_TYPE_ID'] . '_' . $lang;
        $sTargetTypeID = preg_replace('/[^a-z0-9_]/i', '_', $sTargetTypeID);

        $arTargetType = \CIBlockType::GetByID($sTargetTypeID)->Fetch();

        if (!$arTargetType) {
            $arTargetType = \CIBlockType::GetByID($arIblockFrom['IBLOCK_TYPE_ID'])->GetNext(false, false);
            $arTargetTypeLang = \CIBlockType::GetByIDLang($arIblockFrom['IBLOCK_TYPE_ID'], LANGUAGE_ID);

            $arTargetType = \Smartcat\Connector\Helper\ArrayHelper::cleanUpTilda($arTargetType);
            $arTargetTypeLang = \Smartcat\Connector\Helper\ArrayHelper::cleanUpTilda($arTargetTypeLang);

            $arTargetType['ID'] = $sTargetTypeID;
            $arTargetTypeLang['NAME'] .= ' (' . strtoupper($lang) . ')';

            $arTargetType['LANG'][LANGUAGE_ID] = $arTargetTypeLang;

            $res = $CIBlockType->Add($arTargetType);
            if (!$res) {
                throw new \Exception(GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_SOZDATQ_T") . $arTargetTypeLang['NAME'] . '": ' . $CIBlockType->LAST_ERROR);
            }
        }

        $arTargetIBlock = $arIblockFrom;

        unset($arTargetIBlock['ID']);
        unset($arTargetIBlock['TIMESTAMP_X']);
        unset($arTargetIBlock['XML_ID']);
        unset($arTargetIBlock['TMP_ID']);
        unset($arTargetIBlock['EXTERNAL_ID']);
        $arTargetIBlock['IBLOCK_TYPE_ID'] = $arTargetType['ID'];
        $arTargetIBlock['CODE'] .= '_' . $lang;
        $arTargetIBlock['NAME'] .= ' (' . strtoupper($lang) . ')';


        $arExisting = \CIBlock::GetList([], ['CODE' => $arTargetIBlock['CODE']])->Fetch();
        if ($arExisting) {
            return $arExisting['ID'];
        } else {
            $ID = $CIBlock->Add($arTargetIBlock);
            if (!$ID) {
                throw new \Exception(GetMessage("SMARTCAT_CONNECTOR_NE_UDALOSQ_SOZDATQ_I") . $arTargetIBlock['NAME'] . '": ' . $CIBlock->LAST_ERROR);
            }
            return $ID;
        }
    }

    public static function copyIBlockProps($iblockIDFrom, $iblockIDTo)
    {
        Loader::includeModule('iblock');

        $rsProps = \CIBlockProperty::GetList([], [
            'IBLOCK_ID' => $iblockIDFrom,
        ]);

        $CIBLockProperty = new \CIBlockProperty();
        $CIBlockPropertyEnum = new \CIBlockPropertyEnum();

        while ($arProp = $rsProps->Fetch()) {

            $arPropCopy = \CIBlockProperty::GetList([], [
                'IBLOCK_ID' => $iblockIDTo,
                'CODE' => $arProp['CODE'],
            ])->Fetch();

            if (!$arPropCopy) {
                $arPropCopy = $arProp;
                unset($arPropCopy['TIMESTAMP_X']);
                $arPropCopy['IBLOCK_ID'] = $iblockIDTo;
                $arPropCopy['ID'] = $CIBLockProperty->Add($arPropCopy);
            }

            if ($arPropCopy['ID'] > 0) {

                if ($arProp['PROPERTY_TYPE'] == 'L') {
                    //echo '<pre>' . print_r($arProp, true) . '</pre>';
                    $rsValues = \CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => $arProp['IBLOCK_ID'], "CODE" => $arProp['CODE']]);

                    while ($arValue = $rsValues->Fetch()) {

                        $arValueCopy = \CIBlockPropertyEnum::GetList([], [
                            "IBLOCK_ID" => $arPropCopy['IBLOCK_ID'],
                            "CODE" => $arPropCopy['CODE'],
                            "XML_ID" => $arValue['XML_ID'],
                        ])->Fetch();

                        if (!$arValueCopy) {
                            $arValueCopy = $arValue;
                            $arValueCopy['PROPERTY_ID'] = $arPropCopy['ID'];
                            unset($arValueCopy['ID']);

                            $arValueCopy['ID'] = $CIBlockPropertyEnum->Add($arValueCopy);
                        }

                    }
                }
            }
        }


    }


}