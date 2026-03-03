<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"]))
{
	$arAvailableThemes = array();
	$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/themes/"));
	if (is_dir($dir) && $directory = opendir($dir))
	{
		while (($file = readdir($directory)) !== false)
		{
			if ($file != "." && $file != ".." && is_dir($dir.$file))
				$arAvailableThemes[] = $file;
		}
		closedir($directory);
	}

	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$theme = COption::GetOptionString("main", "wizard_eshop_bootstrap_theme_id", "blue", SITE_ID);
			$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
	}
}
else
{
	$arParams["TEMPLATE_THEME"] = "blue";
}
$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";


foreach($arResult["ITEMS"] as $key=>$arItem){
	$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"],"ID" => $arItem["ID"]));
	if($prop_fields = $properties->GetNext())
	{
		$arResult["ITEMS"][$key]["HINT"] = $prop_fields["HINT"];
	}
}

// Естественная сортировка значений свойств
if (isset($arResult["ITEMS"]) && is_array($arResult["ITEMS"])) {
	foreach ($arResult["ITEMS"] as $key => $arItem) {
		// Пропускаем цены и свойства без значений
		if (isset($arItem["PRICE"]) || empty($arItem["VALUES"]) || !is_array($arItem["VALUES"])) {
			continue;
		}
		
		// Сортируем значения: сначала по SORT, затем по естественному порядку с учетом числовых частей
		uasort($arResult["ITEMS"][$key]["VALUES"], function($a, $b) {
			// Приоритет: сначала по полю SORT (если есть)
			if (isset($a["SORT"]) && isset($b["SORT"])) {
				$cmp = $a["SORT"] <=> $b["SORT"];
				if ($cmp !== 0) {
					return $cmp;
				}
			} elseif (isset($a["SORT"])) {
				return -1; // Элемент с SORT идет первым
			} elseif (isset($b["SORT"])) {
				return 1; // Элемент с SORT идет первым
			}
			
			// Если SORT одинаковый или отсутствует, используем естественную сортировку по VALUE
			$valueA = isset($a["VALUE"]) ? trim((string)$a["VALUE"]) : "";
			$valueB = isset($b["VALUE"]) ? trim((string)$b["VALUE"]) : "";
			
			// Извлекаем числовую часть из начала строки
			if (preg_match("/^([\\d\\,\\.]+)/", $valueA, $matchesA) && preg_match("/^([\\d\\,\\.]+)/", $valueB, $matchesB)) {
				$numA = floatval(str_replace(",", ".", $matchesA[1]));
				$numB = floatval(str_replace(",", ".", $matchesB[1]));
				$cmp = $numA <=> $numB;
				if ($cmp !== 0) {
					return $cmp;
				}
			}
			
			// Если числа равны или не найдены, используем естественную сортировку строк
			return strnatcmp($valueA, $valueB);
		});
	}
}