<?php

use Bitrix\Main\EventManager;
use HomeArt\Integration\Handlers\BizprocHandler;

// Регистрируем наши обработчики событий
$eventManager = EventManager::getInstance();

// Подписываемся на событие "После запуска задания бизнес-процесса"
$eventManager->addEventHandler(
    'bizproc', // Модуль, в котором происходит событие
    'OnTaskAdd', // Конкретное событие
    [BizprocHandler::class, 'onAfterBizprocTaskAdd'] // Класс и метод для вызова
);

// В будущем мы добавим здесь регистрацию для CRM и других событий.
