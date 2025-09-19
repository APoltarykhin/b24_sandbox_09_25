<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\UI\Selector\EntitySelector;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\SystemException;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;

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

// ПОДКЛЮЧЕНИЕ ПРОЛОГА
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (!Loader::includeModule('homeart.integration')) {
    throw new \Bitrix\Main\SystemException('Module homeart.integration is not installed');
}

Loc::loadMessages(__FILE__);
Extension::load(['ui.forms', 'ui.buttons', 'ui.notification']);

$module_id = 'homeart.integration';
$request = Context::getCurrent()->getRequest();

// Обработка сохранения формы
if ($request->isPost() && check_bitrix_sessid() && $request->getPost('save')) {
    $webhookUrl = trim($request->getPost('webhook_url'));
    $secretKey = trim($request->getPost('secret_key'));
    $logEnabled = $request->getPost('log_enabled') === 'Y' ? 'Y' : 'N';

// Валидация URL
    if (!empty($webhookUrl) && !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
        $errorMessage = Loc::getMessage("PT_CUSTOM_ADMIN_STAT_INVALID_URL");
        \CAdminMessage::ShowMessage([
            'MESSAGE' => $errorMessage,
            'TYPE' => 'ERROR'
        ]);
    } else {
// Сохранение настроек
        Option::set($module_id, "webhook_url", $webhookUrl);
        Option::set($module_id, "secret_key", $secretKey);
        Option::set($module_id, "log_enabled", $logEnabled);

// Показать сообщение об успехе
        \CAdminMessage::ShowNote(Loc::getMessage("PT_CUSTOM_ADMIN_STAT_SETTINGS_SAVED"));
    }
}

// Получение текущих значений
$currentWebhookUrl = Option::get($module_id, "webhook_url", "");
$currentSecretKey = Option::get($module_id, "secret_key", "");
$currentLogEnabled = Option::get($module_id, "log_enabled", "N");

$APPLICATION->SetTitle(Loc::getMessage("PT_CUSTOM_ADMIN_STAT_TASKS_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// Создаем табы меню
$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("PT_CUSTOM_ADMIN_STAT_WEBHOOK_SETTINGS"),
        "TITLE" => Loc::getMessage("PT_CUSTOM_ADMIN_STAT_WEBHOOK_SETTINGS_TITLE"),
    ],
    [
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("PT_CUSTOM_ADMIN_STAT_ADDITIONAL_SETTINGS"),
        "TITLE" => Loc::getMessage("PT_CUSTOM_ADMIN_STAT_ADDITIONAL_SETTINGS_TITLE"),
    ],
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<form method="post"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($module_id) ?>&lang=<?= LANGUAGE_ID ?>"
      id="settings-form">
    <?= bitrix_sessid_post() ?>

    <?php $tabControl->BeginNextTab(); ?>

    <tr>
        <td width="40%">
            <label for="webhook_url">
                <?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_WEBHOOK_URL") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="url"
                   name="webhook_url"
                   id="webhook_url"
                   value="<?= htmlspecialcharsbx($currentWebhookUrl) ?>"
                   placeholder="https://example.com/webhook"
                   class="adm-input"
                   style="width: 300px;">
            <div class="ui-ctl-label-text" style="color: #828b95; font-size: 12px; margin-top: 5px;">
                <?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_WEBHOOK_URL_HINT") ?>
            </div>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="secret_key">
                <?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_SECRET_KEY") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="password"
                   name="secret_key"
                   id="secret_key"
                   value="<?= htmlspecialcharsbx($currentSecretKey) ?>"
                   placeholder="<?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_SECRET_KEY_PLACEHOLDER") ?>"
                   class="adm-input"
                   style="width: 300px;">
            <div class="ui-ctl-label-text" style="color: #828b95; font-size: 12px; margin-top: 5px;">
                <?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_SECRET_KEY_HINT") ?>
            </div>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="log_enabled">
                <?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_LOG_ENABLED") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="checkbox"
                   name="log_enabled"
                   id="log_enabled"
                   value="Y"
                <?= $currentLogEnabled === 'Y' ? 'checked' : '' ?>
                   class="adm-designed-checkbox">
            <label for="log_enabled" class="adm-designed-checkbox-label"></label>
        </td>
    </tr>

    <?php $tabControl->BeginNextTab(); ?>

    <tr>
        <td colspan="2">
            <div style="padding: 20px; text-align: center;">
                <h3><?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_ADDITIONAL_FEATURES") ?></h3>
                <p><?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_ADDITIONAL_FEATURES_DESC") ?></p>
            </div>
        </td>
    </tr>

    <?php $tabControl->Buttons(); ?>

    <input type="submit"
           name="save"
           value="<?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_SAVE_BUTTON") ?>"
           class="ui-btn ui-btn-success">

    <input type="button"
           value="<?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_CANCEL_BUTTON") ?>"
           onclick="window.location.reload()"
           class="ui-btn ui-btn-link">

    <?php $tabControl->End(); ?>
</form>

<script>
    // Валидация формы перед отправкой
    document.getElementById('settings-form').addEventListener('submit', function (e) {
        var webhookUrl = document.getElementById('webhook_url').value;

        if (webhookUrl && !isValidUrl(webhookUrl)) {
            e.preventDefault();
            showNotification('<?= Loc::getMessage("PT_CUSTOM_ADMIN_STAT_INVALID_URL") ?>', 'error');
            return false;
        }

        return true;
    });

    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    function showNotification(message, type) {
        if (typeof BX.UI.Notification !== 'undefined') {
            BX.UI.Notification.Center.notify({
                content: message,
                autoHideDelay: 3000
            });
        } else {
            alert(message);
        }
    }
</script>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
