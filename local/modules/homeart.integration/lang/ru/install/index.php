<?php defined("B_PROLOG_INCLUDED") and (B_PROLOG_INCLUDED === true) or die();

$moduleLang = strtolower(basename(dirname(dirname(dirname(__DIR__)))));

$MESS[$moduleLang . "_MODULE_NAME"] = "Homeart: Интеграции";
$MESS[$moduleLang . "_MODULE_DESCRIPTION"] = "Модуль для создания различных интеграций";
$MESS[$moduleLang . "_PARTNER_NAME"] = "Homeart";
$MESS[$moduleLang . "_PARTNER_URI"] = "https://home.ru";
$MESS[$moduleLang . "_INSTALL_TITLE"] = "Установка модуля \"".$MESS[$moduleLang . "_MODULE_NAME"]."\"";
$MESS[$moduleLang . "_INSTALL_ERROR"] = "Произошла ошибка при установке \"".$MESS[$moduleLang . "_MODULE_NAME"]."\"";
$MESS[$moduleLang . "_UNINSTALL_TITLE"] = "Удаление модуля \"".$MESS[$moduleLang . "_MODULE_NAME"]."\"";
$MESS[$moduleLang . "_INSTALL_ERROR_WRONG_VERSION"] = "Версия ядра системы не соответствует требованиям модуля, обновите систему и попробуйте установить модуль еще раз";

// Фразы для файла unstep1
$MESS["MY_INTEGRATION_SAVE"] = 'Сохранить или удалить таблицы модуля';
$MESS["MY_INTEGRATION_NO_DEL"] = 'Сохранить таблицы';
$MESS["MY_INTEGRATION_NEXT_STEP"] = 'Продолжить';
$MESS["MY_INTEGRATION_UNINSTALL_TITLE"] = 'Успешно удалён';