<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
    return "";

$strReturn = '';

$strReturn .= '<nav aria-label="breadcrumb" class="breadcrumbs">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    
    if($index > 0) {
        $strReturn .= '<span aria-hidden="true">—</span>';
    }

    if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
    {
        $strReturn .= '
            <a href="'.$arResult[$index]["LINK"].'" class="breadcrumbs__item">
                '.$title.'
            </a>';
    }
    else
    {
        $strReturn .= '
            <span aria-current="page" class="breadcrumbs__item">
                '.$title.'
            </span>';
    }
}

$strReturn .= '</nav>';

return $strReturn;