<?php

require_once dirname(__FILE__) . "/lib/ABBYY/vendor/autoload.php";

/**
 * ������������ ������� �� ����� lib/
 * PSR-0
 * @param $className
 */
function smartcat_connector_autoload($className)
{
    $sModuleId = basename(dirname(__FILE__));
    $className = ltrim($className, '\\');
    $arParts = explode('\\', $className);

    $isABBYY = $arParts[0] == 'ABBYY';
    $sModuleCheck = strtolower($arParts[0] . '.' . $arParts[1]);

    if (!$isABBYY && $sModuleCheck != $sModuleId)
        return;


    $arParts = array_splice($arParts, 2);

    if ($isABBYY) {
        $arParts = array_merge(array('ABBYY', 'src', 'ABBYY', 'CloudAPI'), $arParts);
    }


    if (!empty($arParts)) {
        $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arParts) . '.php';
        if (file_exists($fileName))
            require_once $fileName;
    }
}

spl_autoload_register('smartcat_connector_autoload');