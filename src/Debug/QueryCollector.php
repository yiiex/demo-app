<?php

namespace App\Debug;

use Psr\EventDispatcher\ListenerProviderInterface;
use Yii1x\ActiveRecord\Events\EndQueryEvent;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;

class QueryCollector implements CollectorInterface
{
    use CollectorTrait;

    protected array $queries = [];

    public function __construct(protected ListenerProviderInterface $listenerProvider)
    {

    }

    public function listenQuery(EndQueryEvent $event): void
    {
        $this->queries[] = [
            'event' => $event,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20),
        ];
    }

    public function getCollected(): array
    {
        return [
            'queries' => array_map(function (array $data) {
                $q = $data['event'];
                return [
                    'id' => $q->queryId,
                    'sql' => $q->sql,
                    'sql_formatted' => $this->formatSql($q->sql),
                    'params' => $q->params,
                    'duration' => round($q->duration * 1000, 2),
                    'connection' => $q->connectionName,
                    'trace' => $this->formatTrace($data['trace']),
                ];
            }, $this->queries),
        ];
    }

    private function formatSql(string $sql): string
    {
        // Нормализация
        $sql = trim(preg_replace('/\s+/', ' ', $sql));

        // Склеиваем составные ключевые слова
        $sql = preg_replace('/LEFT\s+OUTER\s+JOIN/i', 'LEFT OUTER JOIN', $sql);
        $sql = preg_replace('/RIGHT\s+OUTER\s+JOIN/i', 'RIGHT OUTER JOIN', $sql);
        $sql = preg_replace('/INNER\s+JOIN/i', 'INNER JOIN', $sql);
        $sql = preg_replace('/CROSS\s+JOIN/i', 'CROSS JOIN', $sql);
        $sql = preg_replace('/ORDER\s+BY/i', 'ORDER BY', $sql);
        $sql = preg_replace('/GROUP\s+BY/i', 'GROUP BY', $sql);

        // Убираем пробелы внутри скобок для защиты от разрыва
        $sql = preg_replace('/\(\s+/', '(', $sql);
        $sql = preg_replace('/\s+\)/', ')', $sql);

        // Основное форматирование
        $sql = preg_replace([
            '/\bSELECT\b/i',
            '/,\s*/',
            '/\bFROM\b/i',
            '/\b(LEFT OUTER JOIN|RIGHT OUTER JOIN|INNER JOIN|CROSS JOIN|LEFT JOIN|RIGHT JOIN|JOIN)\b/i',
            '/\bON\b/i',
            '/\bWHERE\b/i',
            '/\bAND\b/i',
            '/\bOR\b/i',
            '/\bORDER BY\b/i',
            '/\bGROUP BY\b/i',
        ], [
            "SELECT\n    ",
            ",\n    ",
            "\nFROM\n    ",
            "\n$1\n    ",
            "\n        ON ",
            "\nWHERE\n    ",
            "\n        AND",
            "\n        OR",
            "\nORDER BY\n    ",
            "\nGROUP BY\n    ",
        ], $sql);

        // Добавляем пробелы после запятых внутри скобок
        $sql = preg_replace_callback('/\(([^)]+)\)/', function ($m) {
            return '(' . preg_replace('/\s*,\s*/', ', ', $m[1]) . ')';
        }, $sql);

        // Финальная очистка
        $sql = preg_replace("/\n{3,}/", "\n\n", $sql);
        $sql = preg_replace('/[ \t]+$/m', '', $sql);

        return trim($sql);
    }

    private function formatTrace(array $trace): array
    {
        return array_values(array_filter(array_map(function (array $item) {
            // Пропускаем служебные вызовы
            $file = $item['file'] ?? '';
            if (str_contains($file, 'QueryCollector.php')
                || str_contains($file, 'vendor/yiisoft')
                || str_contains($file, 'DbCommand.php')
                || str_contains($file, 'DbConnection.php')
            ) {
                return null;
            }

            return [
                'file' => $file ? $this->relativePath($file) : '',
                'line' => $item['line'] ?? 0,
                'function' => $item['function'] ?? '',
                'class' => $item['class'] ?? '',
                'type' => $item['type'] ?? '',
            ];
        }, $trace)));
    }

    private function relativePath(string $path): string
    {
        $basePath = dirname(__DIR__, 2) . '/'; // корень проекта
        return str_starts_with($path, $basePath)
            ? substr($path, strlen($basePath))
            : $path;
    }

    private function reset(): void
    {
        $this->queries = [];
    }
}
