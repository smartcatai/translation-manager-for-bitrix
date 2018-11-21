<?php

require_once dirname(__FILE__) . "/vendor/autoload.php";

if(!class_exists('\CIBlockElement')){
    include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");//Loader::includeModule('iblock');
}