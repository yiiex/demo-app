<?php

namespace App\Debug;

use App\Models\DebugLog;
use Yiisoft\Router\Debug\RouterCollector;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\Debug\Collector\Web\RequestCollector;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

class DatabaseDebugStorage implements StorageInterface
{
    public function __construct(protected CurrentUser $user)
    {

    }

    public function read(string $type, ?string $id): array
    {
        return [];
    }

    public function write(string $id, array $data, array $objectsMap, array $summary): void
    {
        $debug = new DebugLog('insert');
        $debug->setAttributes([
            'id' => $id,
            'user_id' => $this->user->getId(),
            'data' => $this->sanitizeData($data),
        ], false);
        $debug->save(false);
    }

    public function clear(): void
    {
        DebugLog::model()
            ->getDbConnection()
            ->createCommand()
            ->truncateTable(DebugLog::model()->tableName());
    }

    private function sanitizeData(array $data): array
    {
        if (isset($data[RequestCollector::class])) {
            $data[RequestCollector::class]['responseRaw'] = null;
        }
        if (isset($data[RouterCollector::class])) {
            unset(
                $data[RouterCollector::class]['routes'],
                $data[RouterCollector::class]['routesTree'],
            );
            unset($data[RouterCollector::class]['routeTime']);
        }
        return $data;
    }
}
