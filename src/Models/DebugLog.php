<?php

namespace App\Models;

use App\Debug\QueryCollector;
use App\Shared\Behaviors\CastBehavior;
use App\Shared\Caster\Casts\JsonCast;
use Yiisoft\Router\Debug\RouterCollector;
use Yiisoft\Yii\Debug\Collector\Web\RequestCollector;
use Yiisoft\Yii\Debug\Collector\Web\WebAppInfoCollector;

class DebugLog extends BaseModel
{
    public function behaviors(): array
    {
        return [
            CastBehavior::class => [
                'class' => CastBehavior::class,
                'attributes' => [
                    'data' => JsonCast::class,
                ],
            ],
        ];
    }

    public function tableName(): string
    {
        return 'debug_log';
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'queries' => $this->data[QueryCollector::class]['queries'] ?? [],
            'currentRoute' => $this->data[RouterCollector::class]['currentRoute'] ?? null,
            'app' => $this->data[WebAppInfoCollector::class] ?? null,
            'request' => $this->data[RequestCollector::class] ?? null,
        ];
    }
}
