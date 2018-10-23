<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?><?
IncludeModuleLangFile(__FILE__);

$APPLICATION->SetAdditionalCSS('/bitrix/css/abbyy.cloud/abbyy.cloud.css');

if ($APPLICATION->GetGroupRight("abbyy.cloud") != "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "abbyy_cloud",
        "sort" => 200,
        "text" => GetMessage("ABBYY_CLOUD_ALS"),
        "title" => GetMessage("ABBYY_CLOUD_ALS"),
        "icon" => "abbyy_cloud_menu_icon",
        "page_icon" => "subscribe_page_icon",
        "items_id" => "menu_abbyy_cloud",

        "items" => Array(
            Array(
                "url" => "abbyy.cloud_profiles.php?lang=" . LANGUAGE_ID,
                "text" => GetMessage("ABBYY_CLOUD_PROFILI_PEREVODA"),
                "title" => GetMessage("ABBYY_CLOUD_PROFILI_PEREVODA"),
                "more_url" => Array(
                    'abbyy.cloud_profile.php',
                ),
            ),
            Array(
                "url" => "abbyy.cloud_tasks.php?lang=" . LANGUAGE_ID,
                "text" => GetMessage("ABBYY_CLOUD_ZAKAZY"),
                "title" => GetMessage("ABBYY_CLOUD_ZAKAZY"),
                "more_url" => Array(
                    'abbyy.cloud_content.php',
                ),
            ),
        )
    );

    return $aMenu;
}
return false;
?>