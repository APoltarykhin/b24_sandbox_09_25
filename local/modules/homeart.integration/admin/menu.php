<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight("homeart.integration") != "D") {
    $aMenu = [
        "parent_menu" => "global_menu_services",
        "section" => "my_integration",
        "sort" => 1000,
        "text" => Loc::getMessage('MY_INTEGRATION_PAGE_TITLE'),
        "title" => Loc::getMessage('MY_INTEGRATION_PAGE_TITLE'),
        "url" => "my_integration.php?lang=" . LANGUAGE_ID,
        "icon" => "form_menu_icon",
        "page_icon" => "form_page_icon",
    ];

    $aMenu['items'][] = [
        'text' => Loc::getMessage('MY_INTEGRATION_ITEM_PAGE_TITLE'),
        'title' => Loc::getMessage('MY_INTEGRATION_ITEM_PAGE_TITLE'),
        'url' => '/bitrix/admin/my_item_integration.php',
    ];
    
    return $aMenu;
}
return false;
