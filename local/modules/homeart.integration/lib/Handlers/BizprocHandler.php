<?php

namespace Homeart\Integration\Handlers;

use Bitrix\Main\Diag\Debug;
use HomeArt\Integration\Service\WebhookSender;

/**
 * Обработчик событий модуля.
 * Класс является статическим, так как его методы должны быть доступны без создания экземпляра,
 * что соответствует требованию API событий Битрикс.
 */
class BizprocHandler
{
    /**
     * Обрабатывает события, связанные с бизнес-процессами.
     *
     * @param \Bitrix\Main\Event $event Объект события.
     */
    public static function onAfterBizprocWorkflowStart(\Bitrix\Main\Event $event): void
    {
        // Получаем параметры, переданные вместе с событием
        $parameters = $event->getParameters();
        // ID запущенного workflow (экземпляра бизнес-процесса)
        $workflowId = $parameters[0];

        // Здесь будет логика: получить данные по БП и отправить вебхук
        // Пока что просто логируем, что событие сработало (KISS - начинаем с простого)
        if (Option::get('homeart.integration', 'log_enabled') == 'Y') {
            Debug::writeToFile(
                "Запущен Бизнес-процесс с ID: " . $workflowId,
                "",
                "/local/modules/homeart.integration/logs/" . date("Y-m-d") . ".log"
            );
        }

        // -- На следующем этапе здесь будет вызов WebhookSender --
        // $webhookSender = new WebhookSender();
        // $webhookSender->send('bizproc.start', ['workflow_id' => $workflowId]);
    }

    // В будущем мы добавим сюда другие обработчики:
    // onAfterCrmLeadAdd, onAfterBizprocWorkflowComplete и т.д.
}
