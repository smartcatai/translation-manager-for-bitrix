<?php

require_once dirname(__FILE__) . "/lib/Smartcat/vendor/autoload.php";

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

    $isSmarcat = $arParts[0] == 'SmartCat';
    $sModuleCheck = strtolower($arParts[0] . '.' . $arParts[1]);

    if (!$isSmarcat && $sModuleCheck != $sModuleId)
        return;

    if (!empty($arParts) && !$isSmarcat) {
        $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arParts) . '.php';
        if (file_exists($fileName))
            require_once $fileName;
    }
}

spl_autoload_register('smartcat_connector_autoload');