<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use    Bitrix\Sale\BusinessValue;
use Bitrix\Sale\OrderStatus;
use Bitrix\Sale\DeliveryStatus;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Bitrix\Catalog;
use Bitrix\Sale;
use Bitrix\Main\Diag\Debug;

if (!Loader::includeModule('sale')) {
    return;
}

$saleConverted15 = COption::GetOptionString("main", "~sale_converted_15", "") == "Y";
if ($saleConverted15) {
    $BIZVAL_INDIVIDUAL_DOMAIN = BusinessValue::INDIVIDUAL_DOMAIN;
    $BIZVAL_ENTITY_DOMAIN = BusinessValue::ENTITY_DOMAIN;
} else {
    $BIZVAL_INDIVIDUAL_DOMAIN = null;
    $BIZVAL_ENTITY_DOMAIN = null;
}

if (COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS") == "") {
    COption::SetOptionString("catalog", "1C_GROUP_PERMISSIONS", "1", GetMessage('SALE_1C_GROUP_PERMISSIONS'));
}

$arGeneralInfo = [];

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if ($arSite = $dbSite->Fetch()) {
    $lang = $arSite["LANGUAGE_ID"];
}
if ($lang == '') {
    $lang = "ru";
}
$bRus = false;
if ($lang == "ru") {
    $bRus = true;
}

$shopLocalization = $wizard->GetVar("shopLocalization");

COption::SetOptionString("acroweb_unibrix", "shopLocalization", $shopLocalization, "ru", WIZARD_SITE_ID);
if ($shopLocalization == "kz") {
    $shopLocalization = "ru";
}

$defCurrency = "EUR";
if ($lang == "ru") {
    if ($shopLocalization == "ua") {
        $defCurrency = "UAH";
    } elseif ($shopLocalization == "bl") {
        $defCurrency = "BYR";
    } else {
        $defCurrency = "RUB";
    }
} elseif ($lang == "en") {
    $defCurrency = "USD";
}

$arLanguages = [];
$rsLanguage = CLanguage::GetList();
while ($arLanguage = $rsLanguage->Fetch()) {
    $arLanguages[] = $arLanguage["LID"];
}

WizardServices::IncludeServiceLang("step1.php", $lang);

