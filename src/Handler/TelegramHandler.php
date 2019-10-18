<?php


namespace bazoonchik\CriticalAlertsBundle\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramHandler extends AbstractProcessingHandler
{
    private const BOT_API = 'https://api.telegram.org/bot';

    /**
     * Telegram bot access token provided by BotFather.
     * Create telegram bot with https://telegram.me/BotFather and use access token from it.
     * @var string
     */
    private $apiKey;
    /**
     * Telegram channel name.
     * Since to start with '@' symbol as prefix.
     * @var string
     */
    private $channel;
    /**
     * @var HttpClientInterface
     */
    private $telegramClient;

    /**
     * @param string $apiKey  Telegram bot access token provided by BotFather
     * @param string $channel Telegram channel name
     * @inheritDoc
     */
    public function __construct(
        string $apiKey,
        string $channel,
        $level = Logger::DEBUG,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->apiKey = $apiKey;
        $this->channel = $channel;
        $this->level = $level;
        $this->bubble = $bubble;

        $this->telegramClient = HttpClient::create([
            'base_uri' => self::BOT_API,
            'verify_peer' => true
        ]);
    }

    /**
     * @param array $record
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function write(array $record): void
    {
        $this->send($record['formatted']);
    }

    /**
     * Send request to @link https://api.telegram.org/bot on SendMessage action.
     * @param string $message
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function send(string $message): void
    {
        $response = $this->telegramClient->request('POST', '/SendMessage', [
            'body' => [
                'text' => $message,
                'chat_id' => $this->channel,
            ]
        ]);

        $result = json_decode($response->getContent(false));

        if ($result['ok'] === false) {
            throw new RuntimeException('Telegram API error. Description: ' . $result['description']);
        }
    }
}