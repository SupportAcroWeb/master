<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

class AcrowebSearchPageComponent extends CBitrixComponent
{
    protected $obSearch;
    protected $obCache;

    public function onPrepareComponentParams($arParams)
    {
        $arParams["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ? $arParams["CACHE_TIME"] : 3600;
        $arParams["SHOW_WHEN"] = ($arParams["SHOW_WHEN"] ?? "N") === "Y";
        $arParams["SHOW_WHERE"] = ($arParams["SHOW_WHERE"] ?? "Y") !== "N";
        $arParams["arrWHERE"] = isset($arParams["arrWHERE"]) && is_array(
            $arParams["arrWHERE"]
        ) ? $arParams["arrWHERE"] : [];
        $arParams["PAGE_RESULT_COUNT"] = intval($arParams["PAGE_RESULT_COUNT"] ?? 50);
        $arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"] ?? '');
        $arParams["PAGER_SHOW_ALWAYS"] = ($arParams["PAGER_SHOW_ALWAYS"] ?? "Y") !== "N";
        $arParams["USE_TITLE_RANK"] = ($arParams["USE_TITLE_RANK"] ?? "N") === "Y";
        $arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"] ?? '');
        $arParams["DEFAULT_SORT"] = $arParams["DEFAULT_SORT"] === "date" ? "date" : "rank";
        $arParams["CHECK_DATES"] = $arParams["CHECK_DATES"] === "Y";

        return $arParams;
    }

    public function executeComponent()
    {
        parent::executeComponent();

        $this->setFrameMode(false);

        if (!Loader::includeModule("search")) {
            ShowError(Loc::getMessage("SEARCH_MODULE_UNAVAILABLE"));
            return;
        }

        CPageOption::SetOptionString("main", "nav_page_in_session", "N");

        // activation rating
        CRatingsComponentsMain::GetShowRating($this->arParams);

        $this->prepareData();
        $this->performSearch();

        $this->includeComponentTemplate();

        if (!empty($this->arResult['RETURN'])) {
            return $this->arResult['RETURN'];
        }
    }

    protected function prepareData()
    {
        $request = Context::getCurrent()->getRequest();

        $this->arResult["REQUEST"] = [
            "HOW" => $this->getHowParameter($request),
            "~FROM" => $this->getFromParameter($request),
            "FROM" => htmlspecialcharsbx($this->arResult["REQUEST"]["~FROM"]),
            "~TO" => $this->getToParameter($request),
            "TO" => htmlspecialcharsbx($this->arResult["REQUEST"]["~TO"]),
        ];

        $this->prepareQueryData($request);
        $this->prepareTagsData($request);

        $this->arResult["REQUEST"]["WHERE"] = htmlspecialcharsbx($request->get('where') ?? '');

        $this->prepareUrls();
    }

    protected function getHowParameter($request)
    {
        $how = trim($request->get('how') ?? '');
        if ($how == "d") {
            return "d";
        } elseif ($how == "r") {
            return "";
        } elseif ($this->arParams["DEFAULT_SORT"] == "date") {
            return "d";
        } else {
            return "";
        }
    }

    protected function getFromParameter($request)
    {
        $from = $request->get('from');
        return (is_string($from) && strlen($from) && CheckDateTime($from)) ? $from : "";
    }

    protected function getToParameter($request)
    {
        $to = $request->get('to');
        return (is_string($to) && strlen($to) && CheckDateTime($to)) ? $to : "";
    }

    protected function prepareQueryData($request)
    {
        $q = $request->get('q') ?: false;

        if ($q !== false) {
            if (($this->arParams["USE_LANGUAGE_GUESS"] ?? 'Y') === "N" || isset($_REQUEST["spell"])) {
                $this->arResult["REQUEST"]["~QUERY"] = $q;
                $this->arResult["REQUEST"]["QUERY"] = htmlspecialcharsex($q);
            } else {
                $arLang = CSearchLanguage::GuessLanguage($q);
                if (is_array($arLang) && $arLang["from"] != $arLang["to"]) {
                    $this->arResult["REQUEST"]["~ORIGINAL_QUERY"] = $q;
                    $this->arResult["REQUEST"]["ORIGINAL_QUERY"] = htmlspecialcharsex($q);
                    $this->arResult["REQUEST"]["~QUERY"] = CSearchLanguage::ConvertKeyboardLayout(
                        $this->arResult["REQUEST"]["~ORIGINAL_QUERY"],
                        $arLang["from"],
                        $arLang["to"]
                    );
                    $this->arResult["REQUEST"]["QUERY"] = htmlspecialcharsex($this->arResult["REQUEST"]["~QUERY"]);
                } else {
                    $this->arResult["REQUEST"]["~QUERY"] = $q;
                    $this->arResult["REQUEST"]["QUERY"] = htmlspecialcharsex($q);
                }
            }
        } else {
            $this->arResult["REQUEST"]["~QUERY"] = false;
            $this->arResult["REQUEST"]["QUERY"] = false;
        }
    }

    protected function prepareTagsData($request)
    {
        $tags = $request->get('tags') ?: false;

        if ($tags !== false) {
            $this->arResult["REQUEST"]["~TAGS_ARRAY"] = array_filter(
                array_map('trim', explode(",", $tags)),
                function ($tag) {
                    return $tag !== '';
                }
            );
            $this->arResult["REQUEST"]["TAGS_ARRAY"] = htmlspecialcharsex($this->arResult["REQUEST"]["~TAGS_ARRAY"]);
            $this->arResult["REQUEST"]["~TAGS"] = implode(",", $this->arResult["REQUEST"]["~TAGS_ARRAY"]);
            $this->arResult["REQUEST"]["TAGS"] = htmlspecialcharsex($this->arResult["REQUEST"]["~TAGS"]);
        } else {
            $this->arResult["REQUEST"]["~TAGS_ARRAY"] = [];
            $this->arResult["REQUEST"]["TAGS_ARRAY"] = [];
            $this->arResult["REQUEST"]["~TAGS"] = false;
            $this->arResult["REQUEST"]["TAGS"] = false;
        }
    }

    protected function prepareUrls()
    {
        global $APPLICATION;

        $this->arResult["URL"] = $APPLICATION->GetCurPage()
            . "?q=" . urlencode($this->arResult["REQUEST"]["~QUERY"])
            . (isset($_REQUEST["spell"]) ? "&amp;spell=1" : "")
            . "&amp;where=" . urlencode($this->arResult["REQUEST"]["WHERE"])
            . ($this->arResult["REQUEST"]["~TAGS"] !== false ? "&amp;tags=" . urlencode(
                    $this->arResult["REQUEST"]["~TAGS"]
                ) : "");

        if (isset($this->arResult["REQUEST"]["~ORIGINAL_QUERY"])) {
            $this->arResult["ORIGINAL_QUERY_URL"] = $APPLICATION->GetCurPage()
                . "?q=" . urlencode($this->arResult["REQUEST"]["~ORIGINAL_QUERY"])
                . "&amp;spell=1"
                . "&amp;where=" . urlencode($this->arResult["REQUEST"]["WHERE"])
                . ($this->arResult["REQUEST"]["HOW"] == "d" ? "&amp;how=d" : "")
                . ($this->arResult["REQUEST"]["FROM"] ? '&amp;from=' . urlencode(
                        $this->arResult["REQUEST"]["~FROM"]
                    ) : "")
                . ($this->arResult["REQUEST"]["TO"] ? '&amp;to=' . urlencode($this->arResult["REQUEST"]["~TO"]) : "")
                . ($this->arResult["REQUEST"]["~TAGS"] !== false ? "&amp;tags=" . urlencode(
                        $this->arResult["REQUEST"]["~TAGS"]
                    ) : "");
        }
    }

    protected function performSearch()
    {
        $arFilter = $this->getSearchFilter();
        $aSort = $this->getSearchSort();
        $exFILTER = CSearchParameters::ConvertParamsToFilter($this->arParams, "arrFILTER");

        $this->obSearch = new CSearch();

        $this->obSearch->SetOptions([
            "ERROR_ON_EMPTY_STEM" => $this->arParams["RESTART"] != "Y",
            "NO_WORD_LOGIC" => ($this->arParams["NO_WORD_LOGIC"] ?? "N") == "Y",
        ]);

        $this->obSearch->Search($arFilter, $aSort, $exFILTER);

        $this->arResult["ERROR_CODE"] = $this->obSearch->errorno;
        $this->arResult["ERROR_TEXT"] = $this->obSearch->error;

        $this->arResult["SEARCH"] = [];

        if ($this->obSearch->errorno == 0) {
            $this->obSearch->NavStart($this->arParams["PAGE_RESULT_COUNT"], false);
            $ar = $this->obSearch->GetNext();

            if (!$ar && ($this->arParams["RESTART"] == "Y") && $this->obSearch->Query->bStemming) {
                $exFILTER["STEMMING"] = false;
                $this->obSearch = new CSearch();
                $this->obSearch->Search($arFilter, $aSort, $exFILTER);

                $this->arResult["ERROR_CODE"] = $this->obSearch->errorno;
                $this->arResult["ERROR_TEXT"] = $this->obSearch->error;

                if ($this->obSearch->errorno == 0) {
                    $this->obSearch->NavStart($this->arParams["PAGE_RESULT_COUNT"], false);
                    $ar = $this->obSearch->GetNext();
                }
            }

            $this->arResult['RETURN'] = [];
            while ($ar) {
                $this->arResult['RETURN'][$ar["ID"]] = $ar["ITEM_ID"];
                $templatePath = $this->getTemplatePath();

                $ar["CHAIN_PATH"] = $GLOBALS["APPLICATION"]->GetNavChain(
                    $ar["URL"],
                    0,
                    $templatePath . "/chain_template.php",
                    true,
                    false
                );
                $ar["URL"] = htmlspecialcharsbx($ar["URL"]);
                $ar["TAGS"] = [];
                if (!empty($ar["~TAGS_FORMATED"])) {
                    foreach ($ar["~TAGS_FORMATED"] as $name => $tag) {
                        if ($this->arParams["TAGS_INHERIT"] == "Y") {
                            $arTags = $this->arResult["REQUEST"]["~TAGS_ARRAY"];
                            $arTags[$tag] = $tag;
                            $tags = implode(",", $arTags);
                        } else {
                            $tags = $tag;
                        }
                        $ar["TAGS"][] = [
                            "URL" => $GLOBALS["APPLICATION"]->GetCurPageParam("tags=" . urlencode($tags), ["tags"]),
                            "TAG_NAME" => htmlspecialcharsex($name),
                        ];
                    }
                }
                $this->arResult["SEARCH"][] = $ar;
                $ar = $this->obSearch->GetNext();
            }

            $navComponentObject = null;
            $this->arResult["NAV_STRING"] = $this->obSearch->GetPageNavStringEx(
                $navComponentObject,
                $this->arParams["PAGER_TITLE"],
                $this->arParams["PAGER_TEMPLATE"],
                $this->arParams["PAGER_SHOW_ALWAYS"]
            );
            $this->arResult["NAV_CACHED_DATA"] = null;
            $this->arResult["NAV_RESULT"] = $this->obSearch;
        }

        $this->prepareTagsChain();
    }

    protected function getTemplatePath()
    {
        $this->InitComponentTemplate();
        return $this->__template->__folder;
    }

    protected function getSearchFilter()
    {
        $arFilter = [
            "SITE_ID" => SITE_ID,
            "QUERY" => $this->arResult["REQUEST"]["~QUERY"],
            "TAGS" => $this->arResult["REQUEST"]["~TAGS"],
        ];

        if (!empty($this->arParams["IBLOCK_IDS"])) {
            $iblockIds = array_map('intval', $this->arParams["IBLOCK_IDS"]);
            if (!empty($iblockIds)) {
                $arFilter["MODULE_ID"] = "iblock";
                $arFilter["PARAM2"] = $iblockIds; // Передаем массив ID инфоблоков
            }
        } else {
            if ($this->arResult["REQUEST"]["WHERE"]) {
                [$module_id, $part_id] = array_pad(explode("_", $this->arResult["REQUEST"]["WHERE"], 2), 2, '');
                $arFilter["MODULE_ID"] = $module_id;
                if ($part_id) {
                    $arFilter["PARAM1"] = $part_id;
                }
            }
        }

        if ($this->arParams["CHECK_DATES"]) {
            $arFilter["CHECK_DATES"] = "Y";
        }
        if ($this->arResult["REQUEST"]["~FROM"]) {
            $arFilter[">=DATE_CHANGE"] = $this->arResult["REQUEST"]["~FROM"];
        }
        if ($this->arResult["REQUEST"]["~TO"]) {
            $arFilter["<=DATE_CHANGE"] = $this->arResult["REQUEST"]["~TO"];
        }

        return $arFilter;
    }

    protected function getSearchSort()
    {
        if ($this->arParams["USE_TITLE_RANK"]) {
            return $this->arResult["REQUEST"]["HOW"] == "d"
                ? ["DATE_CHANGE" => "DESC", "CUSTOM_RANK" => "DESC", "TITLE_RANK" => "DESC", "RANK" => "DESC"]
                : ["CUSTOM_RANK" => "DESC", "TITLE_RANK" => "DESC", "RANK" => "DESC", "DATE_CHANGE" => "DESC"];
        } else {
            return $this->arResult["REQUEST"]["HOW"] == "d"
                ? ["DATE_CHANGE" => "DESC", "CUSTOM_RANK" => "DESC", "RANK" => "DESC"]
                : ["CUSTOM_RANK" => "DESC", "RANK" => "DESC", "DATE_CHANGE" => "DESC"];
        }
    }

    protected function prepareTagsChain()
    {
        $this->arResult["TAGS_CHAIN"] = [];
        if ($this->arResult["REQUEST"]["~TAGS"]) {
            $res = array_unique($this->arResult["REQUEST"]["~TAGS_ARRAY"]);
            foreach ($res as $key => $tags) {
                $tagsCopy = $this->arResult["REQUEST"]["~TAGS_ARRAY"];
                unset($tagsCopy[$key]);
                $url_without = $GLOBALS["APPLICATION"]->GetCurPageParam(
                    "tags=" . urlencode(implode(",", $tagsCopy)),
                    ["tags"]
                );
                $this->arResult["TAGS_CHAIN"][] = [
                    "TAG_NAME" => htmlspecialcharsex($this->arResult["REQUEST"]["~TAGS_ARRAY"][$key]),
                    "TAG_URL" => $url_without,
                ];
            }
        }
    }
}