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

Loc::loadMessages(__FILE__);
Extension::load(['ui.progressbar', 'ui.forms', 'ui.entity-selector', 'ui.buttons', 'ui.datetime', 'ui.notification']);

// Получаем текущий запрос
$request = Context::getCurrent()->getRequest();

$APPLICATION->ShowHead();
$APPLICATION->SetTitle(Loc::getMessage("PT_CUSTOM_ADMIN_STAT_TASKS_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<!-- Начало основного блока -->

<div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
    <a id="settingsButton" href="<?=htmlspecialcharsbx('/bitrix/admin/my_item_integration.php'); ?>" class="btn btn-primary" role="button"><?=Loc::getMessage('SETTINGS_WEBHOOKS'); ?></a>
</div>

<!-- Обработка клика через JQuery -->
<script type="text/javascript">
BX.ready(function(){
    $('#settingsButton').on('click', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        top.BX.adminSidePanelHelper.open(link); // Открытие страницы в боковой панели админки
    });
});
</script>

<!-- Основной контент заканчивается тут -->


<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
