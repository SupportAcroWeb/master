<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

CModule::IncludeModule('iblock');
CModule::IncludeModule('acroweb.mage');

use Acroweb\Mage\Import;

$siteID = $wizard->GetVar("siteID");
$import = new Import($siteID);
$import->importAll();


return true;
