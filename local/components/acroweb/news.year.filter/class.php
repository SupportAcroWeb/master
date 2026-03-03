<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Iblock\SectionElementTable;
use Bitrix\Main\Context;

class NewsYearFilterComponent extends CBitrixComponent
{
    protected $filterName = "newsYearFilter";

    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_ID'] = (int)$arParams['IBLOCK_ID'];
        $arParams['SECTION_ID'] = (int)$arParams['SECTION_ID'];
        $arParams['FILTER_NAME'] = $this->filterName;
        return $arParams;
    }

    public function executeComponent()
    {
        if (!Loader::includeModule('iblock')) {
            ShowError(Loc::getMessage('NEWS_YEAR_FILTER_MODULE_NOT_INSTALLED'));
            return;
        }

        $this->arResult = [
            'YEARS' => $this->getYears(),
            'CURRENT_YEAR' => $this->getCurrentYear(),
        ];

        $this->applyFilter();

        $this->includeComponentTemplate();
    }

    protected function getYears()
    {
        $years = [];

        $query = ElementTable::query();
        $query->setSelect(['ACTIVE_FROM']);
        $filter = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y',
            '!ACTIVE_FROM' => false,
        ];

        if ($this->arParams['SECTION_ID'] > 0) {
            $query->registerRuntimeField('IBLOCK_SECTION_ELEMENT',
                new ReferenceField(
                    'IBLOCK_SECTION_ELEMENT',
                    SectionElementTable::getEntity(),
                    ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID'],
                    ['join_type' => 'INNER']
                )
            );
            $filter['IBLOCK_SECTION_ELEMENT.IBLOCK_SECTION_ID'] = $this->arParams['SECTION_ID'];
        }

        $query->setFilter($filter);
        $query->setOrder(['ACTIVE_FROM' => 'DESC']);

        $result = $query->exec();

        while ($row = $result->fetch()) {
            $year = date('Y', strtotime($row['ACTIVE_FROM']));
            if (!isset($years[$year])) {
                $years[$year] = $year;
            }
        }

        return $years;
    }

    protected function getCurrentYear()
    {
        $request = Context::getCurrent()->getRequest();
        return $request->get("FILTER_YEAR") ?: "all";
    }

    protected function applyFilter()
    {
        global ${$this->filterName};
        ${$this->filterName} = [];

        $currentYear = $this->getCurrentYear();
        if ($currentYear !== "all") {
            $year = (int)$currentYear;
            ${$this->filterName} = [
                ">DATE_ACTIVE_FROM" => ConvertTimeStamp(mktime(0, 0, 0, 1, 1, $year), "FULL"),
                "<DATE_ACTIVE_FROM" => ConvertTimeStamp(mktime(23, 59, 59, 12, 31, $year), "FULL")
            ];
        }
 
        if ($this->arParams['SECTION_ID'] > 0) {
            ${$this->filterName}['SECTION_ID'] = $this->arParams['SECTION_ID'];
        }
    }
}