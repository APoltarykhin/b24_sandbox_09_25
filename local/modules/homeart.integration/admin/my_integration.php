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

<?php
// Определяем адрес перехода
$url = '/bitrix/admin/my_item_integration.php';

// Формируем элемент управления с помощью классов UI Button
echo '<div style="margin: auto; width: fit-content;">';
CBitrixComponent::includeComponentClass('bitrix:main.ui.button');

// Создаем компонент Button
CBitrixUiButton::render([
    'id' => 'my_settings_button',
    'type' => CBitrixUiButton::TYPE_PRIMARY,
    'label' => Loc::getMessage('MY_BUTTON_LABEL'),
]);

// Скрипт-обработчик для навигации по клику
?>
<script type="text/javascript">
document.getElementById('my_settings_button').addEventListener('click', function () {
    top.BX.adminSidePanelHelper.open('<?=$url?>');
});
</script>

<?php echo '</div>'; ?>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