if ($bRus || COption::GetOptionString(
        "acroweb_unibrix",
        "wizard_installed",
        "N",
        WIZARD_SITE_ID
    ) != "Y" || WIZARD_INSTALL_DEMO_DATA) {
    $personType = $wizard->GetVar("personType");
    $paysystem = $wizard->GetVar("paysystem");

    if ($shopLocalization == "ru") {
        if (CSaleLang::GetByID(WIZARD_SITE_ID)) {
            CSaleLang::Update(WIZARD_SITE_ID, ["LID" => WIZARD_SITE_ID, "CURRENCY" => "RUB"]);
        } else {
            CSaleLang::Add(["LID" => WIZARD_SITE_ID, "CURRENCY" => "RUB"]);
        }

        $shopLocation = $wizard->GetVar("shopLocation");
        COption::SetOptionString("acroweb_unibrix", "shopLocation", $shopLocation, false, WIZARD_SITE_ID);
        $shopOfName = $wizard->GetVar("shopOfName");
        COption::SetOptionString("acroweb_unibrix", "shopOfName", $shopOfName, false, WIZARD_SITE_ID);
        $shopAdr = $wizard->GetVar("shopAdr");
        COption::SetOptionString("acroweb_unibrix", "shopAdr", $shopAdr, false, WIZARD_SITE_ID);

        $shopINN = $wizard->GetVar("shopINN");
        COption::SetOptionString("acroweb_unibrix", "shopINN", $shopINN, false, WIZARD_SITE_ID);
        $shopKPP = $wizard->GetVar("shopKPP");
        COption::SetOptionString("acroweb_unibrix", "shopKPP", $shopKPP, false, WIZARD_SITE_ID);
        $shopNS = $wizard->GetVar("shopNS");
        COption::SetOptionString("acroweb_unibrix", "shopNS", $shopNS, false, WIZARD_SITE_ID);
        $shopBANK = $wizard->GetVar("shopBANK");
        COption::SetOptionString("acroweb_unibrix", "shopBANK", $shopBANK, false, WIZARD_SITE_ID);
        $shopBANKREKV = $wizard->GetVar("shopBANKREKV");
        COption::SetOptionString("acroweb_unibrix", "shopBANKREKV", $shopBANKREKV, false, WIZARD_SITE_ID);
        $shopKS = $wizard->GetVar("shopKS");
        COption::SetOptionString("acroweb_unibrix", "shopKS", $shopKS, false, WIZARD_SITE_ID);
        $siteStamp = $wizard->GetVar("siteStamp");
        if ($siteStamp == "") {
            $siteStamp = COption::GetOptionString("acroweb_unibrix", "siteStamp", "", WIZARD_SITE_ID);
        }
    } elseif ($shopLocalization == "ua") {
        if (CSaleLang::GetByID(WIZARD_SITE_ID)) {
            CSaleLang::Update(WIZARD_SITE_ID, ["LID" => WIZARD_SITE_ID, "CURRENCY" => "UAH"]);
        } else {
            CSaleLang::Add(["LID" => WIZARD_SITE_ID, "CURRENCY" => "UAH"]);
        }

        $shopLocation = $wizard->GetVar("shopLocation_ua");
        COption::SetOptionString("acroweb_unibrix", "shopLocation_ua", $shopLocation, false, WIZARD_SITE_ID);
        $shopOfName = $wizard->GetVar("shopOfName_ua");
        COption::SetOptionString("acroweb_unibrix", "shopOfName_ua", $shopOfName, false, WIZARD_SITE_ID);
        $shopAdr = $wizard->GetVar("shopAdr_ua");
        COption::SetOptionString("acroweb_unibrix", "shopAdr_ua", $shopAdr, false, WIZARD_SITE_ID);

        $shopEGRPU_ua = $wizard->GetVar("shopEGRPU_ua");
        COption::SetOptionString("acroweb_unibrix", "shopEGRPU_ua", $shopEGRPU_ua, false, WIZARD_SITE_ID);
        $shopINN_ua = $wizard->GetVar("shopINN_ua");
        COption::SetOptionString("acroweb_unibrix", "shopINN_ua", $shopINN_ua, false, WIZARD_SITE_ID);
        $shopNDS_ua = $wizard->GetVar("shopNDS_ua");
        COption::SetOptionString("acroweb_unibrix", "shopNDS_ua", $shopNDS_ua, false, WIZARD_SITE_ID);
        $shopNS_ua = $wizard->GetVar("shopNS_ua");
        COption::SetOptionString("acroweb_unibrix", "shopNS_ua", $shopNS_ua, false, WIZARD_SITE_ID);
        $shopBank_ua = $wizard->GetVar("shopBank_ua");
        COption::SetOptionString("acroweb_unibrix", "shopBank_ua", $shopBank_ua, false, WIZARD_SITE_ID);
        $shopMFO_ua = $wizard->GetVar("shopMFO_ua");
        COption::SetOptionString("acroweb_unibrix", "shopMFO_ua", $shopMFO_ua, false, WIZARD_SITE_ID);
        $shopPlace_ua = $wizard->GetVar("shopPlace_ua");
        COption::SetOptionString("acroweb_unibrix", "shopPlace_ua", $shopPlace_ua, false, WIZARD_SITE_ID);
        $shopFIO_ua = $wizard->GetVar("shopFIO_ua");
        COption::SetOptionString("acroweb_unibrix", "shopFIO_ua", $shopFIO_ua, false, WIZARD_SITE_ID);
        $shopTax_ua = $wizard->GetVar("shopTax_ua");
        COption::SetOptionString("acroweb_unibrix", "shopTax_ua", $shopTax_ua, false, WIZARD_SITE_ID);
    }

    $siteTelephone = $wizard->GetVar("siteTelephone");
    COption::SetOptionString("acroweb_unibrix", "siteTelephone", $siteTelephone, false, WIZARD_SITE_ID);
    $shopEmail = $wizard->GetVar("shopEmail");
    COption::SetOptionString("acroweb_unibrix", "shopEmail", $shopEmail, false, WIZARD_SITE_ID);
    $siteName = $wizard->GetVar("siteName");
    COption::SetOptionString("acroweb_unibrix", "siteName", $siteName, false, WIZARD_SITE_ID);

    $obSite = new CSite;
    $obSite->Update(WIZARD_SITE_ID, [
        "EMAIL" => $shopEmail,
        "SITE_NAME" => $siteName,
        "SERVER_NAME" => $_SERVER["SERVER_NAME"],
    ]);

    if ($siteStamp <> '') {
        if (intval($siteStamp) > 0) {
            $ff = CFile::GetByID($siteStamp);
            if ($zr = $ff->Fetch()) {
                $strOldFile = str_replace(
                    "//",
                    "/",
                    WIZARD_SITE_ROOT_PATH . "/" . (COption::GetOptionString(
                        "main",
                        "upload_dir",
                        "upload"
                    )) . "/" . $zr["SUBDIR"] . "/" . $zr["FILE_NAME"]
                );
                @copy($strOldFile, WIZARD_SITE_PATH . "include/stamp.gif");
                CFile::Delete($zr["ID"]);
                $siteStamp = WIZARD_SITE_DIR . "include/stamp.gif";
                COption::SetOptionString("acroweb_unibrix", "siteStamp", $siteStamp, false, WIZARD_SITE_ID);
            }
        }
    } else {
        $siteStamp = "/local/templates/" . WIZARD_TEMPLATE_ID . "_" . WIZARD_THEME_ID . "/images/pechat.gif";
    }

    $arPersonTypeNames = [];
    $dbPerson = CSalePersonType::GetList([], ["LID" => WIZARD_SITE_ID]);
    //if(!$dbPerson->Fetch())//if there are no data in module
    //{
    while ($arPerson = $dbPerson->Fetch()) {
        $arPersonTypeNames[$arPerson["ID"]] = $arPerson["NAME"];
    }
    //Person Types
    if (!$bRus) {
        $personType["fiz"] = "Y";
        $personType["ur"] = "N";
    }

    $fizExist = in_array(GetMessage("SALE_WIZARD_PERSON_1"), $arPersonTypeNames);
    $urExist = in_array(GetMessage("SALE_WIZARD_PERSON_2"), $arPersonTypeNames);
    $fizUaExist = in_array(GetMessage("SALE_WIZARD_PERSON_3"), $arPersonTypeNames);

    $personTypeFiz = (isset($personType["fiz"]) && $personType["fiz"] == "Y" ? "Y" : "N");
    COption::SetOptionString("acroweb_unibrix", "personTypeFiz", $personTypeFiz, false, WIZARD_SITE_ID);
    $personTypeUr = (isset($personType["ur"]) && $personType["ur"] == "Y" ? "Y" : "N");
    COption::SetOptionString("acroweb_unibrix", "personTypeUr", $personTypeUr, false, WIZARD_SITE_ID);

    if (!defined("ADDITIONAL_INSTALL") && in_array(GetMessage("SALE_WIZARD_PERSON_1"), $arPersonTypeNames)) {
        $arGeneralInfo["personType"]["fiz"] = array_search(GetMessage("SALE_WIZARD_PERSON_1"), $arPersonTypeNames);
        CSalePersonType::Update(array_search(GetMessage("SALE_WIZARD_PERSON_1"), $arPersonTypeNames), [
                "ACTIVE" => $personTypeFiz,
            ]
        );
    } elseif ($personTypeFiz == "Y") {
        $arGeneralInfo["personType"]["fiz"] = CSalePersonType::Add([
                "LID" => WIZARD_SITE_ID,
                "NAME" => GetMessage("SALE_WIZARD_PERSON_1"),
                "SORT" => "100",
            ]
        );
    }

    if (!defined("ADDITIONAL_INSTALL") && in_array(GetMessage("SALE_WIZARD_PERSON_2"), $arPersonTypeNames)) {
        $arGeneralInfo["personType"]["ur"] = array_search(GetMessage("SALE_WIZARD_PERSON_2"), $arPersonTypeNames);
        CSalePersonType::Update(array_search(GetMessage("SALE_WIZARD_PERSON_2"), $arPersonTypeNames), [
                "ACTIVE" => $personTypeUr,
            ]
        );
    } elseif ($personTypeUr == "Y") {
        $arGeneralInfo["personType"]["ur"] = CSalePersonType::Add([
                "LID" => WIZARD_SITE_ID,
                "NAME" => GetMessage("SALE_WIZARD_PERSON_2"),
                "SORT" => "150",
            ]
        );
    }

    if ($shopLocalization == "ua") {
        $personTypeFizUa = (isset($personType["fiz_ua"]) && $personType["fiz_ua"] == "Y" ? "Y" : "N");
        COption::SetOptionString("acroweb_unibrix", "personTypeFizUa", $personTypeFizUa, false, WIZARD_SITE_ID);

        if (!defined("ADDITIONAL_INSTALL") && in_array(GetMessage("SALE_WIZARD_PERSON_3"), $arPersonTypeNames)) {
            $arGeneralInfo["personType"]["fiz_ua"] = array_search(
                GetMessage("SALE_WIZARD_PERSON_3"),
                $arPersonTypeNames
            );
            CSalePersonType::Update(array_search(GetMessage("SALE_WIZARD_PERSON_3"), $arPersonTypeNames), [
                    "ACTIVE" => $personTypeFizUa,
                ]
            );
        } elseif ($personTypeFizUa == "Y") {
            $arGeneralInfo["personType"]["fiz_ua"] = CSalePersonType::Add([
                    "LID" => WIZARD_SITE_ID,
                    "NAME" => GetMessage("SALE_WIZARD_PERSON_3"),
                    "SORT" => "100",
                ]
            );
        }
    }

    if (COption::GetOptionString(
            "acroweb_unibrix",
            "wizard_installed",
            "N",
            WIZARD_SITE_ID
        ) != "Y" || WIZARD_INSTALL_DEMO_DATA) {
        $dbCurrency = Bitrix\Sale\Internals\SiteCurrencyTable::getList([
            "filter" => [
                "LID" => WIZARD_SITE_ID,
            ],
        ]);
        if ($curCurrency = $dbCurrency->fetch()) {
            if ($curCurrency["CURRENCY"] != $defCurrency) {
                Bitrix\Sale\Internals\SiteCurrencyTable::update(WIZARD_SITE_ID, [
                    "CURRENCY" => $defCurrency,
                ]);
            }
        } else {
            Bitrix\Sale\Internals\SiteCurrencyTable::add([
                "LID" => WIZARD_SITE_ID,
                "CURRENCY" => $defCurrency,
            ]);
        }

        if (!defined("ADDITIONAL_INSTALL")) {
            //Set options
            COption::SetOptionString('sale', 'default_currency', $defCurrency);
            COption::SetOptionString('sale', 'delete_after', '30');
            COption::SetOptionString('sale', 'order_list_date', '30');
            COption::SetOptionString('sale', 'MAX_LOCK_TIME', '30');
            COption::SetOptionString('sale', 'GRAPH_WEIGHT', '600');
            COption::SetOptionString('sale', 'GRAPH_HEIGHT', '600');
            COption::SetOptionString('sale', 'path2user_ps_files', '/bitrix/php_interface/include/sale_payment/');
            COption::SetOptionString('sale', 'lock_catalog', 'Y');
            COption::SetOptionString(
                'sale',
                'order_list_fields',
                'ID,USER,PAY_SYSTEM,PRICE,STATUS,PAYED,PS_STATUS,CANCELED,BASKET'
            );
            COption::SetOptionString('sale', 'GROUP_DEFAULT_RIGHT', 'D');
            COption::SetOptionString('sale', 'affiliate_param_name', 'partner');
            COption::SetOptionString('sale', 'show_order_sum', 'N');
            COption::SetOptionString('sale', 'show_order_product_xml_id', 'N');
            COption::SetOptionString('sale', 'show_paysystem_action_id', 'N');
            COption::SetOptionString('sale', 'affiliate_plan_type', 'N');
            if ($bRus) {
                COption::SetOptionString('sale', '1C_SALE_SITE_LIST', WIZARD_SITE_ID);
                COption::SetOptionString('sale', '1C_EXPORT_PAYED_ORDERS', 'N');
                COption::SetOptionString('sale', '1C_EXPORT_ALLOW_DELIVERY_ORDERS', 'N');
                COption::SetOptionString('sale', '1C_EXPORT_FINAL_ORDERS', '');
                COption::SetOptionString('sale', '1C_FINAL_STATUS_ON_DELIVERY', 'F');
                COption::SetOptionString('sale', '1C_REPLACE_CURRENCY', GetMessage("SALE_WIZARD_PS_BILL_RUB"));
                COption::SetOptionString('sale', '1C_SALE_USE_ZIP', 'Y');
            }
            COption::SetOptionString(
                'sale',
                'weight_unit',
                GetMessage("SALE_WIZARD_WEIGHT_UNIT"),
                false,
                WIZARD_SITE_ID
            );
            COption::SetOptionString('sale', 'WEIGHT_different_set', 'N', false, WIZARD_SITE_ID);
            COption::SetOptionString('sale', 'ADDRESS_different_set', 'N');
            COption::SetOptionString('sale', 'measurement_path', '/bitrix/modules/sale/measurements.php');
            COption::SetOptionString(
                'sale',
                'delivery_handles_custom_path',
                '/bitrix/php_interface/include/sale_delivery/'
            );
            if ($bRus) {
                COption::SetOptionString('sale', 'location_zip', '101000');
            }
            COption::SetOptionString('sale', 'weight_koef', '1000', false, WIZARD_SITE_ID);

            COption::SetOptionString('sale', 'recalc_product_list', 'Y');
            COption::SetOptionString('sale', 'recalc_product_list_period', '4');
            COption::SetOptionString('sale', 'order_email', $shopEmail);
        }

        if (!$bRus) {
            $shopLocation = GetMessage("WIZ_CITY");
        }

        if (\Bitrix\Main\Config\Option::get('sale', 'sale_locationpro_migrated', '') == 'Y') {
            $location = '';

            if ($shopLocation <> '') {
                // get city with name equal to $shopLocation
                $item = \Bitrix\Sale\Location\LocationTable::getList([
                    'filter' => [
                        '=NAME.LANGUAGE_ID' => $lang,
                        '=NAME.NAME' => $shopLocation,
                        '=TYPE.CODE' => 'CITY',
                    ],
                    'select' => [
                        'CODE',
                    ],
                ])->fetch();

                if ($item) {
                    $location = $item['CODE'];
                } // city found, simply take it`s code an proceed with it
                else {
                    // city were not found, create it

                    require($_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH . "/locations/pro/country_codes.php");

                    // due to some reasons, $shopLocalization is being changed at the beginning of the step,
                    // but here we want to have real country selected, so introduce a new variable
                    $shopCountry = $wizard->GetVar("shopLocalization");

                    $countryCode = $LOCALIZATION_COUNTRY_CODE_MAP[$shopCountry];
                    $countryId = false;

                    if ($countryCode <> '') {
                        // get country which matches the current localization
                        $countryId = 0;
                        $item = \Bitrix\Sale\Location\LocationTable::getList([
                            'filter' => [
                                '=CODE' => $countryCode,
                                '=TYPE.CODE' => 'COUNTRY',
                            ],
                            'select' => [
                                'ID',
                            ],
                        ])->fetch();

                        // country found
                        if ($item) {
                            $countryId = $item['ID'];
                        }
                    }

                    // at this point types must exist
                    $types = [];
                    $res = \Bitrix\Sale\Location\TypeTable::getList();
                    while ($item = $res->fetch()) {
                        $types[$item['CODE']] = $item['ID'];
                    }

                    if (isset($types['COUNTRY']) && isset($types['CITY'])) {
                        if (!$countryId) {
                            // such country were not found, create it

                            $data = [
                                'CODE' => 'demo_country_' . WIZARD_SITE_ID,
                                'TYPE_ID' => $types['COUNTRY'],
                                'NAME' => [],
                            ];
                            foreach ($arLanguages as $langID) {
                                $data["NAME"][$langID] = [
                                    'NAME' => GetMessage("WIZ_COUNTRY_" . mb_strtoupper($shopCountry)),
                                ];
                            }

                            $res = \Bitrix\Sale\Location\LocationTable::add($data);
                            if ($res->isSuccess()) {
                                $countryId = $res->getId();
                            }
                        }

                        if ($countryId) {
                            // ok, so country were created, now create demo-city

                            $data = [
                                'CODE' => 'demo_city_' . WIZARD_SITE_ID,
                                'TYPE_ID' => $types['CITY'],
                                'NAME' => [],
                                'PARENT_ID' => $countryId,
                            ];
                            foreach ($arLanguages as $langID) {
                                $data["NAME"][$langID] = [
                                    'NAME' => $shopLocation,
                                ];
                            }

                            $res = \Bitrix\Sale\Location\LocationTable::add($data);
                            if ($res->isSuccess()) {
                                $location = 'demo_city_' . WIZARD_SITE_ID;
                            }
                        }
                    }
                }
            }
        } else {
            $location = 0;
            $dbLocation = CSaleLocation::GetList(["ID" => "ASC"], ["LID" => $lang, "CITY_NAME" => $shopLocation]);
            if ($arLocation = $dbLocation->Fetch())//if there are no data in module
            {
                $location = $arLocation["ID"];
            }
            if (intval($location) <= 0) {
                $CurCountryID = 0;
                $db_contList = CSaleLocation::GetList(
                    [],
                    [
                        "COUNTRY_NAME" => GetMessage("WIZ_COUNTRY_" . mb_strtoupper($shopLocalization)),
                        "LID" => $lang,
                    ]
                );
                if ($arContList = $db_contList->Fetch()) {
                    $LLL = intval($arContList["ID"]);
                    $CurCountryID = intval($arContList["COUNTRY_ID"]);
                }

                if (intval($CurCountryID) <= 0) {
                    $arArrayTmp = [];
                    $arArrayTmp["NAME"] = GetMessage("WIZ_COUNTRY_" . mb_strtoupper($shopLocalization));
                    foreach ($arLanguages as $langID) {
                        WizardServices::IncludeServiceLang("step1.php", $langID);
                        $arArrayTmp[$langID] = [
                            "LID" => $langID,
                            "NAME" => GetMessage("WIZ_COUNTRY_" . mb_strtoupper($shopLocalization)),
                        ];
                    }
                    $CurCountryID = CSaleLocation::AddCountry($arArrayTmp);
                }

                $arArrayTmp = [];
                $arArrayTmp["NAME"] = $shopLocation;
                foreach ($arLanguages as $langID) {
                    $arArrayTmp[$langID] = [
                        "LID" => $langID,
                        "NAME" => $shopLocation,
                    ];
                }
                $city_id = CSaleLocation::AddCity($arArrayTmp);

                $location = CSaleLocation::AddLocation(
                    [
                        "COUNTRY_ID" => $CurCountryID,
                        "CITY_ID" => $city_id,
                    ]
                );
                if ($bRus) {
                    CSaleLocation::AddLocationZIP($location, "101000");
                }

                WizardServices::IncludeServiceLang("step1.php", $lang);
            }
        }

        COption::SetOptionString('sale', 'location', $location);
    }
    //Order Prop Group
    if ($fizExist) {
        $dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList([],
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"),
            ],
            false,
            false,
            ["ID"]);
        if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext()) {
            $arGeneralInfo["propGroup"]["user_fiz"] = $arSaleOrderPropsGroup["ID"];
        }

        $dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList([],
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"),
            ],
            false,
            false,
            ["ID"]);
        if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext()) {
            $arGeneralInfo["propGroup"]["adres_fiz"] = $arSaleOrderPropsGroup["ID"];
        }
    } elseif ($personType["fiz"] == "Y") {
        $arGeneralInfo["propGroup"]["user_fiz"] = CSaleOrderPropsGroup::Add(
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"),
                "SORT" => 100,
            ]
        );
        $arGeneralInfo["propGroup"]["adres_fiz"] = CSaleOrderPropsGroup::Add(
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"),
                "SORT" => 200,
            ]
        );
    }

    if ($urExist) {
        $dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList([],
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR1"),
            ],
            false,
            false,
            ["ID"]);
        if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext()) {
            $arGeneralInfo["propGroup"]["user_ur"] = $arSaleOrderPropsGroup["ID"];
        }

        $dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList([],
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR2"),
            ],
            false,
            false,
            ["ID"]);
        if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext()) {
            $arGeneralInfo["propGroup"]["adres_ur"] = $arSaleOrderPropsGroup["ID"];
        }
    } elseif ($personType["ur"] == "Y") {
        $arGeneralInfo["propGroup"]["user_ur"] = CSaleOrderPropsGroup::Add(
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR1"),
                "SORT" => 300,
            ]
        );
        $arGeneralInfo["propGroup"]["adres_ur"] = CSaleOrderPropsGroup::Add(
            [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR2"),
                "SORT" => 400,
            ]
        );
    }

    if ($shopLocalization == "ua") {
        if ($fizUaExist) {
            $dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList([],
                [
                    "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
                    "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"),
                ],
                false,
                false,
                ["ID"]);
            if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext()) {
                $arGeneralInfo["propGroup"]["user_fiz_ua"] = $arSaleOrderPropsGroup["ID"];
            }

            $dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList([],
                [
                    "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
                    "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"),
                ],
                false,
                false,
                ["ID"]);
            if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext()) {
                $arGeneralInfo["propGroup"]["adres_fiz_ua"] = $arSaleOrderPropsGroup["ID"];
            }
        } elseif ($personType["fiz_ua"] == "Y") {
            $arGeneralInfo["propGroup"]["user_fiz_ua"] = CSaleOrderPropsGroup::Add(
                [
                    "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
                    "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"),
                    "SORT" => 100,
                ]
            );
            $arGeneralInfo["propGroup"]["adres_fiz_ua"] = CSaleOrderPropsGroup::Add(
                [
                    "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
                    "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"),
                    "SORT" => 200,
                ]
            );
        }
    }

    $businessValuePersonDomain = [];

    $businessValueGroups = [
        'COMPANY' => ['SORT' => 100],
        'CLIENT' => ['SORT' => 200],
        'CLIENT_COMPANY' => ['SORT' => 300],
    ];

    $businessValueCodes = [];

    $arProps = [];

    if ($personType["fiz"] == "Y") {
        $businessValuePersonDomain[$arGeneralInfo["personType"]["fiz"]] = $BIZVAL_INDIVIDUAL_DOMAIN;

        $businessValueCodes['CLIENT_NAME'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 100,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_6"),
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 100,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "Y",
            "IS_PAYER" => "Y",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "FIO",
            "IS_FILTERED" => "Y",
        ];

        $businessValueCodes['CLIENT_EMAIL'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 110,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => "E-Mail",
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 110,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "Y",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "EMAIL",
            "IS_FILTERED" => "Y",
        ];

        $businessValueCodes['CLIENT_PHONE'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 120,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_9"),
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 120,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz"],
            "SIZE1" => 0,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "PHONE",
            "IS_PHONE" => "Y",
            "IS_FILTERED" => "N",
        ];

        $businessValueCodes['CLIENT_ZIP'] = ['GROUP' => 'CLIENT', 'SORT' => 130, 'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_4"),
            "TYPE" => "TEXT",
            "REQUIED" => "N",
            "DEFAULT_VALUE" => "101000",
            "SORT" => 130,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
            "SIZE1" => 8,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "ZIP",
            "IS_FILTERED" => "N",
            "IS_ZIP" => "Y",
        ];

        $businessValueCodes['CLIENT_CITY'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 145,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_21"),
            "TYPE" => "TEXT",
            "REQUIED" => "N",
            "DEFAULT_VALUE" => $shopLocation,
            "SORT" => 145,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "CITY",
            "IS_FILTERED" => "Y",
        ];

        $businessValueCodes['CLIENT_LOCATION'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 140,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_2"),
            "TYPE" => "LOCATION",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => $location,
            "SORT" => 140,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "Y",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "LOCATION",
            "IS_FILTERED" => "N",
            "INPUT_FIELD_LOCATION" => "",
        ];

        $businessValueCodes['CLIENT_ADDRESS'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 150,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_5"),
            "TYPE" => "TEXTAREA",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 150,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
            "SIZE1" => 30,
            "SIZE2" => 3,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "ADDRESS",
            "IS_FILTERED" => "N",
            "IS_ADDRESS" => "Y",
        ];
    }

    if ($personType["ur"] == "Y") {
        $businessValuePersonDomain[$arGeneralInfo["personType"]["ur"]] = $BIZVAL_ENTITY_DOMAIN;

        if ($shopLocalization != "ua") {
            $businessValueCodes['COMPANY_NAME'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 200,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_8"),
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 200,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "Y",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "COMPANY",
                "IS_FILTERED" => "Y",
            ];

            $businessValueCodes['COMPANY_ADDRESS'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 210,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_7"),
                "TYPE" => "TEXTAREA",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 210,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "COMPANY_ADR",
                "IS_FILTERED" => "N",
                "IS_ADDRESS" => "Y",
            ];

            $businessValueCodes['COMPANY_INN'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 220,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_13"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 220,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "INN",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_KPP'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 230,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_14"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 230,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "KPP",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_CONTACT_NAME'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 240,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_10"),
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 240,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "Y",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "CONTACT_PERSON",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_EMAIL'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 250,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => "E-Mail",
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 250,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "Y",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "EMAIL",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_PHONE'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 260,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_9"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 260,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "IS_PHONE" => "Y",
                "CODE" => "PHONE",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_FAX'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 270,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_11"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 270,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "FAX",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_ZIP'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 280,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_4"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "101000",
                "SORT" => 280,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 8,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "ZIP",
                "IS_FILTERED" => "N",
                "IS_ZIP" => "Y",
            ];

            $businessValueCodes['COMPANY_CITY'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 285,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_21"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => $shopLocation,
                "SORT" => 285,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "CITY",
                "IS_FILTERED" => "Y",
            ];

            $businessValueCodes['COMPANY_LOCATION'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 290,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_2"),
                "TYPE" => "LOCATION",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 290,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "Y",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "Y",
                "CODE" => "LOCATION",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_OPERATION_ADDRESS'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 300,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_12"),
                "TYPE" => "TEXTAREA",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 300,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 10,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "ADDRESS",
                "IS_FILTERED" => "N",
                "IS_ADDRESS" => "Y",
            ];
        } else {
            /*
			$businessValueCodes['COMPANY_CONTACT_NAME'] = array('GROUP' => 'COMPANY', 'SORT' =>  100, 'DOMAIN' => $BIZVAL_ENTITY_DOMAIN);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_41"),
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 100,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "Y",
				"IS_PAYER" => "Y",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "CONTACT_NAME",
				"IS_FILTERED" => "Y",
			);*/

            $businessValueCodes['COMPANY_EMAIL'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 110,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => "E-Mail",
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 110,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "Y",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "EMAIL",
                "IS_FILTERED" => "Y",
            ];

            $businessValueCodes['COMPANY_NAME'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 130,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_40"),
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 130,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "Y",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "COMPANY_NAME",
                "IS_FILTERED" => "Y",
            ];

            $businessValueCodes['COMPANY_ADDRESS'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 140,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_47"),
                "TYPE" => "TEXTAREA",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 140,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 40,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "COMPANY_ADR",
                "IS_FILTERED" => "N",
                "IS_ADDRESS" => "Y",
            ];

            $businessValueCodes['COMPANY_EGRPU'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 150,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_48"),
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 150,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "EGRPU",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_INN'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 160,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_49"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 160,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "INN",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_NDS'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 170,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_46"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 170,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "NDS",
                "IS_FILTERED" => "N",
            ];

            $businessValueCodes['COMPANY_ZIP'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 180,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_44"),
                "TYPE" => "TEXT",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 180,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 8,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "ZIP",
                "IS_FILTERED" => "N",
                "IS_ZIP" => "Y",
            ];

            $businessValueCodes['COMPANY_CITY'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 190,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_43"),
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => $shopLocation,
                "SORT" => 190,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "CITY",
                "IS_FILTERED" => "Y",
            ];

            $businessValueCodes['COMPANY_OPERATION_ADDRESS'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 200,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_42"),
                "TYPE" => "TEXTAREA",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 200,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 3,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "ADDRESS",
                "IS_FILTERED" => "N",
                "IS_ADDRESS" => "Y",
            ];

            $businessValueCodes['COMPANY_PHONE'] = [
                'GROUP' => 'COMPANY',
                'SORT' => 210,
                'DOMAIN' => $BIZVAL_ENTITY_DOMAIN,
            ];
            $arProps[] = [
                "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
                "NAME" => GetMessage("SALE_WIZARD_PROP_45"),
                "TYPE" => "TEXT",
                "REQUIED" => "Y",
                "DEFAULT_VALUE" => "",
                "SORT" => 210,
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
                "SIZE1" => 30,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "CODE" => "PHONE",
                "IS_FILTERED" => "N",
            ];
        }
    }

    if ($shopLocalization == "ua" && $personType["fiz_ua"] == "Y") {
        /*
		$businessValueCodes['CLIENT_NAME'] = array('GROUP' => 'CLIENT', 'SORT' =>  100, 'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN);
		$arProps[] = Array(
			"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
			"NAME" => GetMessage("SALE_WIZARD_PROP_31"),
			"TYPE" => "TEXT",
			"REQUIED" => "Y",
			"DEFAULT_VALUE" => "",
			"SORT" => 100,
			"USER_PROPS" => "Y",
			"IS_LOCATION" => "N",
			"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
			"SIZE1" => 40,
			"SIZE2" => 0,
			"DESCRIPTION" => "",
			"IS_EMAIL" => "N",
			"IS_PROFILE_NAME" => "Y",
			"IS_PAYER" => "Y",
			"IS_LOCATION4TAX" => "N",
			"CODE" => "FIO",
			"IS_FILTERED" => "Y",
		);
		*/

        $businessValuePersonDomain[$arGeneralInfo["personType"]["fiz_ua"]] = $BIZVAL_INDIVIDUAL_DOMAIN;

        $businessValueCodes['CLIENT_EMAIL'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 110,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => "E-Mail",
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 110,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "Y",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "EMAIL",
            "IS_FILTERED" => "Y",
        ];

        $businessValueCodes['CLIENT_NAME'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 130,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_30"),
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 130,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "Y",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "FIO",
            "IS_FILTERED" => "Y",
        ];

        $businessValueCodes['CLIENT_COMPANY_ADDRESS'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 140,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_37"),
            "TYPE" => "TEXTAREA",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 140,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
            "SIZE1" => 40,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "COMPANY_ADR",
            "IS_FILTERED" => "N",
        ];

        $businessValueCodes['CLIENT_EGRPU'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 150,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_38"),
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 150,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
            "SIZE1" => 30,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "EGRPU",
            "IS_FILTERED" => "N",
        ];

        /*
		$businessValueCodes['CLIENT_INN'] = array('GROUP' => 'CLIENT', 'SORT' =>  160, 'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN);
		$arProps[] = Array(
			"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
			"NAME" => GetMessage("SALE_WIZARD_PROP_39"),
			"TYPE" => "TEXT",
			"REQUIED" => "N",
			"DEFAULT_VALUE" => "",
			"SORT" => 160,
			"USER_PROPS" => "Y",
			"IS_LOCATION" => "N",
			"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
			"SIZE1" => 30,
			"SIZE2" => 0,
			"DESCRIPTION" => "",
			"IS_EMAIL" => "N",
			"IS_PROFILE_NAME" => "N",
			"IS_PAYER" => "N",
			"IS_LOCATION4TAX" => "N",
			"CODE" => "INN",
			"IS_FILTERED" => "N",
		);
		*/

        $businessValueCodes['CLIENT_NDS'] = ['GROUP' => 'CLIENT', 'SORT' => 170, 'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_36"),
            "TYPE" => "TEXT",
            "REQUIED" => "N",
            "DEFAULT_VALUE" => "",
            "SORT" => 170,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
            "SIZE1" => 30,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "NDS",
            "IS_FILTERED" => "N",
        ];

        $businessValueCodes['CLIENT_ZIP'] = ['GROUP' => 'CLIENT', 'SORT' => 180, 'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_34"),
            "TYPE" => "TEXT",
            "REQUIED" => "N",
            "DEFAULT_VALUE" => "",
            "SORT" => 180,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
            "SIZE1" => 8,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "ZIP",
            "IS_FILTERED" => "N",
            "IS_ZIP" => "Y",
        ];

        $businessValueCodes['CLIENT_CITY'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 190,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_33"),
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => $shopLocation,
            "SORT" => 190,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
            "SIZE1" => 30,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "CITY",
            "IS_FILTERED" => "Y",
        ];

        $businessValueCodes['CLIENT_ADDRESS'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 200,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_32"),
            "TYPE" => "TEXTAREA",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 200,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
            "SIZE1" => 30,
            "SIZE2" => 3,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "ADDRESS",
            "IS_FILTERED" => "N",
            "IS_ADDRESS" => "Y",
        ];

        $businessValueCodes['CLIENT_PHONE'] = [
            'GROUP' => 'CLIENT',
            'SORT' => 210,
            'DOMAIN' => $BIZVAL_INDIVIDUAL_DOMAIN,
        ];
        $arProps[] = [
            "PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
            "NAME" => GetMessage("SALE_WIZARD_PROP_35"),
            "TYPE" => "TEXT",
            "REQUIED" => "Y",
            "DEFAULT_VALUE" => "",
            "SORT" => 210,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
            "SIZE1" => 30,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "IS_LOCATION4TAX" => "N",
            "CODE" => "PHONE",
            "IS_PHONE" => "Y",
            "IS_FILTERED" => "N",
        ];
    }

    $propCityId = 0;
    reset($businessValueCodes);

    foreach ($arProps as $prop) {
        $variants = [];
        if (!empty($prop["VARIANTS"])) {
            $variants = $prop["VARIANTS"];
            unset($prop["VARIANTS"]);
        }

        if ($prop["CODE"] == "LOCATION" && $propCityId > 0) {
            $prop["INPUT_FIELD_LOCATION"] = $propCityId;
            $propCityId = 0;
        }

        $dbSaleOrderProps = CSaleOrderProps::GetList([],
            ["PERSON_TYPE_ID" => $prop["PERSON_TYPE_ID"], "CODE" => $prop["CODE"]]);
        if ($arSaleOrderProps = $dbSaleOrderProps->GetNext()) {
            $id = $arSaleOrderProps["ID"];
        } else {
            $id = CSaleOrderProps::Add($prop);
        }

        if ($prop["CODE"] == "CITY") {
            $propCityId = $id;
        }
        if ($prop["CODE"] <> '') {
            //$arGeneralInfo["propCode"][$prop["CODE"]] = $prop["CODE"];
            $arGeneralInfo["propCodeID"][$prop["CODE"]] = $id;
            $arGeneralInfo["properies"][$prop["PERSON_TYPE_ID"]][$prop["CODE"]] = $prop;
            $arGeneralInfo["properies"][$prop["PERSON_TYPE_ID"]][$prop["CODE"]]["ID"] = $id;
        }

        if (!empty($variants)) {
            foreach ($variants as $val) {
                $val["ORDER_PROPS_ID"] = $id;
                CSaleOrderPropsVariant::Add($val);
            }
        }

        // add business value mapping to property
        $businessValueCodes[key($businessValueCodes)]['MAP'] = [$prop['PERSON_TYPE_ID'] => ['PROPERTY', $id]];
        next($businessValueCodes);
    }

    if (defined("ADDITIONAL_INSTALL")) {
        return;
    }

    /*
	$propReplace = "";
	foreach($arGeneralInfo["properies"] as $key => $val)
	{
		if(IntVal($val["LOCATION"]["ID"]) > 0)
			$propReplace .= '"PROP_'.$key.'" => Array(0 => "'.$val["LOCATION"]["ID"].'"), ';
	}
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/order/", Array("PROPS" => $propReplace));
*/
    //1C export
    if ($personType["fiz"] == "Y" && !$fizExist) {
        $val = serialize([
            "AGENT_NAME" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["FIO"]["ID"],
            ],
            "FULL_NAME" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["FIO"]["ID"],
            ],
            "SURNAME" => ["TYPE" => "USER", "VALUE" => "LAST_NAME"],
            "NAME" => ["TYPE" => "USER", "VALUE" => "NAME"],
            "ADDRESS_FULL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ADDRESS"]["ID"],
            ],
            "INDEX" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ZIP"]["ID"],
            ],
            "COUNTRY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["LOCATION"]["ID"] . "_COUNTRY",
            ],
            "CITY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["LOCATION"]["ID"] . "_CITY",
            ],
            "STREET" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ADDRESS"]["ID"],
            ],
            "EMAIL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["EMAIL"]["ID"],
            ],
            "PHONE" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["PHONE"]["ID"],
            ],
            "CONTACT_PERSON" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["CONTACT_PERSON"]["ID"],
            ],
            "IS_FIZ" => "Y",
        ]);

        $allPersonTypes = BusinessValue::getPersonTypes(true);
        $personTypeId = $arGeneralInfo["personType"]["fiz"];
        $domain = BusinessValue::INDIVIDUAL_DOMAIN;

        if (!isset($allPersonTypes[$personTypeId]['DOMAIN'])) {
            $r = Bitrix\Sale\Internals\BusinessValuePersonDomainTable::add([
                'PERSON_TYPE_ID' => $personTypeId,
                'DOMAIN' => $domain,
            ]);
            if ($r->isSuccess()) {
                $allPersonTypes[$personTypeId]['DOMAIN'] = $domain;
                BusinessValue::getPersonTypes(true, $allPersonTypes);
            }
        }

        CSaleExport::Add(["PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "VARS" => $val]);
    }
    if ($personType["ur"] == "Y" && !$urExist) {
        $val = serialize([
            "AGENT_NAME" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY"]["ID"],
            ],
            "FULL_NAME" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY"]["ID"],
            ],
            "ADDRESS_FULL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY_ADR"]["ID"],
            ],
            "COUNTRY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"] . "_COUNTRY",
            ],
            "CITY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"] . "_CITY",
            ],
            "STREET" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY_ADR"]["ID"],
            ],
            "INN" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["INN"]["ID"],
            ],
            "KPP" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["KPP"]["ID"],
            ],
            "PHONE" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["PHONE"]["ID"],
            ],
            "EMAIL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["EMAIL"]["ID"],
            ],
            "CONTACT_PERSON" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["NAME"]["ID"],
            ],
            "F_ADDRESS_FULL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ADDRESS"]["ID"],
            ],
            "F_COUNTRY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"] . "_COUNTRY",
            ],
            "F_CITY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"] . "_CITY",
            ],
            "F_INDEX" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ZIP"]["ID"],
            ],
            "F_STREET" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ADDRESS"]["ID"],
            ],
            "IS_FIZ" => "N",
        ]);

        $allPersonTypes = BusinessValue::getPersonTypes(true);
        $personTypeId = $arGeneralInfo["personType"]["ur"];
        $domain = BusinessValue::ENTITY_DOMAIN;

        if (!isset($allPersonTypes[$personTypeId]['DOMAIN'])) {
            $r = Bitrix\Sale\Internals\BusinessValuePersonDomainTable::add([
                'PERSON_TYPE_ID' => $personTypeId,
                'DOMAIN' => $domain,
            ]);
            if ($r->isSuccess()) {
                $allPersonTypes[$personTypeId]['DOMAIN'] = $domain;
                BusinessValue::getPersonTypes(true, $allPersonTypes);
            }
        }

        CSaleExport::Add(["PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"], "VARS" => $val]);
    }
    if ($shopLocalization == "ua" && !$fizUaExist) {
        $val = serialize([
            "AGENT_NAME" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["FIO"]["ID"],
            ],
            "FULL_NAME" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["FIO"]["ID"],
            ],
            "SURNAME" => ["TYPE" => "USER", "VALUE" => "LAST_NAME"],
            "NAME" => ["TYPE" => "USER", "VALUE" => "NAME"],
            "ADDRESS_FULL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["ADDRESS"]["ID"],
            ],
            "INDEX" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["ZIP"]["ID"],
            ],
            "COUNTRY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["LOCATION"]["ID"] . "_COUNTRY",
            ],
            "CITY" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["LOCATION"]["ID"] . "_CITY",
            ],
            "STREET" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["ADDRESS"]["ID"],
            ],
            "EMAIL" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["EMAIL"]["ID"],
            ],
            "PHONE" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["PHONE"]["ID"],
            ],
            "CONTACT_PERSON" => [
                "TYPE" => "PROPERTY",
                "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["CONTACT_PERSON"]["ID"],
            ],
            "IS_FIZ" => "Y",
        ]);
        CSaleExport::Add(["PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "VARS" => $val]);
    }
    //PaySystem
    $arPaySystems = [];
    if ($paysystem["cash"] == "Y") {
        $arPaySystems[] = [
            'PAYSYSTEM' => [
                "NAME" => GetMessage("SALE_WIZARD_PS_CASH"),
                "PSA_NAME" => GetMessage("SALE_WIZARD_PS_CASH"),
                "SORT" => 80,
                "ACTIVE" => "Y",
                "IS_CASH" => "Y",
                "DESCRIPTION" => GetMessage("SALE_WIZARD_PS_CASH_DESCR"),
                "ACTION_FILE" => "cash",
                "RESULT_FILE" => "",
                "NEW_WINDOW" => "N",
                "PARAMS" => "",
                "HAVE_PAYMENT" => "Y",
                "HAVE_ACTION" => "N",
                "HAVE_RESULT" => "N",
                "HAVE_PREPAY" => "N",
                "HAVE_RESULT_RECEIVE" => "N",
                'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
            ],
            'PERSON_TYPE' => [$arGeneralInfo["personType"]["fiz"]],
        ];
    }

    if ($paysystem["collect"] == "Y") {
        $arPaySystems[] = [
            'PAYSYSTEM' => [
                "NAME" => GetMessage("SALE_WIZARD_PS_COLLECT"),
                "SORT" => 110,
                "ACTIVE" => "Y",
                "DESCRIPTION" => GetMessage("SALE_WIZARD_PS_COLLECT_DESCR"),
                "PSA_NAME" => GetMessage("SALE_WIZARD_PS_COLLECT"),
                "ACTION_FILE" => "cashondeliverycalc",
                "RESULT_FILE" => "",
                "NEW_WINDOW" => "N",
                "HAVE_PAYMENT" => "Y",
                "HAVE_ACTION" => "N",
                "HAVE_RESULT" => "N",
                "HAVE_PREPAY" => "N",
                "HAVE_RESULT_RECEIVE" => "N",
                'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
            ],
            'PERSON_TYPE' => [$arGeneralInfo["personType"]["fiz"], $arGeneralInfo["personType"]["ur"]],
        ];
    }
    if ($personType["fiz"] == "Y" && $shopLocalization != "ua") {
        if ($bRus) {
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_YMoney"),
                    "SORT" => 50,
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_YMoney_DESC"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_YMoney"),
                    "ACTION_FILE" => "yandex",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "PS_MODE" => "PC",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                'PERSON_TYPE' => [$arGeneralInfo["personType"]["fiz"]],
                "BIZVAL" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "PAYMENT", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                        "PS_IS_TEST" => ["VALUE" => "Y"],
                        "PS_CHANGE_STATUS_PAY" => ["VALUE" => "Y"],
                        "YANDEX_SHOP_ID" => ["TYPE" => "", "VALUE" => ""],
                        "YANDEX_SCID" => ["TYPE" => "", "VALUE" => ""],
                        "YANDEX_SHOP_KEY" => ["TYPE" => "", "VALUE" => ""],
                    ],
                ],
            ];

            $logo = $_SERVER["DOCUMENT_ROOT"] . WIZARD_SERVICE_RELATIVE_PATH . "/images/yandex_cards.png";
            $arPicture = CFile::MakeFileArray($logo);
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_YCards"),
                    "SORT" => 60,
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_YCards_DESC"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_YCards"),
                    "ACTION_FILE" => "yandex",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    "PS_MODE" => "AC",
                    "LOGOTIP" => $arPicture,
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "BIZVAL" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "ORDER", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                        "PS_IS_TEST" => ["VALUE" => "Y"],
                        "PS_CHANGE_STATUS_PAY" => ["VALUE" => "Y"],
                        "YANDEX_SHOP_ID" => ["TYPE" => "", "VALUE" => ""],
                        "YANDEX_SCID" => ["TYPE" => "", "VALUE" => ""],
                        "YANDEX_SHOP_KEY" => ["TYPE" => "", "VALUE" => ""],
                    ],
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
            ];
            $logo = $_SERVER["DOCUMENT_ROOT"] . WIZARD_SERVICE_RELATIVE_PATH . "/images/yandex_terminals.png";
            $arPicture = CFile::MakeFileArray($logo);
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_YTerminals"),
                    "SORT" => 70,
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_YTerminals_DESC"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_YTerminals"),
                    "ACTION_FILE" => "yandex",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    "LOGOTIP" => $arPicture,
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "BIZVAL" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "ORDER", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                        "PS_IS_TEST" => ["VALUE" => "Y"],
                        "PS_CHANGE_STATUS_PAY" => ["VALUE" => "Y"],
                        "YANDEX_SHOP_ID" => ["TYPE" => "", "VALUE" => ""],
                        "YANDEX_SCID" => ["TYPE" => "", "VALUE" => ""],
                        "YANDEX_SHOP_KEY" => ["TYPE" => "", "VALUE" => ""],
                    ],
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
            ];
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_PS_WM"),
                    "SORT" => 90,
                    "ACTIVE" => "N",
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_PS_WM_DESCR"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_PS_WM"),
                    "ACTION_FILE" => "webmoney",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "Y",
                    "PARAMS" => "",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "Y",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "N",
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
            ];

            if ($paysystem["sber"] == "Y") {
                $arPaySystems[] = [
                    'PAYSYSTEM' => [
                        "NAME" => GetMessage("SALE_WIZARD_PS_SB"),
                        "SORT" => 110,
                        "DESCRIPTION" => GetMessage("SALE_WIZARD_PS_SB_DESCR"),
                        "PSA_NAME" => GetMessage("SALE_WIZARD_PS_SB"),
                        "ACTION_FILE" => "sberbank",
                        "RESULT_FILE" => "",
                        "NEW_WINDOW" => "Y",
                        "HAVE_PAYMENT" => "Y",
                        "HAVE_ACTION" => "N",
                        "HAVE_RESULT" => "N",
                        "HAVE_PREPAY" => "N",
                        "HAVE_RESULT_RECEIVE" => "N",
                        'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                    ],
                    "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
                    "BIZVAL" => [
                        '' => [
                            "SELLER_COMPANY_NAME" => ["TYPE" => "", "VALUE" => $shopOfName],
                            "SELLER_COMPANY_INN" => ["TYPE" => "", "VALUE" => $shopINN],
                            "SELLER_COMPANY_KPP" => ["TYPE" => "", "VALUE" => $shopKPP],
                            "SELLER_COMPANY_BANK_ACCOUNT" => ["TYPE" => "", "VALUE" => $shopNS],
                            "SELLER_COMPANY_BANK_NAME" => ["TYPE" => "", "VALUE" => $shopBANK],
                            "SELLER_COMPANY_BANK_BIC" => ["TYPE" => "", "VALUE" => $shopBANKREKV],
                            "SELLER_COMPANY_BANK_ACCOUNT_CORR" => ["TYPE" => "", "VALUE" => $shopKS],
                            "PAYMENT_ID" => ["TYPE" => "PAYMENT", "VALUE" => "ACCOUNT_NUMBER"],
                            "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_INSERT_DATE"],
                            "BUYER_PERSON_FIO" => ["TYPE" => "PROPERTY", "VALUE" => "FIO"],
                            "BUYER_PERSON_ZIP" => ["TYPE" => "PROPERTY", "VALUE" => "ZIP"],
                            "BUYER_PERSON_COUNTRY" => ["TYPE" => "PROPERTY", "VALUE" => "LOCATION_COUNTRY"],
                            "BUYER_PERSON_REGION" => ["TYPE" => "PROPERTY", "VALUE" => "LOCATION_REGION"],
                            "BUYER_PERSON_CITY" => ["TYPE" => "PROPERTY", "VALUE" => "LOCATION_CITY"],
                            "BUYER_PERSON_ADDRESS_FACT" => ["TYPE" => "PROPERTY", "VALUE" => "ADDRESS"],
                            "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                        ],
                    ],
                ];
            }
        } else {
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => "PayPal",
                    "SORT" => 90,
                    "DESCRIPTION" => "",
                    "PSA_NAME" => "PayPal",
                    "ACTION_FILE" => "paypal",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "BIZVAL" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "PAYMENT", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL_DATE"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                        "PAYMENT_CURRENCY" => ["TYPE" => "PAYMENT", "VALUE" => "CURRENCY"],
                    ],
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
            ];
        }
    }
    if ($personType["ur"] == "Y" && $paysystem["bill"] == "Y" && $shopLocalization != "ua") {
        $arPaySystems[] = [
            'PAYSYSTEM' => [
                "NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
                "SORT" => 100,
                "DESCRIPTION" => "",
                "PSA_NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
                "ACTION_FILE" => "bill",
                "RESULT_FILE" => "",
                "NEW_WINDOW" => "Y",
                "HAVE_PAYMENT" => "Y",
                "HAVE_ACTION" => "N",
                "HAVE_RESULT" => "N",
                "HAVE_PREPAY" => "N",
                "HAVE_RESULT_RECEIVE" => "N",
                'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
            ],
            "PERSON_TYPE" => [$arGeneralInfo["personType"]["ur"]],
            "BIZVAL" => [
                '' => [
                    "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL_DATE"],
                    "SELLER_COMPANY_NAME" => ["TYPE" => "", "VALUE" => $shopOfName],
                    "SELLER_COMPANY_ADDRESS" => ["TYPE" => "", "VALUE" => $shopAdr],
                    "SELLER_COMPANY_PHONE" => ["TYPE" => "", "VALUE" => $siteTelephone],
                    "SELLER_COMPANY_INN" => ["TYPE" => "", "VALUE" => $shopINN],
                    "SELLER_COMPANY_KPP" => ["TYPE" => "", "VALUE" => $shopKPP],
                    "SELLER_COMPANY_BANK_ACCOUNT" => ["TYPE" => "", "VALUE" => $shopNS],
                    "SELLER_COMPANY_BANK_ACCOUNT_CORR" => ["TYPE" => "", "VALUE" => $shopKS],
                    "SELLER_COMPANY_BANK_BIC" => ["TYPE" => "", "VALUE" => $shopBANKREKV],
                    "BUYER_PERSON_COMPANY_NAME" => ["TYPE" => "PROPERTY", "VALUE" => "COMPANY_NAME"],
                    "BUYER_PERSON_COMPANY_INN" => ["TYPE" => "PROPERTY", "VALUE" => "INN"],
                    "BUYER_PERSON_COMPANY_ADDRESS" => ["TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"],
                    "BUYER_PERSON_COMPANY_PHONE" => ["TYPE" => "PROPERTY", "VALUE" => "PHONE"],
                    "BUYER_PERSON_COMPANY_FAX" => ["TYPE" => "PROPERTY", "VALUE" => "FAX"],
                    "BUYER_PERSON_COMPANY_NAME_CONTACT" => ["TYPE" => "PROPERTY", "VALUE" => "CONTACT_PERSON"],
                    "BILL_PATH_TO_STAMP" => ["TYPE" => "", "VALUE" => $siteStamp],
                ],
            ],
        ];
    }
