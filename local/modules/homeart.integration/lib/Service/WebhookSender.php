<?php

namespace HomeArt\Integration\Service;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Diag\Debug;

/**
 * Сервис для отправки данных на внешний сервер via Webhook.
 * Отвечает ТОЛЬКО за отправку HTTP-запросов. (Принцип Single Responsibility)
 */
class WebhookSender
{
    private string $webhookUrl;
    private ?string $secretKey;
    private bool $logEnabled;

    /**
     * Конструктор. Внедряем зависимости (настройки) через параметры.
     *
     * @param string|null $webhookUrl URL вебхука. Если null, берётся из настроек модуля.
     * @param string|null $secretKey Секретный ключ.
     * @param bool|null $logEnabled Флаг логирования.
     */
    public function __construct(?string $webhookUrl = null, ?string $secretKey = null, ?bool $logEnabled = null)
    {
        // Если параметры не переданы явно, загружаем из настроек модуля.
        $this->webhookUrl = $webhookUrl ?? Option::get('homeart.integration', 'webhook_url', '');
        $this->secretKey = $secretKey ?? Option::get('homeart.integration', 'secret_key', '');
        $this->logEnabled = $logEnabled ?? (Option::get('homeart.integration', 'log_enabled') === 'Y');

        // Важно: URL должен быть валидным
        if (empty($this->webhookUrl)) {
            throw new \InvalidArgumentException('Webhook URL is not configured.');
        }
    }

    /**
     * Основной метод отправки вебхука.
     * Инкапсулирует всю логику подготовки и отправки запроса.
     *
     * @param string $eventType Тип события (напр., 'bizproc.task.created').
     * @param array $payload Данные для отправки.
     * @return bool Успешна ли отправка.
     */
    public function send(string $eventType, array $payload): bool
    {
        // 1. Подготавливаем тело запроса
        $requestBody = $this->prepareRequestBody($eventType, $payload);

        // 2. Логируем исходящий запрос (если включено)
        $this->logOutgoingRequest($requestBody);

        // 3. Отправляем запрос
        $isSuccess = $this->makeHttpRequest($requestBody);

        // 4. Логируем результат
        $this->logResult($isSuccess);

        return $isSuccess;
    }

    /**
     * Подготавливает тело запроса в формате JSON.
     * Добавляет служебные поля и, опционально, подпись.
     *
     * @param string $eventType
     * @param array $payload
     * @return array
     */
    private function prepareRequestBody(string $eventType, array $payload): array
    {
        $body = [
            'event_type' => $eventType,
            'timestamp' => date('c'), // ISO 8601
            'domain' => $_SERVER['HTTP_HOST'] ?? 'cli',
            'payload' => $payload // Основные данные
        ];

        // Если задан секретный ключ, добавляем подпись для безопасности
        if (!empty($this->secretKey)) {
            $body['signature'] = $this->generateSignature($body);
        }

        return $body;
    }

    /**
     * Генерирует HMAC-SHA256 подпись для верификации на стороне получателя.
     * Защищает от подделки запросов.
     *
     * @param array $data Данные для подписи.
     * @return string
     */
    private function generateSignature(array $data): string
    {
        $dataString = Json::encode($data); // Преобразуем массив в стабильную JSON-строку
        return hash_hmac('sha256', $dataString, $this->secretKey);
    }

    /**
     * Выполняет HTTP POST запрос к внешнему серверу.
     * Использует битриксовый HttpClient.
     *
     * @param array $requestBody
     * @return bool
     */
    private function makeHttpRequest(array $requestBody): bool
    {
        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json');
        $httpClient->setHeader('User-Agent', 'HomeArt-Bitrix-Integration/1.0');

        try {
            // Пытаемся отправить JSON
            $response = $httpClient->post($this->webhookUrl, Json::encode($requestBody));
            // Успех, если статус ответа 2xx
            return ($httpClient->getStatus() >= 200 && $httpClient->getStatus() < 300);
        } catch (\Exception $e) {
            // Ловим ошибки сети, невалидный JSON и т.д.
            $this->logError($e->getMessage());
            return false;
        }
    }

    /**
     * Логирует исходящий запрос.
     *
     * @param array $requestBody
     */
    private function logOutgoingRequest(array $requestBody): void
    {
        if (!$this->logEnabled) {
            return;
        }
        $logMessage = "OUTGOING WEBHOOK:\n" . Json::encode($requestBody, JSON_PRETTY_PRINT);
        Debug::writeToFile($logMessage, '', $this->getLogPath());
    }

    /**
     * Логирует результат отправки.
     *
     * @param bool $isSuccess
     */
    private function logResult(bool $isSuccess): void
    {
        if (!$this->logEnabled) {
            return;
        }
        $status = $isSuccess ? 'SUCCESS' : 'FAILED';
        Debug::writeToFile("Webhook sending result: {$status}", '', $this->getLogPath());
    }

    /**
     * Логирует ошибку.
     *
     * @param string $errorMessage
     */
    private function logError(string $errorMessage): void
    {
        if (!$this->logEnabled) {
            return;
        }
        Debug::writeToFile("Webhook ERROR: {$errorMessage}", '', $this->getLogPath());
    }

    /**
     * Возвращает путь к файлу лога.
     * (Принцип DRY)
     *
     * @return string
     */
    private function getLogPath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/local/modules/homeart.integration/logs/webhook_' . date('Y-m-d') . '.log';
    }
}
