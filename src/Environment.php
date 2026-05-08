<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class Environment
{
    public const DEV = 'dev';
    public const TEST = 'test';
    public const PROD = 'prod';
    private array $values = [];
    private array $defaults = [
        'APP_ENV' => 'prod',
        'APP_DEBUG' => false,
        'APP_C3' => false,
        'APP_HOST_PATH' => null,
    ];

    private array $allowedValues = [
        'APP_ENV' => ['dev', 'test', 'prod'],
    ];

    public function __construct(?array $env = null)
    {
        $this->load($env ?? $_ENV);
    }

    /**
     * Загружает переменные из .env файла
     */
    public function loadFromFile(string $filePath): self
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Env file not found: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];

        foreach ($lines as $line) {
            // Пропускаем комментарии
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Разбираем строку вида KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Убираем кавычки, если есть
                $value = trim($value, '"\'');

                $env[$key] = $value;

                // Сразу устанавливаем в окружение PHP
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        // Загружаем найденные значения
        $this->load($env);

        return $this;
    }

    /**
     * Загружает переменные из массива (обычно $_ENV или getenv())
     */
    public function load(array $env): void
    {
        foreach ($this->defaults as $key => $default) {
            $value = $env[$key] ?? $default;

            // Приводим к правильному типу
            $this->values[$key] = match ($key) {
                'APP_DEBUG', 'APP_C3' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                'APP_ENV' => $this->validateEnv($value),
                default => $value,
            };
        }
    }

    /**
     * Универсальный метод для получения любой переменной
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key]
            ?? getenv($key)
            ?? $_ENV[$key]
            ?? $default;
    }

    /**
     * Получить все переменные окружения
     */
    public function all(): array
    {
        return $this->values;
    }

    /**
     * Проверить, существует ли переменная
     */
    public function has(string $key): bool
    {
        return isset($this->values[$key])
            || getenv($key) !== false
            || isset($_ENV[$key]);
    }

    /**
     * Установить переменную (для тестов)
     */
    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * Магический метод для удобства
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * Проверка окружения (удобные методы-хелперы)
     */
    public function isDev(): bool
    {
        return $this->get('APP_ENV') === 'dev';
    }

    public function isTest(): bool
    {
        return $this->get('APP_ENV') === 'test';
    }

    public function isProd(): bool
    {
        return $this->get('APP_ENV') === 'prod';
    }

    public function isDebug(): bool
    {
        return (bool) $this->get('APP_DEBUG', false);
    }

    private function validateEnv(string $value): string
    {
        if (!in_array($value, $this->allowedValues['APP_ENV'], true)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid APP_ENV "%s". Allowed: %s',
                    $value,
                    implode(', ', $this->allowedValues['APP_ENV'])
                )
            );
        }
        return $value;
    }
}
