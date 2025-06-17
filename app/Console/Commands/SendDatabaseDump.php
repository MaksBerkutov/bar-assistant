<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDatabaseDump extends Command
{
    protected $signature = 'db:send-dump';
    protected $description = 'Создать дамп БД и отправить его в Telegram';

    public function handle()
    {
        $filename = 'dump_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path("app/{$filename}");

        // Команда дампа
        $db = config('database.connections.mysql');
        $command = "mysqldump -u{$db['username']} -p\"{$db['password']}\" -h{$db['host']} {$db['database']} > {$path}";
        exec($command);

        // Отправка в Telegram
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $response = \Http::attach('document', file_get_contents($path), $filename)
            ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => 'Дамп базы данных ' . now(),
            ]);

        if ($response->successful()) {
            $this->info('Дамп успешно отправлен в Telegram');
        } else {
            $this->error('Ошибка при отправке: ' . $response->body());
        }

        unlink($path); // удалить после отправки
    }
}
