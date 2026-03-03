<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

if (class_exists('acroweb_mage')) {
    return;
}

Loc::loadLanguageFile(__FILE__);

class acroweb_mage extends CModule
{
    public $MODULE_ID = 'acroweb.mage';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    private $errors;

    public function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->PARTNER_NAME = Loc::getMessage('ACROWEB_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('ACROWEB_MODULE_PARTNER_URL');
        $this->MODULE_NAME = Loc::getMessage('ACROWEB_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ACROWEB_MODULE_DESC');
    }

    public function DoInstall($auto = false)
    {
        global $APPLICATION;

        if (!$this->InstallDB()) {
            return false;
        }

        if (!$this->InstallFiles()) {
            $this->UnInstallDB();
            return false;
        }

        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallEvents(); 

        if ($auto) {
            return true;
        }

        $bLocal = $this->isLocalDir();
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('ACROWEB_MODULE_INSTALL'),
            Application::getDocumentRoot(
            ) . '/' . ($bLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/install/step.php'
        );

        return true;
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $bLocal = $this->isLocalDir();
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('ACROWEB_MODULE_UNINSTALL'),
            Application::getDocumentRoot(
            ) . '/' . ($bLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/install/unstep.php'
        );
    }

    public function InstallDB()
    {
        return true;
    }

    public function UnInstallDB()
    {
        return true;
    }

    public function InstallFiles($arParams = [])
    {
        CopyDirFiles(
            __DIR__ . '/admin/',
            Application::getDocumentRoot() . '/bitrix/admin/',
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . '/js/acroweb_mage',
            Application::getDocumentRoot() . '/bitrix/js/acroweb_mage',
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . '/components/',
            Application::getDocumentRoot() . '/local/components',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/wizards/acroweb/UniBrix/',
            Application::getDocumentRoot() . '/bitrix/wizards/acroweb/UniBrix/',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/wizards/acroweb/UniBrix/site/templates',
            Application::getDocumentRoot() . '/local/templates',
            true,
            true,
        );

        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(
            __DIR__ . '/admin/',
            Application::getDocumentRoot() . '/bitrix/admin/'
        );
        DeleteDirFilesEx('/bitrix/js/acroweb_mage');

        DeleteDirFilesEx('/local/templates/UniBrix/');

        DeleteDirFilesEx('/bitrix/wizards/acroweb/UniBrix/');

        $this->UnInstallComponents();

        return true;
    }

    public function UnInstallComponents()
    {
        DeleteDirFilesEx('/local/components/acroweb/news.year.filter/');
        DeleteDirFilesEx('/local/components/acroweb/search.page/');
        DeleteDirFilesEx('/local/components/acroweb/sender.subscribe/');
        DeleteDirFilesEx('/local/components/acroweb/universal.form/');
        DeleteDirFilesEx('/local/components/acroweb/universal.iblock.form/');
        DeleteDirFilesEx('/local/components/acroweb/widgets/');

        return true;
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            \Acroweb\Mage\Helpers\TemplateHelper::class,
            'onEndBufferContent'
        );
        $eventManager->registerEventHandler(
            'main',
            'OnEpilog',
            $this->MODULE_ID,
            \Acroweb\Mage\Helpers\TemplateHelper::class,
            'page404'
        );
        $eventManager->registerEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            \Acroweb\Mage\Helpers\AdminMenuHelper::class,
            'onBuildGlobalMenu'
        );
        $eventManager->registerEventHandler(
            'iblock',
            'OnAfterIBlockUpdate',
            $this->MODULE_ID,
            \Acroweb\Mage\Config::class,
            'onAfterIBlockUpdate'
        );
        return true;
    }

    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            \Acroweb\Mage\Helpers\TemplateHelper::class,
            'onEndBufferContent'
        );
        $eventManager->unRegisterEventHandler(
            'main',
            'OnEpilog',
            $this->MODULE_ID,
            \Acroweb\Mage\Helpers\TemplateHelper::class,
            'page404'
        );
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            \Acroweb\Mage\Helpers\AdminMenuHelper::class,
            'onBuildGlobalMenu'
        );
        $eventManager->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockUpdate',
            $this->MODULE_ID,
            \Acroweb\Mage\Config::class,
            'onAfterIBlockUpdate'
        );
        return true;
    }

    public function isLocalDir()
    {
        return preg_match("#/local/#", str_replace('\\', '/', __FILE__));
    }

    public function GetModuleRightList()
    {
        return [
            'reference_id' => ['D', 'R', 'W'],
            'reference' => [
                '[D] ' . Loc::getMessage('ACROWEB_MODULE_RIGHTS_D_TITLE'),
                '[R] ' . Loc::getMessage('ACROWEB_MODULE_RIGHTS_R_TITLE'),
                '[W] ' . Loc::getMessage('ACROWEB_MODULE_RIGHTS_W_TITLE'),
            ],
        ];
    }
}