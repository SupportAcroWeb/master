<?php
//require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\ModuleManager;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!defined('WIZARD_SITE_ID') || !defined('WIZARD_SITE_DIR')) {
    return;
}

$modules = [
    'dev2fun.imagecompress',
    'thebrainstech.copyiblock',
    'webdebug.utilities',
    'wsrubi.smtp'
];

$sourcePath = Application::getDocumentRoot() . '/local/modules/acroweb.mage/install/wizards/acroweb/UniBrix/site/services/modules/';
$destinationPath = Application::getDocumentRoot() . '/bitrix/modules/';

foreach ($modules as $module) {
    $sourceDir = new Directory($sourcePath . $module);
    $destDir = new Directory($destinationPath . $module);

    if ($sourceDir->isExists()) {
        if (!$destDir->isExists()) {
            CopyDirFiles(
                $sourceDir->getPath(),
                $destDir->getPath(),
                $rewrite = true,
                $recursive = true,
                $delete_after_copy = false
            );
        }

        if (!ModuleManager::isModuleInstalled($module)) {
            ModuleManager::registerModule($module);
        }
    }
}

// Очистка кеша
Application::getInstance()->getTaggedCache()->clearByTag('bitrix:modules');

return true;
