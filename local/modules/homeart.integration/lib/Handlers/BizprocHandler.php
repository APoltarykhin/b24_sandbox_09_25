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

        if (!$lastTask) {
            // Если задание почему-то не найдено, выходим
            return;
        }

        // 2. Подготавливаем полезную нагрузку (Payload)
        // Мы не отправляем ВСЕ поля, а только самые важные (KISS, безопасность)
        $webhookPayload = [
            'task_id' => (int) $lastTask['ID'],
            'workflow_id' => $lastTask['WORKFLOW_ID'],
            'activity_name' => $lastTask['ACTIVITY_NAME'],
            'task_created_date' => $lastTask['CREATED_DATE'],
            'task_status' => $lastTask['STATUS'],
        ];

        // 3. Пытаемся отправить вебхук
        try {
            $webhookSender = new WebhookSender(); // Настройки подтянутся из модуля автоматически
            $isSent = $webhookSender->send('bizproc.task.created', $webhookPayload);

            // Можно залогировать результат, если нужно
            // if (!$isSent) { ... }

        } catch (\InvalidArgumentException $e) {
            // Ошибка конфигурации (например, не указан URL вебхука)
            // В продакшене можно залогировать ошибку, но не прерывать выполнение скрипта
            // file_put_contents(...$e->getMessage()...);
        } catch (\Exception $e) {
            // Любая другая ошибка
            // file_put_contents(...$e->getMessage()...);
        }
    }
}
