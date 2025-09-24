<?php

use Bitrix\Main\EventManager;
use HomeArt\Integration\Handlers\BizprocHandler;

// Регистрируем наши обработчики событий
$eventManager = EventManager::getInstance();
$moduleId = 'homeart.integration';

// Подписываемся на событие "После запуска задания бизнес-процесса"
$eventManager->RegisterEventHandler(
    'bizproc', // Модуль, в котором происходит нужное событие
    'OnTaskAdd', // Конкретное событие
    $moduleId,
    BizprocHandler::class, // Класс
    'onAfterBizprocTaskAdd' // Класс и метод-обработчик события
);

// В будущем мы добавим здесь регистрацию для CRM и других событий.
