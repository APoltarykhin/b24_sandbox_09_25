<?php

use Bitrix\Main\EventManager;
use HomeArt\Integration\Handlers\BizprocHandler;

// Регистрируем наши обработчики событий
$eventManager = EventManager::getInstance();

// Подписываемся на событие "После запуска бизнес-процесса"
$eventManager->addEventHandler(
    'bizproc', // Модуль, в котором происходит событие
    'OnAfterBizprocWorkflowStart', // Конкретное событие
    [BizprocHandler::class, 'onAfterBizprocWorkflowStart'] // Класс и метод для вызова
);

// В будущем мы добавим здесь регистрацию для CRM и других событий.
