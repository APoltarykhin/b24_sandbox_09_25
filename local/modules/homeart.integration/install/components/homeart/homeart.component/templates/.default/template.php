<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);
Extension::load([
    'ui.buttons',
    'ui.buttons.icons',
    'ui.icons',
    'ui.fonts.opensans',
    'ui.alerts',
    'ui.grid',
    'main.lazyload',
    'ui.filter',
]);

$APPLICATION->SetTitle(Loc::getMessage("HOMEART_COMPONENT_TEMPLATE_TITLE"));
