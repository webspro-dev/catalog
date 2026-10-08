<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class websbro_democatalog extends CModule
{
    public $MODULE_ID = 'websbro.democatalog';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME = 'WebsBro';
    public $PARTNER_URI = 'https://github.com/webspro-dev/bitrix-catalog-module';

    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('WEBSBRO_DEMOCATALOG_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('WEBSBRO_DEMOCATALOG_MODULE_DESCRIPTION');
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if (!\Bitrix\Main\Loader::includeModule('main')) {
            $APPLICATION->ThrowException('Не удалось загрузить модуль main.');
            return false;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallDB();

        $this->InstallFiles();

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('WEBSBRO_DEMOCATALOG_INSTALL_TITLE'),
            __DIR__ . '/step.php'
        );

        return true;
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallDB();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('WEBSBRO_DEMOCATALOG_UNINSTALL_TITLE'),
            __DIR__ . '/unstep.php'
        );

        return true;
    }

    public function InstallDB()
    {
        global $DB;

        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();

        $tableName = 'websbro_democatalog_product';

        if (!$connection->isTableExists($tableName)) {
            $connection->queryExecute(
                'CREATE TABLE ' . $sqlHelper->quoteIdentifier($tableName) . ' (
                    ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    NAME VARCHAR(255) NOT NULL,
                    CODE VARCHAR(255) NULL,
                    PRICE DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    ACTIVE CHAR(1) NOT NULL DEFAULT \'Y\',
                    DATE_CREATE DATETIME NOT NULL,
                    DATE_UPDATE DATETIME NOT NULL,
                    PRIMARY KEY (ID),
                    KEY IX_ACTIVE (ACTIVE),
                    KEY IX_CODE (CODE)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );
        }

        return true;
    }

    public function UnInstallDB()
    {
        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $tableName = 'websbro_democatalog_product';

        if ($connection->isTableExists($tableName)) {
            $connection->queryExecute(
                'DROP TABLE ' . $sqlHelper->quoteIdentifier($tableName)
            );
        }

        return true;
    }

    public function InstallFiles()
    {
        return true;
    }

    public function UnInstallFiles()
    {
        return true;
    }
}
