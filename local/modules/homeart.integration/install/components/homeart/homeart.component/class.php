<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class HomeartComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('homeart.integration')) {
            return;
        }

        $this->includeComponentTemplate();
    }
}
