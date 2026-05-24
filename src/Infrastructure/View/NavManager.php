<?php

namespace App\Infrastructure\View;

interface NavManager
{
    public function setTitle(string $title): static;
    public function getTitle(): ?string;
    public function addBreadcrumb(string $title, string|array $url): static;
    public function addBreadcrumbRoute(string $title, string $routeName, array $arguments = []): static;
    public function addBreadcrumbs(array $breadcrumbs): static;
    public function getBreadcrumbs(): array;
    public function withHomeBreadcrumb(string $title = 'Home', string $url = '/'): static;
}
