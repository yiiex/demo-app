<?php

namespace App\Shared\Behaviors;

use App\Shared\Caster\Contracts\CasterInterface;
use Yii1x\ActiveRecord\ActiveRecordBehavior;
use Yii1x\ActiveRecord\Model\{Event, ModelEvent};
use Yii1x\ActiveRecord\ORMContext;

class CastBehavior extends ActiveRecordBehavior
{
    public array $attributes = [];
    public ?CasterInterface $caster = null {
        get {
            if (!$this->caster) {
                $container = ORMContext::container();
                if (!$container || !$container->has(CasterInterface::class)) {
                    $this->caster = null;
                }
                $this->caster = $container->get(CasterInterface::class);
            }
            return $this->caster;
        }
    }

    public function afterFind(Event $event): void
    {
        if ($this->attributes) {
            $this->prepareAttributes('get');
        }
    }

    public function beforeSave(ModelEvent $event): void
    {
        if ($event->isValid && $this->attributes) {
            $this->prepareAttributes('set');
        }
    }

    public function afterSave(Event $event): void
    {
        if ($this->attributes) {
            $this->prepareAttributes('get');
        }
    }

    protected function prepareAttributes(string $method): void
    {
        if (!$this->caster) {
            return;
        }
        $prepared = [];
        $relations = $this->owner->getMetaData()->relations;
        foreach ($this->attributes as $key => $cast) {
            if (!isset($relations[$key]) || $this->owner->hasRelated($key)) {
                $prepared[$key] = $this->owner->$key;
            }
        }
        if ($prepared) {
            foreach ($this->caster->applyMany($prepared, $method, $this->attributes) as $key => $value) {
                $this->owner->$key = $value;
            }
        }
    }

    public function cast(mixed $value, string $cast, string $method): mixed
    {
        return $this->caster->apply($value, $cast, $method);
    }

    public function decode(string $attribute): mixed
    {
        return $this->cast($this->owner->$attribute, $this->ensureAttributeCast($attribute), 'get');
    }

    public function encode(string $attribute): mixed
    {
        return $this->cast($this->owner->$attribute, $this->ensureAttributeCast($attribute), 'set');
    }

    protected function ensureAttributeCast(string $attribute): string
    {
        if (!isset($this->attributes[$attribute])) {
            throw new \Exception("Attribute '$attribute' not found in cast config");
        }
        return $this->attributes[$attribute];
    }
}
