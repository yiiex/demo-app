<?php

namespace App\Infrastructure\Form;

use App\Http\Helpers\ResponseHelper;
use App\Shared\Caster\Contracts\CasterInterface;
use Closure;
use Psr\Http\Message\ResponseInterface;
use Yii1x\ActiveRecord\Model\Model;
use Yiisoft\Http\Status;

class FormData implements \JsonSerializable
{
    protected Model $model;
    protected array $casts = [];

    public function __construct(
        protected readonly CasterInterface $caster,
        protected readonly ResponseHelper  $response,
    )
    {

    }

    public function setModel(Model $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function setAttributes(array $attributes): static
    {
        $this->checkModel();
        if ($this->casts && $attributes) {
            $attributes = array_merge($attributes, $this->caster->applyMany($attributes, 'set', $this->casts));
        }
        $this->model->setAttributes($attributes);
        return $this;
    }

    public function getAttributes(): array
    {
        $this->checkModel();
        $attributes = $this->model->getAttributes();
        if ($this->casts) {
            $attributes = array_merge($attributes, $this->caster->applyMany($attributes, 'get', $this->casts));
        }
        return $attributes;
    }

    public function validate(): bool
    {
        $this->checkModel();
        return $this->model->validate();
    }

    public function validateWithResponse(Closure $onSuccess): ResponseInterface
    {
        if ($this->validate()) {
            $result = $onSuccess($this->model);
            return $result instanceof ResponseInterface ? $result : $this->response->json($result);
        } else {
            return $this->response->json([
                'success' => false,
                'errors' => $this->model->getErrors(),
                'message' => 'Validation failed',
            ], Status::UNPROCESSABLE_ENTITY);
        }
    }

    protected function checkModel(): void
    {
        if (!isset($this->model)) {
            throw new \RuntimeException(sprintf(
                '%s requires a model to operate. Call setModel() before using this object.',
                static::class
            ));
        }
    }

    public function jsonSerialize(): array
    {
        $this->checkModel();
        return [
            'model' => $this->getAttributes(),
            'attributeLabels' => $this->model->attributeLabels(),
            'safeAttributes' => $this->model->getSafeAttributeNames(),
            'errors' => $this->model->getErrors(),
        ];
    }
}
