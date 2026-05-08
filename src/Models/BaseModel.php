<?php

namespace App\Models;

use JsonSerializable;
use ReturnTypeWillChange;
use Yii1x\ActiveRecord\ActiveRecord;
use Yii1x\ActiveRecord\QueryBuilder;

abstract class BaseModel extends ActiveRecord implements JsonSerializable
{
    protected array $secureAttributes = [];

    public QueryBuilder $query {
        get => $query ?? new QueryBuilder($this, $this->dbCriteria);
    }

    public function beforeSave(): bool
    {
        if ($this->isNewRecord && isset($this->metaData->columns['created_at'])) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        if (isset($this->metaData->columns['updated_at'])) {
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave();
    }

    public function whenLoaded(string $relation, mixed $default = null)
    {
        return $this->hasRelated($relation) ? $this->$relation : $default;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_diff_key($this->attributes, array_flip($this->secureAttributes));
    }
}
