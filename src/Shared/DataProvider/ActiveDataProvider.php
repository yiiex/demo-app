<?php

namespace App\Shared\DataProvider;

use Traversable;
use Yii1x\ActiveRecord\ActiveRecord;
use Yii1x\ActiveRecord\QueryBuilder;

/**
 * @template-implements DataProvider<int, ActiveRecord>
 */
class ActiveDataProvider implements DataProvider
{
    /** @var ActiveRecord[] */
    protected array $items = [];
    protected ?int $totalCount = null;
    protected bool $fetched = false;

    public function __construct(protected QueryBuilder $queryBuilder, protected int $perPage = 10, protected int $currentPage = 1)
    {

    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function items(): array
    {
        $this->fetchData();
        return $this->items;
    }

    public function totalCount(): ?int
    {
        $this->fetchData();
        return $this->totalCount;
    }

    public function fetchData($refresh = false): void
    {
        if ($this->fetched && !$refresh) {
            return;
        }
        $countBuilder = clone $this->queryBuilder;
        $this->totalCount = $countBuilder->count();
        $this->items = $this->queryBuilder
            ->offset($this->perPage * ($this->currentPage - 1))
            ->limit($this->perPage)
            ->findAll();
        $this->fetched = true;
    }

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->items(),
            'meta' => [
                'total' => $this->totalCount(),
                'page' => $this->currentPage,
                'perPage' => $this->perPage,
                'count' => $this->count(),
            ],
        ];
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items());
    }

    public function count(): int
    {
        return count($this->items());
    }
}
