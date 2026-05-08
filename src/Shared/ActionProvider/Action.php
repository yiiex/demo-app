<?php

namespace App\Shared\ActionProvider;

use Closure;
use JsonSerializable;
use Yiisoft\User\CurrentUser;

class Action implements JsonSerializable
{
    protected string $title;
    protected string|Closure|null $url {
        get => $this->url instanceof Closure ? ($this->url)($this->model, $this->user) : $this->url;
    }
    protected string $method = 'GET';
    protected string|Closure|null $confirm = null {
        get => $this->confirm instanceof Closure ? ($this->confirm)($this->model, $this->user) : $this->confirm;
    }
    protected string|Closure|null $process = null {
        get => $this->process instanceof Closure ? ($this->process)($this->model, $this->user) : $this->process;
    }
    protected bool $async = false;
    protected string $variant = 'default';
    protected Closure|bool $can = true {
        get => $this->can instanceof Closure ? ($this->can)($this->model, $this->user) : $this->can;
    }
    protected ?object $model = null;
    protected ?CurrentUser $user = null;
    protected array $attributes = [];

    public function __construct(
        public readonly string $name
    )
    {
    }

    public static function new(string $name): static
    {
        return new static($name);
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function url(string|Closure $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function method(string $method): static
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function confirm(string|Closure $confirm): static
    {
        $this->confirm = $confirm;
        return $this;
    }

    public function process(string|Closure $process): static
    {
        $this->process = $process;
        return $this;
    }

    public function async(bool $async = true): static
    {
        $this->async = $async;
        return $this;
    }

    public function variant(string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }

    public function visible(Closure|bool $can): static
    {
        $this->can = $can;
        return $this;
    }

    public function attribute(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function model(mixed $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function user(mixed $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function can(): bool
    {
        return $this->can;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'title' => $this->title,
            'url' => $this->url,
            'method' => $this->method,
            'confirm' => $this->confirm,
            'process' => $this->process,
            'async' => $this->async ?: null,
            'variant' => $this->variant,
            'can' => $this->can,
            'attributes' => !empty($this->attributes) ? $this->attributes : null,
        ], fn($value) => $value !== null);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
