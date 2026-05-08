<?php

namespace App\Infrastructure\View;

use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\CommonParametersInjectionInterface;

class ViteViewInjection implements CommonParametersInjectionInterface
{
    protected WebView $view;

    public function __construct(
        protected string $viteAssetPath,
        protected string $devServerUrl = 'http://localhost:5173',
        protected bool   $isDevMode = false,
    )
    {

    }

    public function getCommonParameters(): array
    {
        return ['vite' => $this];
    }

    public function withEngine(WebView $view): static
    {
        $this->view = $view;
        return $this;
    }

    public function registerAssets($assets): void
    {
        foreach ($assets as $entryPoint) {
            if ($this->isDevMode()) {
                $this->registerAsset($entryPoint);
            } else {
                $manifestFile = $this->getManifest();
                if (isset($manifestFile[$entryPoint])) {
                    $entry = $manifestFile[$entryPoint];
                    $this->registerAsset($entry['file']);
                    foreach ($entry['imports'] ?? [] as $import) {
                        if (isset($manifestFile[$import])) {
                            $this->registerAsset($manifestFile[$import]['file']);
                        } else {
                            throw new \Exception('Entry asset not found in manifest: ' . $entryPoint);
                        }
                    }
                } else {
                    throw new \Exception('Entry asset not found in manifest: ' . $entryPoint);
                }
            }
        }
    }

    private function registerAsset(string $entryPoint): void
    {
        $extension = pathinfo($entryPoint, PATHINFO_EXTENSION);
        if ($this->isDevMode()) {
            $this->view->registerJsFile($this->getAssetUrl($entryPoint), options: ['type' => 'module']);
        } else {
            if ($extension === 'js') {
                $this->view->registerJsFile($this->getAssetUrl($entryPoint), options: ['type' => 'module']);
            } elseif ($extension === 'css') {
                $this->view->registerCssFile($this->getAssetUrl($entryPoint));
            } else {
                throw new \Exception('Unsupported asset type: ' . $extension);
            }
        }
    }

    public function getAssetUrl($filePath): string
    {
        if ($this->isDevMode()) {
            return "$this->devServerUrl/$filePath";
        } else {
            return "/build/$filePath";
        }
    }

    private function isDevMode(): bool
    {
        if ($this->isDevMode) {
            return file_exists($this->viteAssetPath . DIRECTORY_SEPARATOR . 'hot');
        }
        return false;
    }

    private function getManifest()
    {
        $manifestPath = $this->viteAssetPath . '/build/manifest.json';
        if (file_exists($manifestPath)) {
            return json_decode(file_get_contents($manifestPath), true);
        }
        return [];
    }
}