//Ukraine
    if ($shopLocalization == "ua") {
        if ($personType["fiz"] == "Y") {
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_YMoney"),
                    "SORT" => 60,
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_YMoney_DESC"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_YMoney"),
                    "ACTION_FILE" => "yandex",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "PS_MODE" => "PC",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
                "PARAMS" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "PAYMENT", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                    ],
                ],
            ];
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_YCards"),
                    "SORT" => 70,
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_YCards_DESC"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_YCards"),
                    "ACTION_FILE" => "yandex",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "PS_MODE" => "AC",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
                "BIZVAL" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "PAYMENT", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                    ],
                ],
            ];
            $arPaySystems[] = [
                'PAYSYSTEM' => [
                    "NAME" => GetMessage("SALE_WIZARD_YTerminals"),
                    "SORT" => 80,
                    "DESCRIPTION" => GetMessage("SALE_WIZARD_YTerminals_DESC"),
                    "PSA_NAME" => GetMessage("SALE_WIZARD_YTerminals"),
                    "ACTION_FILE" => "yandex",
                    "RESULT_FILE" => "",
                    "NEW_WINDOW" => "N",
                    "HAVE_PAYMENT" => "Y",
                    "HAVE_ACTION" => "N",
                    "HAVE_RESULT" => "N",
                    "HAVE_PREPAY" => "N",
                    "HAVE_RESULT_RECEIVE" => "Y",
                    "PS_MODE" => "GP",
                    'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
                ],
                "PERSON_TYPE" => [$arGeneralInfo["personType"]["fiz"]],
                "BIZVAL" => [
                    '' => [
                        "PAYMENT_ID" => ["TYPE" => "PAYMENT", "VALUE" => "ID"],
                        "PAYMENT_DATE_INSERT" => ["TYPE" => "PAYMENT", "VALUE" => "DATE_BILL"],
                        "PAYMENT_SHOULD_PAY" => ["TYPE" => "PAYMENT", "VALUE" => "SUM"],
                    ],
                ],
            ];
        }
        //bill
        if ($paysystem["bill"] == "Y") {
            $arPaySystem['PAYSYSTEM'] = [
                "NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
                "PSA_NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
                "ACTION_FILE" => "billua",
                "RESULT_FILE" => "",
                "NEW_WINDOW" => "Y",
                "HAVE_PAYMENT" => "Y",
                "HAVE_ACTION" => "N",
                "HAVE_RESULT" => "N",
                "HAVE_PREPAY" => "N",
                "HAVE_RESULT_RECEIVE" => "N",
                'ENTITY_REGISTRY_TYPE' => Sale\Registry::REGISTRY_TYPE_ORDER,
            ];

            $arPaySystem['PERSON_TYPE'] = [];
            $arPaySystem['BIZVAL'] = [];

            if ($personType["ur"] == "Y") {
                $arPaySystem['PERSON_TYPE'][] = $arGeneralInfo["personType"]["ur"];
                $arPaySystem['BIZVAL'][$arGeneralInfo["personType"]["ur"]] = [
                    "PAYMENT_DATE_INSERT" => ["TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"],
                    "SELLER_COMPANY_NAME" => ["TYPE" => "", "VALUE" => $shopOfName],
                    "SELLER_COMPANY_ADDRESS" => ["TYPE" => "", "VALUE" => $shopAdr],
                    "SELLER_COMPANY_PHONE" => ["TYPE" => "", "VALUE" => $siteTelephone],
                    "SELLER_COMPANY_IPN" => ["TYPE" => "", "VALUE" => $shopINN_ua],
                    "SELLER_COMPANY_EDRPOY" => ["TYPE" => "", "VALUE" => $shopEGRPU_ua],
                    "SELLER_COMPANY_BANK_ACCOUNT" => ["TYPE" => "", "VALUE" => $shopNS_ua],
                    "SELLER_COMPANY_BANK_NAME" => ["TYPE" => "", "VALUE" => $shopBank_ua],
                    "SELLER_COMPANY_MFO" => ["TYPE" => "", "VALUE" => $shopMFO_ua],
                    "SELLER_COMPANY_PDV" => ["TYPE" => "", "VALUE" => $shopNDS_ua],
                    "PAYMENT_ID" => ["TYPE" => "ORDER", "VALUE" => "ID"],
                    "SELLER_COMPANY_SYS" => ["TYPE" => "", "VALUE" => $shopTax_ua],
                    "BUYER_PERSON_COMPANY_NAME" => ["TYPE" => "PROPERTY", "VALUE" => "COMPANY_NAME"],
                    "BUYER_PERSON_COMPANY_ADDRESS" => ["TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"],
                    "BUYER_PERSON_COMPANY_PHONE" => ["TYPE" => "PROPERTY", "VALUE" => "PHONE"],
                    "BUYER_PERSON_COMPANY_FAX" => ["TYPE" => "PROPERTY", "VALUE" => "FAX"],
                    "BILLUA_PATH_TO_STAMP" => ["TYPE" => "", "VALUE" => $siteStamp],
                ];
            }

            if ($personType["fiz"] == "Y") {
                $arPaySystem['PERSON_TYPE'][] = $arGeneralInfo["personType"]["fiz"];
                $arPaySystem['BIZVAL'][$arGeneralInfo["personType"]["fiz"]] = [
                    "PAYMENT_DATE_INSERT" => ["TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"],
                    "SELLER_COMPANY_NAME" => ["TYPE" => "", "VALUE" => $shopOfName],
                    "SELLER_COMPANY_ADDRESS" => ["TYPE" => "", "VALUE" => $shopAdr],
                    "SELLER_COMPANY_PHONE" => ["TYPE" => "", "VALUE" => $siteTelephone],
                    "SELLER_COMPANY_IPN" => ["TYPE" => "", "VALUE" => $shopINN_ua],
                    "SELLER_COMPANY_EDRPOY" => ["TYPE" => "", "VALUE" => $shopEGRPU_ua],
                    "SELLER_COMPANY_BANK_ACCOUNT" => ["TYPE" => "", "VALUE" => $shopNS_ua],
                    "SELLER_COMPANY_BANK_NAME" => ["TYPE" => "", "VALUE" => $shopBank_ua],
                    "SELLER_COMPANY_MFO" => ["TYPE" => "", "VALUE" => $shopMFO_ua],
                    "SELLER_COMPANY_PDV" => ["TYPE" => "", "VALUE" => $shopNDS_ua],
                    "PAYMENT_ID" => ["TYPE" => "ORDER", "VALUE" => "ID"],
                    "SELLER_COMPANY_SYS" => ["TYPE" => "", "VALUE" => $shopTax_ua],
                    "BUYER_PERSON_COMPANY_NAME" => ["TYPE" => "PROPERTY", "VALUE" => "FIO"],
                    "BUYER_PERSON_COMPANY_ADDRESS" => ["TYPE" => "PROPERTY", "VALUE" => "ADDRESS"],
                    "BUYER_PERSON_COMPANY_PHONE" => ["TYPE" => "PROPERTY", "VALUE" => "PHONE"],
                    "BUYER_PERSON_COMPANY_FAX" => ["TYPE" => "PROPERTY", "VALUE" => "FAX"],
                    "BILLUA_PATH_TO_STAMP" => ["TYPE" => "", "VALUE" => $siteStamp],
                ];
            }

            if ($personType["fiz_ua"] == "Y") {
                $arPaySystem['PERSON_TYPE'][] = $arGeneralInfo["personType"]["fiz_ua"];
                $arPaySystem['BIZVAL'][$arGeneralInfo["personType"]["fiz_ua"]] = [
                    "PAYMENT_DATE_INSERT" => ["TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"],
                    "SELLER_COMPANY_NAME" => ["TYPE" => "", "VALUE" => $shopOfName],
                    "SELLER_COMPANY_ADDRESS" => ["TYPE" => "", "VALUE" => $shopAdr],
                    "SELLER_COMPANY_PHONE" => ["TYPE" => "", "VALUE" => $siteTelephone],
                    "SELLER_COMPANY_IPN" => ["TYPE" => "", "VALUE" => $shopINN_ua],
                    "SELLER_COMPANY_EDRPOY" => ["TYPE" => "", "VALUE" => $shopEGRPU_ua],
                    "SELLER_COMPANY_BANK_ACCOUNT" => ["TYPE" => "", "VALUE" => $shopNS_ua],
                    "SELLER_COMPANY_BANK_NAME" => ["TYPE" => "", "VALUE" => $shopBank_ua],
                    "SELLER_COMPANY_MFO" => ["TYPE" => "", "VALUE" => $shopMFO_ua],
                    "SELLER_COMPANY_PDV" => ["TYPE" => "", "VALUE" => $shopNDS_ua],
                    "PAYMENT_ID" => ["TYPE" => "ORDER", "VALUE" => "ID"],
                    "SELLER_COMPANY_SYS" => ["TYPE" => "", "VALUE" => $shopTax_ua],
                    "BUYER_PERSON_COMPANY_NAME" => ["TYPE" => "PROPERTY", "VALUE" => "FIO"],
                    "BUYER_PERSON_COMPANY_ADDRESS" => ["TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"],
                    "BUYER_PERSON_COMPANY_PHONE" => ["TYPE" => "PROPERTY", "VALUE" => "PHONE"],
                    "BUYER_PERSON_COMPANY_FAX" => ["TYPE" => "PROPERTY", "VALUE" => "FAX"],
                    "BILLUA_PATH_TO_STAMP" => ["TYPE" => "", "VALUE" => $siteStamp],
                ];
            }

            $arPaySystems[] = $arPaySystem;
        }
    }
    //}

    foreach ($arPaySystems as $arPaySystem) {
        $updateFields = [];

        $val = $arPaySystem['PAYSYSTEM'];
        if (array_key_exists('LOGOTIP', $val) && is_array($val['LOGOTIP'])) {
            $updateFields['LOGOTIP'] = $val['LOGOTIP'];
            unset($val['LOGOTIP']);
        }

        $dbRes = \Bitrix\Sale\PaySystem\Manager::getList(
            ['select' => ["ID", "NAME"], 'filter' => ["NAME" => $val["NAME"]]]
        );
        $tmpPaySystem = $dbRes->fetch();
        if (!$tmpPaySystem) {
            $resultAdd = \Bitrix\Sale\PaySystem\Manager::add($val);
            if ($resultAdd->isSuccess()) {
                $id = $resultAdd->getId();

                if (array_key_exists('BIZVAL', $arPaySystem) && $arPaySystem['BIZVAL']) {
                    $arGeneralInfo["paySystem"][$arPaySystem["ACTION_FILE"]] = $id;
                    foreach ($arPaySystem['BIZVAL'] as $personType => $codes) {
                        foreach ($codes as $code => $map) {
                            \Bitrix\Sale\BusinessValue::setMapping(
                                $code,
                                'PAYSYSTEM_' . $id,
                                $personType,
                                ['PROVIDER_KEY' => $map['TYPE'] ?: 'VALUE', 'PROVIDER_VALUE' => $map['VALUE']],
                                true
                            );
                        }
                    }
                }

                if ($arPaySystem['PERSON_TYPE']) {
                    $params = [
                        'filter' => [
                            "SERVICE_ID" => $id,
                            "SERVICE_TYPE" => Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT,
                            "=CLASS_NAME" => '\\' . Sale\Services\PaySystem\Restrictions\PersonType::class,
                        ],
                    ];

                    $dbRes = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList($params);
                    if (!$dbRes->fetch()) {
                        $fields = [
                            "SERVICE_ID" => $id,
                            "SERVICE_TYPE" => \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT,
                            "SORT" => 100,
                            "PARAMS" => [
                                'PERSON_TYPE_ID' => $arPaySystem['PERSON_TYPE'],
                            ],
                        ];
                        \Bitrix\Sale\Services\PaySystem\Restrictions\PersonType::save($fields);
                    }
                }

                $updateFields['PARAMS'] = serialize(['BX_PAY_SYSTEM_ID' => $id]);
                $updateFields['PAY_SYSTEM_ID'] = $id;

                $image = '/bitrix/modules/sale/install/images/sale_payments/' . $val['ACTION_FILE'] . '.png';
                if ((!array_key_exists('LOGOTIP', $updateFields) || !is_array($updateFields['LOGOTIP'])) &&
                    \Bitrix\Main\IO\File::isFileExists($_SERVER['DOCUMENT_ROOT'] . $image)
                ) {
                    $updateFields['LOGOTIP'] = CFile::MakeFileArray($image);
                    $updateFields['LOGOTIP']['MODULE_ID'] = "sale";
                }

                CFile::SaveForDB($updateFields, 'LOGOTIP', 'sale/paysystem/logotip');
                \Bitrix\Sale\PaySystem\Manager::update($id, $updateFields);
            }
        } else {
            $flag = false;

            $params = [
                'filter' => [
                    "SERVICE_ID" => $tmpPaySystem['ID'],
                    "SERVICE_TYPE" => Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT,
                    "=CLASS_NAME" => '\\' . Sale\Services\PaySystem\Restrictions\PersonType::class,
                ],
            ];

            $dbRes = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList($params);
            $restriction = $dbRes->fetch();

            if ($restriction) {
                foreach ($restriction['PARAMS']['PERSON_TYPE_ID'] as $personTypeId) {
                    if (array_search($personTypeId, $arPaySystem['PERSON_TYPE']) === false) {
                        $arPaySystem['PERSON_TYPE'][] = $personTypeId;
                        $flag = true;
                    }
                }

                $restrictionId = $restriction['ID'];
            }

            if ($flag) {
                $fields = [
                    "SERVICE_ID" => $restrictionId,
                    "SERVICE_TYPE" => \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT,
                    "SORT" => 100,
                    "PARAMS" => [
                        'PERSON_TYPE_ID' => $arPaySystem['PERSON_TYPE'],
                    ],
                ];

                \Bitrix\Sale\Services\PaySystem\Restrictions\PersonType::save($fields, $restrictionId);
            }
        }
    }

    if (COption::GetOptionString(
            "acroweb_unibrix",
            "wizard_installed",
            "N",
            WIZARD_SITE_ID
        ) != "Y" || WIZARD_INSTALL_DEMO_DATA) {
        Sale\Notify::setNotifyDisable(true);

        if ($saleConverted15) {
            $orderPaidStatus = 'P';
            $deliveryAssembleStatus = 'DA';
            $deliveryGoodsStatus = 'DG';
            $deliveryTransportStatus = 'DT';
            $deliveryShipmentStatus = 'DS';

            $statusIds = [
                $orderPaidStatus,
                $deliveryAssembleStatus,
                $deliveryGoodsStatus,
                $deliveryTransportStatus,
                $deliveryShipmentStatus,
            ];

            $statusLanguages = [];

            foreach ($arLanguages as $langID) {
                Loc::loadLanguageFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/lib/status.php', $langID);

                foreach ($statusIds as $statusId) {
                    if ($statusName = Loc::getMessage("SALE_STATUS_{$statusId}")) {
                        $statusLanguages[$statusId] [] = [
                            'LID' => $langID,
                            'NAME' => $statusName,
                            'DESCRIPTION' => Loc::getMessage("SALE_STATUS_{$statusId}_DESCR"),
                        ];
                    }
                }
            }

            OrderStatus::install([
                'ID' => $orderPaidStatus,
                'SORT' => 150,
                'NOTIFY' => 'Y',
                'LANG' => $statusLanguages[$orderPaidStatus],
            ]);
            CSaleStatus::CreateMailTemplate($orderPaidStatus);

            DeliveryStatus::install([
                'ID' => $deliveryAssembleStatus,
                'SORT' => 310,
                'NOTIFY' => 'Y',
                'LANG' => $statusLanguages[$deliveryAssembleStatus],
            ]);

            DeliveryStatus::install([
                'ID' => $deliveryGoodsStatus,
                'SORT' => 320,
                'NOTIFY' => 'Y',
                'LANG' => $statusLanguages[$deliveryGoodsStatus],
            ]);

            DeliveryStatus::install([
                'ID' => $deliveryTransportStatus,
                'SORT' => 330,
                'NOTIFY' => 'Y',
                'LANG' => $statusLanguages[$deliveryTransportStatus],
            ]);

            DeliveryStatus::install([
                'ID' => $deliveryShipmentStatus,
                'SORT' => 340,
                'NOTIFY' => 'Y',
                'LANG' => $statusLanguages[$deliveryShipmentStatus],
            ]);
        } else {
            $bStatusP = false;
            $dbStatus = CSaleStatus::GetList(["SORT" => "ASC"]);
            while ($arStatus = $dbStatus->Fetch()) {
                $arFields = [];
                foreach ($arLanguages as $langID) {
                    WizardServices::IncludeServiceLang("step1.php", $langID);
                    $arFields["LANG"][] = [
                        "LID" => $langID,
                        "NAME" => GetMessage("WIZ_SALE_STATUS_" . $arStatus["ID"]),
                        "DESCRIPTION" => GetMessage("WIZ_SALE_STATUS_DESCRIPTION_" . $arStatus["ID"]),
                    ];
                }
                $arFields["ID"] = $arStatus["ID"];
                CSaleStatus::Update($arStatus["ID"], $arFields);
                if ($arStatus["ID"] == "P") {
                    $bStatusP = true;
                }
            }
            if (!$bStatusP) {
                $arFields = ["ID" => "P", "SORT" => 150];
                foreach ($arLanguages as $langID) {
                    WizardServices::IncludeServiceLang("step1.php", $langID);
                    $arFields["LANG"][] = [
                        "LID" => $langID,
                        "NAME" => GetMessage("WIZ_SALE_STATUS_P"),
                        "DESCRIPTION" => GetMessage("WIZ_SALE_STATUS_DESCRIPTION_P"),
                    ];
                }

                $ID = CSaleStatus::Add($arFields);
                if ($ID !== '') {
                    CSaleStatus::CreateMailTemplate($ID);
                }
            }
        }

        WizardServices::IncludeServiceLang("step1.php", $lang);

        if (Loader::includeModule("catalog")) {
            $vat = Catalog\Model\Vat::getRow([
                'select' => [
                    'ID',
                ],
                'filter' => [
                    '=EXCLUDE_VAT' => 'Y',
                ],
            ]);
            if ($vat === null) {
                Catalog\Model\Vat::add([
                    'NAME' => GetMessage('WIZ_VAT_1'),
                    'ACTIVE' => 'Y',
                    'SORT' => 100,
                    'EXCLUDE_VAT' => 'Y',
                    'RATE' => null,
                ]);
            }
            $vat = Catalog\Model\Vat::getRow([
                'select' => [
                    'ID',
                ],
                'filter' => [
                    '=EXCLUDE_VAT' => 'N',
                    '=RATE' => 0,
                ],
            ]);
            if ($vat === null) {
                Catalog\Model\Vat::add([
                    'NAME' => Loc::getMessage('WIZ_VAT_ZERO'),
                    'ACTIVE' => 'Y',
                    'SORT' => 200,
                    'EXCLUDE_VAT' => 'N',
                    'RATE' => 0,
                ]);
            }
            $vatValue = (float)GetMessage('WIZ_VAT_2_VALUE');
            $vat = Catalog\Model\Vat::getRow([
                'select' => [
                    'ID',
                ],
                'filter' => [
                    '=RATE' => $vatValue,
                ],
            ]);
            if ($vat === null) {
                Catalog\Model\Vat::add([
                    'NAME' => GetMessage('WIZ_VAT_2'),
                    'ACTIVE' => 'Y',
                    'SORT' => 300,
                    'EXCLUDE_VAT' => 'N',
                    'RATE' => $vatValue,
                ]);
            }

            $dbResultList = CCatalogGroup::GetList([], ["CODE" => "BASE"]);
            if ($arRes = $dbResultList->Fetch()) {
                $arFields = [];
                foreach ($arLanguages as $langID) {
                    WizardServices::IncludeServiceLang("step1.php", $langID);
                    $arFields["USER_LANG"][$langID] = GetMessage("WIZ_PRICE_NAME");
                }
                $arFields["BASE"] = "Y";
                if ($wizard->GetVar("installPriceBASE") == "Y") {
                    $db_res = CCatalogGroup::GetGroupsList(["CATALOG_GROUP_ID" => '1', "BUY" => "Y"]);
                    if ($ar_res = $db_res->Fetch()) {
                        $wizGroupId[] = $ar_res['GROUP_ID'];
                    }
                    $wizGroupId[] = 2;
                    $arFields["USER_GROUP"] = $wizGroupId;
                    $arFields["USER_GROUP_BUY"] = $wizGroupId;
                }
                CCatalogGroup::Update($arRes["ID"], $arFields);
            }
        }

        //making orders
        function __MakeOrder(array $arData, array $productFilter, $prdCnt = 1)
        {
            static $catalogIncluded = null;
            static $saleIncluded = null;

            if (empty($arData) || empty($productFilter)) {
                return false;
            }

            $prdCnt = (int)$prdCnt;
            if ($prdCnt < 1 || $prdCnt > 20) {
                $prdCnt = 1;
            }

            if ($catalogIncluded === null) {
                $catalogIncluded = Main\Loader::includeModule('catalog');
            }
            if (!$catalogIncluded) {
                return false;
            }
            if ($saleIncluded === null) {
                $saleIncluded = Main\Loader::includeModule('sale');
            }
            if (!$saleIncluded) {
                return false;
            }

            $arPrd = [];
            $dbItem = CIBlockElement::GetList(
                [],
                $productFilter,
                false,
                ["nTopCount" => 100],
                ["ID", "IBLOCK_ID", "NAME"]
            );
            while ($arItem = $dbItem->Fetch()) {
                $arPrd[] = $arItem;
            }
            unset($arItem, $dbItem);

            if (empty($arPrd)) {
                return false;
            }

            $registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
            /** @var Sale\Order $orderClass */
            $orderClass = $registry->getOrderClassName();

            $order = $orderClass::create($arData['SITE_ID'], $arData['USER_ID'], $arData['CURRENCY']);
            $order->setPersonTypeId($arData['PERSON_TYPE_ID']);
            if (!empty($arData['PROPS'])) {
                $propertyValues = [];
                $propertyCollection = $order->getPropertyCollection();
                /** @var Sale\PropertyValue $property */
                foreach ($propertyCollection as $property) {
                    if ($property->isUtil()) {
                        continue;
                    }

                    $propertyId = $property->getPropertyId();
                    if (!isset($arData['PROPS'][$propertyId]) && $property->isRequired()) {
                        return false;
                    }

                    $propertyValues[$propertyId] = $arData['PROPS'][$propertyId];
                    unset($propertyId);
                }
                unset($property);
                if (!empty($propertyValues)) {
                    $result = $propertyCollection->setValuesFromPost(
                        ['PROPERTIES' => $propertyValues],
                        []
                    );
                    if (!$result->isSuccess()) {
                        return false;
                    }
                    unset($result);
                }
                unset($propertyValues);
            }

            /** @var Sale\Basket $basketClass */
            $basketClass = $registry->getBasketClassName();

            $basket = $basketClass::create($arData['SITE_ID']);
            $basket->setFUserId($arData['FUSER_ID']);

            while ($prdCnt > 0) {
                $product = $arPrd[mt_rand(0, 99)];
                $item = $basket->createItem('catalog', $product['ID']);

                $result = $item->setFields([
                    'NAME' => $product['NAME'],
                    'QUANTITY' => 1,
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                ]);

                if (!$result->isSuccess()) {
                    return false;
                }

                $prdCnt--;
                unset($result, $product);
            }

            $result = $order->setBasket($basket);
            if (!$result->isSuccess()) {
                return false;
            }
            unset($result);

            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $shipmentItemCollection = $shipment->getShipmentItemCollection();

            /** @var Sale\BasketItem $basketItem */
            foreach ($order->getBasket() as $basketItem) {
                /** @var Sale\ShipmentItem $shipmentItem */
                $shipmentItem = $shipmentItemCollection->createItem($basketItem);
                $result = $shipmentItem->setQuantity($basketItem->getQuantity());
                if (!$result->isSuccess()) {
                    return false;
                }
                unset($result);
            }
            unset($basketItem);

            $emptyDeliveryServiceId = Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId();
            $result = $shipment->setField('DELIVERY_ID', $emptyDeliveryServiceId);
            if (!$result->isSuccess()) {
                return false;
            }
            unset($result);

            $paySystemObject = Sale\PaySystem\Manager::getObjectById($arData['PAY_SYSTEM_ID']);
            if ($paySystemObject === null) {
                return false;
            }
            $paymentCollection = $order->getPaymentCollection();
            /** @var \Bitrix\Sale\Payment $payment */
            $payment = $paymentCollection->createItem($paySystemObject);

            $discounts = $order->getDiscount();
            $result = $discounts->calculate();
            if (!$result->isSuccess()) {
                return false;
            }
            unset($result);

            $result = $payment->setFields([
                'SUM' => $order->getPrice(),
                'CURRENCY' => $order->getCurrency(),
            ]);
            if (!$result->isSuccess()) {
                return false;
            }
            unset($result);

            $result = $order->save();
            if (!$result->isSuccess()) {
                return false;
            }
            unset($result);

            return $order->getId();
        }

        $personType = $arGeneralInfo["personType"]["ur"];
        if (intval($arGeneralInfo["personType"]["fiz"]) > 0) {
            $personType = $arGeneralInfo["personType"]["fiz"];
        }
        if (intval($personType) <= 0) {
            $dbPerson = CSalePersonType::GetList([], ["LID" => WIZARD_SITE_ID]);
            if ($arPerson = $dbPerson->Fetch()) {
                $personType = $arPerson["ID"];
            }
        }
        $paySystem = 0;
        if (intval($arGeneralInfo["paySystem"]["cash"]) > 0) {
            $paySystem = $arGeneralInfo["paySystem"]["cash"];
        } elseif (intval($arGeneralInfo["paySystem"]["bill"]) > 0) {
            $paySystem = $arGeneralInfo["paySystem"]["bill"];
        } elseif (intval($arGeneralInfo["paySystem"]["sberbank"]) > 0) {
            $paySystem = $arGeneralInfo["paySystem"]["sberbank"];
        } elseif (intval($arGeneralInfo["paySystem"]["paypal"]) > 0) {
            $paySystem = $arGeneralInfo["paySystem"]["paypal"];
        } else {
            $dbPS = \Bitrix\Sale\PaySystem\Manager::getList([]);
            if ($arPS = $dbPS->fetch()) {
                $paySystem = $arPS["ID"];
            }
        }

        if (\Bitrix\Main\Config\Option::get('sale', 'sale_locationpro_migrated', '') == 'Y') {
            if ($location == '') {
                // get first found
                $item = \Bitrix\Sale\Location\LocationTable::getList(['limit' => 1, 'select' => ['CODE']])->fetch();
                if ($item) {
                    $location = $item['CODE'];
                }
            }
        } else {
            if (intval($location) <= 0) {
                $dbLocation = CSaleLocation::GetList(["ID" => "ASC"], ["LID" => $lang]);
                if ($arLocation = $dbLocation->Fetch()) {
                    $location = $arLocation["ID"];
                }
            }
        }

        if (empty($arGeneralInfo["properies"][$personType])) {
            $dbProp = CSaleOrderProps::GetList([], ["PERSON_TYPE_ID" => $personType]);
            while ($arProp = $dbProp->Fetch()) {
                $arGeneralInfo["properies"][$personType][$arProp["CODE"]] = $arProp;
            }
        }

        if (WIZARD_INSTALL_DEMO_DATA) {
            $db_sales = CSaleOrder::GetList(["DATE_INSERT" => "ASC"], ["LID" => WIZARD_SITE_ID], false, false, ["ID"]);
            while ($ar_sales = $db_sales->Fetch()) {
                CSaleOrder::Delete($ar_sales["ID"]);
            }
        }

        $arData = [
            "SITE_ID" => WIZARD_SITE_ID,
            "PERSON_TYPE_ID" => $personType,
            "CURRENCY" => $defCurrency,
            "USER_ID" => 1,
            "FUSER_ID" => Sale\Fuser::getIdByUserId(1),
            "PAY_SYSTEM_ID" => $paySystem,
            "PROPS" => [],
        ];
        foreach ($arGeneralInfo["properies"][$personType] as $key => $val) {
            $propertyValue = '';

            if ($key == "FIO" || $key == "CONTACT_PERSON") {
                $propertyValue = GetMessage("WIZ_ORD_FIO");
            } elseif ($key == "ADDRESS" || $key == "COMPANY_ADR") {
                $propertyValue = GetMessage("WIZ_ORD_ADR");
            } elseif ($key == "EMAIL") {
                $propertyValue = "example@example.com";
            } elseif ($key == "PHONE") {
                $propertyValue = "8 495 2312121";
            } elseif ($key == "ZIP") {
                $propertyValue = "101000";
            } elseif ($key == "LOCATION") {
                $propertyValue = $location;
            } elseif ($key == "CITY") {
                $propertyValue = $shopLocation;
            }
            $arData["PROPS"][$val["ID"]] = $propertyValue;
        }

        $productFilter = [
            "=IBLOCK_TYPE" => "offers",
            "=IBLOCK_SITE_ID" => WIZARD_SITE_ID,
            "PROPERTY_NEWPRODUCT" => false,
            "ACTIVE" => "Y",
            "CATALOG_AVAILABLE" => "Y",
            "CATALOG_TYPE" => Catalog\ProductTable::TYPE_OFFER,
        ];

        $orderID = __MakeOrder($arData, $productFilter, 3);
        if ($orderID) {
            CSaleOrder::DeliverOrder($orderID, "Y");
            CSaleOrder::PayOrder($orderID, "Y");
            CSaleOrder::StatusOrder($orderID, "F");
        }
        $orderID = __MakeOrder($arData, $productFilter, 4);
        if ($orderID) {
            CSaleOrder::DeliverOrder($orderID, "Y");
            CSaleOrder::PayOrder($orderID, "Y");
            CSaleOrder::StatusOrder($orderID, "F");
        }
        $orderID = __MakeOrder($arData, $productFilter, 2);
        if ($orderID) {
            CSaleOrder::PayOrder($orderID, "Y");
            CSaleOrder::StatusOrder($orderID, "P");
        }
        $orderID = __MakeOrder($arData, $productFilter, 1);
        $orderID = __MakeOrder($arData, $productFilter, 1);
        if ($orderID) {
            CSaleOrder::CancelOrder($orderID, "Y");
        }
        CAgent::RemoveAgent("CSaleProduct::RefreshProductList();", "sale");
        CAgent::AddAgent("CSaleProduct::RefreshProductList();", "sale", "N", 60 * 60 * 24 * 4, "", "Y");

        Sale\Notify::setNotifyDisable(false);
    }
}

$basePriceTypeId = \CCatalogGroup::GetBaseGroupId();
if ($basePriceTypeId === null) {
    $arFields = [];
    $arFields["BASE"] = "Y";
    $arFields["SORT"] = 100;
    $arFields["NAME"] = "BASE";
    $arFields["XML_ID"] = "BASE";
    $arFields["USER_GROUP"] = [1];
    $arFields["USER_GROUP_BUY"] = [1];
    $arFields["USER_LANG"] = [
        "ru" => "Розничная цена",
        "en" => "Retail price",
    ];
    CCatalogGroup::Add($arFields);
}

return true;
