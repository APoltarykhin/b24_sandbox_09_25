<?php

namespace Homeart\Integration\Handlers;

use Bitrix\Main\Loader;
use Bitrix\Main\Diag\Debug;
use Bitrix\Bizproc\Workflow\Task\TaskTable;
use HomeArt\Integration\Service\WebhookSender;


/**
 * Обработчик событий модуля.
 */
class BizprocHandler
{

    /**
     * Обрабатывает событие создания нового задания (таска) Бизнес-процесса - создание лида.
     *
     //     * @param array $arFields Массив с полями создаваемого задания.
     //     * @param array $arParams Дополнительные параметры.
     */
    public static function onAfterBizprocTaskAdd(): void
    {
        // Проверяем, что модуль bizproc подключен
        if (!Loader::includeModule('bizproc')) {
            return;
        }

        // Строим запрос через класс таблицы b_bp_task
        $query = TaskTable::query()
            ->setSelect(['*']) // Выбираем все поля
            ->setOrder(['CREATED_DATE' => 'DESC']) // Сортируем по дате создания
            ->setLimit(1); // Ограничиваем одну запись

        // Выполняем запрос
        $result = $query->exec();

        // Получаем последнюю запись
        $lastTask = $result->fetch();

        // Логируем результат
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/log_test_ARTART.txt',
            "=== Логи ===\n" .
            var_export($lastTask, true) . "\n\n",
            FILE_APPEND
        );


//        // Логируем факт срабатывания события
//        if (Option::get('homeart.integration', 'log_enabled') == 'Y') {
//            $logMessage = sprintf(
//                "Создано задание БП. ID задания: %s, ID workflow (БП): %s, Активность: %s, Параметры: %s",
//                $arFields['ID'] ?? 'N/A',
//                $arFields['WORKFLOW_ID'] ?? 'N/A',
//                    $arFields['IS_INLINE'] ?? 'N/A' ? 'inline' : 'обычная',
//                print_r($arParams, true)
//            );
//
//            Debug::writeToFile(
//                $logMessage,
//                "",
//                $_SERVER['DOCUMENT_ROOT'] . "/local/modules/homeart.integration/logs/" . date("Y-m-d") . ".log"
//            );
//        }

        // -- На следующем этапе здесь будет вызов WebhookSender --
        // $webhookData = [
        //    'event_type' => 'bizproc.task.created',
        //    'task_id' => $arFields['ID'],
        //    'workflow_id' => $arFields['WORKFLOW_ID'],
        //    'activity' => $arFields['ACTIVITY'],
        //    'activity_name' => $arFields['ACTIVITY_NAME']
        // ];
        // $webhookSender->send($webhookData);
    }
}
