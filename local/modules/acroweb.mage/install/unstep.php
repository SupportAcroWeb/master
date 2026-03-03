<?php
if (!check_bitrix_sessid()) {
    return;
} ?>
<?= CAdminMessage::ShowNote('Удаление прошло успешно'); ?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="submit" name="" value="Вернуться обратно">
    <form>