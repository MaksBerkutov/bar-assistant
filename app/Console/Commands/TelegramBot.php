<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramBot extends Command
{
    protected $signature = 'bot:telegram';
    protected $description = 'Запускает Telegram-бота в режиме polling';

    protected Api $telegram;
    protected array $allowedIds;

    public function __construct()
    {
        parent::__construct();
        $this->telegram = new Api(config('telegram.bots.default.token'));
        $this->allowedIds = explode(',', env('TELEGRAM_ALLOWED_IDS'));
    }

    public function handle()
    {
        $this->info('Telegram Bot polling запущен');
        $lastUpdateId = 0;

        while (true) {
            $updates = $this->telegram->getUpdates([
                'offset' => $lastUpdateId + 1,
                'timeout' => 5,
            ]);

            foreach ($updates as $update) {
                $lastUpdateId = $update->getUpdateId(); // очень важно

                $message = $update->getMessage();
                if (!$message) continue;

                $chatId = $message->getChat()->getId();
                if (!in_array($chatId, $this->allowedIds)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'Доступ запрещён',
                    ]);
                    continue;
                }

                $text = trim($message->getText());

                match(true) {
                    str_starts_with($text, '/start') => $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Добро пожаловать! Доступные команды:\n/report\n/logs\n/errors\n/dump\n/edit",
                    ]),
                    str_starts_with($text, '/report') => $this->sendReport($chatId),
                    str_starts_with($text, '/logs') => $this->sendLogs($chatId),
                    str_starts_with($text, '/errors') => $this->sendErrors($chatId),
                    str_starts_with($text, '/dump') => $this->sendEncryptedDump($chatId),
                    default => $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Неизвестная команда",
                    ]),
                };
            }

            sleep(1);
        }
    }

    protected function sendReport($chatId)
    {
        $count = DB::table('orders')->whereDate('created_at', today())->count();
        $sum = DB::table('orders')->whereDate('created_at', today())->sum('total_price');
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "Сегодня оформлено заказов: $count\nНа сумму: $sum грн",
        ]);
    }

    protected function sendLogs($chatId)
    {
        $path = storage_path('logs/laravel.log');
        if (!file_exists($path)) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Файл логов не найден.',
            ]);
            return;
        }

        $this->telegram->sendDocument([
            'chat_id' => $chatId,
            'document' => fopen($path, 'r'),
            'caption' => 'Последние логи',
        ]);
    }

    protected function sendErrors($chatId)
    {
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Файл логов не найден.',
            ]);
            return;
        }

        $this->telegram->sendDocument([
            'chat_id' => $chatId,
            'document' => fopen($logPath, 'r'),
            'caption' => 'Логи Laravel',
        ]);
    }

    protected function sendEncryptedDump($chatId)
    {
        $filename = 'dump-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $encrypted = storage_path("app/{$filename}.enc");

        // Создание дампа
        $command = "mysqldump -u root -pYOUR_PASSWORD_HERE your_db_name > /tmp/{$filename}";
        exec($command);

        // Шифруем
        $key = substr(hash('sha256', env('APP_KEY')), 0, 32);
        $plaintext = file_get_contents("/tmp/{$filename}");
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, substr($key, 0, 16));
        file_put_contents($encrypted, $ciphertext);

        $this->telegram->sendDocument([
            'chat_id' => $chatId,
            'document' => fopen($encrypted, 'r'),
            'caption' => 'Зашифрованный дамп БД',
        ]);

        unlink("/tmp/{$filename}");
        unlink($encrypted);
    }
}
