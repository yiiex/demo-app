<?php

namespace App\Infrastructure\View;

use Yiisoft\Router\UrlGeneratorInterface;

class SimpleNavManager implements NavManager
{
    protected ?string $title = null;
    protected array $breadcrumbs = [];

    public function __construct(protected UrlGeneratorInterface $urlGenerator)
    {

    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function addBreadcrumb(string $title, string|array $url): static
    {
        if (is_array($url) && count($url) > 0) {
            $this->addBreadcrumbRoute($title, $url[0], $url[1] ?? []);
            return $this;
        }
        $this->breadcrumbs[] = ['title' => $title, 'url' => $url];
        return $this;
    }

    public function addBreadcrumbRoute(string $title, string $routeName, array $arguments): static
    {
        $this->addBreadcrumb($title, $this->urlGenerator->generate($routeName, $arguments));
        return $this;
    }

    public function addBreadcrumbs(array $breadcrumbs): static
    {
        foreach ($breadcrumbs as $breadcrumb) {
            $this->addBreadcrumb($breadcrumb['title'], $breadcrumb['url']);
        }
        return $this;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function withHomeBreadcrumb(string $title = 'Home', string $url = '/'): static
    {
        $this->addBreadcrumb($title, $url);
        return $this;
    }
}
