<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage("HOMEART_PATH_NAME"),
    "DESCRIPTION" => "",
    "COMPLEX" => "N",
    "PATH" => [
        "ID" => Loc::getMessage("HOMEART_COMPONENT_PATH_ID"),
        "NAME" => Loc::getMessage("HOMEART_PATH_NAME")
    ],
];
