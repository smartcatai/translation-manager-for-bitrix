<?php

class smartcat_connector extends CModule
{
    const MODULE_ID = 'smartcat.connector';

    public $MODULE_ID = self::MODULE_ID;

    public $PARTNER_NAME = 'Smartcat Platform Inc.';
    public $PARTNER_URI = 'https://smartcat.ai/';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $DIR;

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';

        $this->MODULE_NAME = GetMessage("SMARTCAT_CONNECTOR_KONNEKTOR");
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        if (stripos(__FILE__, $_SERVER['DOCUMENT_ROOT'] . '/local/modules') !== false) {
            $this->DIR = 'local';
        } else {
            $this->DIR = 'bitrix';
        }
    }

    function InstallFiles($arParams = array())
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php')
                        continue;
                    file_put_contents(
                        $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item,
                        '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>'
                    );
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/tools')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php')
                        continue;
                    file_put_contents(
                        $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '_' . $item,
                        '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/tools/' . $item . '");?' . '>'
                    );
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/components')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $item, $ReWrite = True, $Recursive = True);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/images')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/' . $this->MODULE_ID . '/' . $item, $ReWrite = True, $Recursive = True);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/js')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . $this->MODULE_ID . '/' . $item, $ReWrite = True, $Recursive = True);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/css')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/' . $this->MODULE_ID . '/' . $item, $ReWrite = True, $Recursive = True);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function UnInstallFiles()
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/tools')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/components')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p . '/' . $item))
                        continue;

                    $dir0 = opendir($p0);
                    while (false !== $item0 = readdir($dir0)) {
                        if ($item0 == '..' || $item0 == '.')
                            continue;
                        DeleteDirFilesEx('/bitrix/components/' . $item . '/' . $item0);
                    }
                    closedir($dir0);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/images')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p . '/' . $item))
                        continue;

                    $dir0 = opendir($p0);
                    while (false !== $item0 = readdir($dir0)) {
                        if ($item0 == '..' || $item0 == '.')
                            continue;
                        DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/' . $item . '/' . $item0);
                    }
                    closedir($dir0);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/js')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p . '/' . $item))
                        continue;

                    $dir0 = opendir($p0);
                    while (false !== $item0 = readdir($dir0)) {
                        if ($item0 == '..' || $item0 == '.')
                            continue;
                        DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/' . $item . '/' . $item0);
                    }
                    closedir($dir0);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . $this->MODULE_ID . '/install/css')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p . '/' . $item))
                        continue;

                    $dir0 = opendir($p0);
                    while (false !== $item0 = readdir($dir0)) {
                        if ($item0 == '..' || $item0 == '.')
                            continue;
                        DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/' . $item . '/' . $item0);
                    }
                    closedir($dir0);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function InstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . self::MODULE_ID . '/install/db/' . $DBType . '/install.sql');

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }
        \Bitrix\Main\Config\Option::get($this->MODULE_ID, 'schema_version', '1.0.0');
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        if (!array_key_exists("save_tables", $arParams) || ($arParams["save_tables"] != "Y")) {
            $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/' . $this->DIR . '/modules/' . self::MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
        }

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }

        return true;
    }

    public function UpgradeDB()
    {
        \Bitrix\Main\Loader::includeModule(self::MODULE_ID);
        $schema = new \Smartcat\Connector\Schema($_SERVER["DOCUMENT_ROOT"] . "/" . $this->DIR . "/modules/" . self::MODULE_ID . "/install/db/mysql");

        if ($schema->needUpgrade()) {
            $schema->upgrade();
        }

    }

    public function InstallEvents()
    {
        $obEventManager = \Bitrix\Main\EventManager::getInstance();
        $obEventManager->registerEventHandler('iblock', 'OnBeforeIBlockElementAdd', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnBeforeIBlockElementAdd');
        $obEventManager->registerEventHandler('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAfterIBlockElementAdd');
        $obEventManager->registerEventHandler('iblock', 'OnBeforeIBlockElementUpdate', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnBeforeIBlockElementUpdate');
        $obEventManager->registerEventHandler('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAfterIBlockElementUpdate');
        $obEventManager->registerEventHandler('iblock', 'OnAfterIBlockElementDelete', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAfterIBlockElementDelete');

        $obEventManager->registerEventHandler('main', 'OnAdminListDisplay', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAdminListDisplayHandler');
        $obEventManager->registerEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnBeforePrologHandler');

        return true;
    }

    public function UnInstallEvents()
    {
        $obEventManager = \Bitrix\Main\EventManager::getInstance();
        $obEventManager->unRegisterEventHandler('iblock', 'OnBeforeIBlockElementAdd', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnBeforeIBlockElementAdd');
        $obEventManager->unRegisterEventHandler('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAfterIBlockElementAdd');
        $obEventManager->unRegisterEventHandler('iblock', 'OnBeforeIBlockElementUpdate', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnBeforeIBlockElementUpdate');
        $obEventManager->unRegisterEventHandler('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAfterIBlockElementUpdate');
        $obEventManager->unRegisterEventHandler('iblock', 'OnAfterIBlockElementDelete', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAfterIBlockElementDelete');

        $obEventManager->unRegisterEventHandler('main', 'OnAdminListDisplay', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnAdminListDisplayHandler');
        $obEventManager->unRegisterEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID,
            '\Smartcat\Connector\Events\Iblock', 'OnBeforePrologHandler');

        return true;
    }

    function InstallAgents()
    {
        $cAgent = new CAgent;
        $res = $cAgent->AddAgent(
            "\\Smartcat\\Connector\\Agent\\Task::Check();",
            $this->MODULE_ID,
            "Y",
            60
        );
    }

    function UnInstallAgents()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallAgents();
        $this->InstallFiles();
        $this->InstallDB();
        $this->UpgradeDB();
        $this->InstallEvents();

    }

    public function DoUninstall()
    {

        global $APPLICATION, $step;

        $step = IntVal($step);

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage("SMARTCAT_CONNECTOR_UDALENIE_MODULA"), $_SERVER["DOCUMENT_ROOT"] . "/" . $this->DIR . "/modules/" . self::MODULE_ID . "/install/unstep.php");
        } else {
            $res = $this->UnInstallDB(array(
                "save_tables" => $_REQUEST["save_tables"],
            ));

            $this->UnInstallAgents();
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            UnRegisterModule($this->MODULE_ID);
        }

    }
}