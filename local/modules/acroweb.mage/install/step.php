<?php

if (!check_bitrix_sessid()) {
    return;
}

use Bitrix\Main\Localization\Loc; 

Loc::loadLanguageFile(__FILE__);
 
?>

<style>
    .adm-info-message-wrap + .adm-info-message-wrap .adm-info-message {
        margin-top: 0 !important;
    }
</style>

<?= CAdminMessage::ShowNote(Loc::getMessage('ACROWEB_MODULE_INSTALL_OK')); ?>

<?= BeginNote('align="left"'); ?>

<?= Loc::getMessage('ACROWEB_MODULE_GO_MASTER') ?>

<?= EndNote(); ?>

<form action="/bitrix/admin/wizard_list.php?lang=<?= LANGUAGE_ID; ?>">
    <input type="submit" name="" value="<?= Loc::getMessage('ACROWEB_MODULE_BACK_IN_LIST') ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>">
</form>