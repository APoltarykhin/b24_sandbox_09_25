<?php

namespace Homeart\Integration\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;

Loc::loadMessages(__FILE__);

/**
 * Класс-контроллер для обработки action
 */
class Integration extends Controller
{

    /**
     * Конфигурация обработки действия, установливает пре-фильтры для action
     */
    public function configureActions()
    {
        return [
            'generate' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                ],
            ],
            'delete' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                ],
            ]
        ];
    }

    /**
     * Метод обработки поступившего action из js (запускает дальнейшие шаги)
     */
    public function generateAction(array $formData, array $previousData = [])
    {
        try {
            return $formData;
        } catch (SystemException $e) {
            return $this->createResponse(
                'error',
                0,
                $e->getMessage()
            );
        }
    }

    /**
     * Метод обработки поступившего action для удаления из таблиц модуля
     */
    public function deleteAction(int $report_id = null): array|int
    {
        try {
           return $report_id;
        } catch (SystemException $e) {
            return $this->createResponse(
                'error',
                1,
                $e->getMessage()
            );
        }
    }

    /**
     * Создает стандартизированный ответ
     */
    private function createResponse(
        string $status,
        int    $progress,
        string $message,
    ): array
    {
        return [
            'status' => $status,
            'progress' => $progress,
            'message' => $message,
        ];
    }
}
