<?php

namespace App\Shared\Filter;

use JsonSerializable;
use ReflectionClass;
use Yii1x\ActiveRecord\Model\Model;
use Yii1x\ActiveRecord\QueryBuilder;

abstract class Filter extends Model implements JsonSerializable
{
    protected array $useScopes = [];
    protected array $with = [];

    protected array $filters = [] {
        get {
            if (!$this->filters) {
                $reflection = new ReflectionClass($this);
                foreach ($reflection->getMethods() as $method) {
                    foreach ($method->getAttributes(FilterMethod::class) as $attribute) {
                        $instance = $attribute->newInstance();
                        $instance->name = $method->getName();
                        $this->filters[$instance->name] = $instance;
                    }
                }
            }
            return $this->filters;
        }
    }

    public function rules(): array
    {
        $rules = [];
        foreach ($this->filters as $filter) {
            $rules = array_merge($rules, $filter->rules);
        }
        return $rules;
    }

    public function apply(QueryBuilder $queryBuilder): static
    {
        foreach ($this->filters as $filter) {
            $this->{$filter->name}($queryBuilder);
        }
        if ($this->useScopes) {
            $queryBuilder->scopes($this->useScopes);
        }
        if ($this->with) {
            $queryBuilder->with($this->with);
        }
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'model' => $this->getAttributes(),
            'attributeLabels' => $this->attributeLabels(),
            'safeAttributes' => $this->getSafeAttributeNames(),
        ];
    }
}
