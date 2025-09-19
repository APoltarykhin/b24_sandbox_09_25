<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\UI\Selector\EntitySelector;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\SystemException;
use Bitrix\Main\Page\Asset;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

if (!Loader::includeModule('homeart.integration')) {
    throw new SystemException('Module homeart.integration is not installed');
}

Loc::loadMessages(__FILE__);
Extension::load(['ui.progressbar', 'ui.forms', 'ui.entity-selector', 'ui.buttons', 'ui.datetime', 'ui.notification']);

// Получаем текущий запрос
$request = Context::getCurrent()->getRequest();

$APPLICATION->ShowHead();
$APPLICATION->SetTitle(Loc::getMessage("PT_CUSTOM_ADMIN_STAT_TASKS_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<!-- Начало основного блока -->

<div class="container-fluid">
    <div class="row">
        <!-- Карточка в левом верхнем углу -->
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Веб-хуки</h5>
                    <p class="card-text">Настройка веб-хука</p>
                    <a href="/bitrix/admin/my_item_integration.php" class="btn btn-primary">
                        Перейти
                    </a>
                </div>
            </div>
        </div>

        <!-- Пустые ячейки для остальных 5 столбцов -->
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4"></div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4"></div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4"></div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4"></div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4"></div>
    </div>
</div>


<!-- Основной контент заканчивается тут -->


<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
