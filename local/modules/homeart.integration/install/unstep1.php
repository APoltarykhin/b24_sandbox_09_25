<?php
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

// Устанавливаем язык локализации
Loc::loadMessages(__FILE__);

// Получаем текущий URL
$request = Application::getInstance()->getContext()->getRequest();
$action = (new Uri($request->getRequestUri()))->getUri();
?>
<form action="<?= $action ?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="id" value="homeart.integration">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <p><?= Loc::getMessage("MY_INTEGRATION_SAVE") ?></p>
    <p>
        <input type="checkbox" name="savedata" id="savedata" value="Y" checked>
        <label for="savedata"><?= Loc::getMessage("MY_INTEGRATIONC_NO_DEL") ?></label>
    </p>
    <input type="submit" name="inst" value="<?= Loc::getMessage("MY_INTEGRATIONC_NEXT_STEP") ?>">
</form>