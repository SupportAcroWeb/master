<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
	"NAME" => GetMessage("ACROWEB_CMP_WIDGETS_NAME"),
	"DESCRIPTION" => GetMessage("ACROWEB_CMP_WIDGETS_DESC"),
	"ICON" => "",
	"SORT" => 10,
	"PATH" => [
		"ID" => "acroweb",
        "NAME" => "Acroweb",
		"CHILD" => [
			"ID" => "acroweb_content",
			"NAME" => GetMessage("ACROWEB_CMP_MENU_CONTENT_TITLE"),
			"SORT" => 30,
        ]
    ]
];