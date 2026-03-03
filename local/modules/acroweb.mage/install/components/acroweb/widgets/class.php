<?php
class AcrowebWidgetsComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams): array {
        return $arParams;
    }

    public function executeComponent(): void {
        $this->includeComponentTemplate();
    }
}