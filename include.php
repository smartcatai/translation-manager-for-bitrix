<?php

require_once dirname(__FILE__) . "/vendor/autoload.php";

if(!class_exists('\CIBlockElement')){
    include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");//Loader::includeModule('iblock');
}

if(!class_exists('\Smartcat\Connector\Agent\Task')){

    function smartcat_connector_autoload($className)
    {
        $sModuleId = basename(dirname(__FILE__));
        $className = ltrim($className, '\\');
        $arParts = explode('\\', $className);
        $isSmarcat = $arParts[0] == 'SmartCat';
        $sModuleCheck = strtolower($arParts[0] . '.' . $arParts[1]);
        if (!$isSmarcat && $sModuleCheck != $sModuleId)
            return;
        unset($arParts[0] , $arParts[1]);
        if (!empty($arParts) && !$isSmarcat) {
            $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arParts) . '.php';
            if (file_exists($fileName))
                require_once $fileName;
        }
    }
    spl_autoload_register('smartcat_connector_autoload');

}
