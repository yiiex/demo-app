<?php

namespace App\Shared\DataProvider;

use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template TKey
 * @template TValue
 * @template-extends IteratorAggregate<TKey, TValue>
 */
interface DataProvider extends JsonSerializable, IteratorAggregate, Countable
{
    /**
     * @return TValue[]
     */
    public function items(): array;

    public function totalCount(): ?int;

    public function perPage(): int;

    public function currentPage(): int;
}
