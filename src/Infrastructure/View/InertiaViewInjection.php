<?php

namespace App\Infrastructure\View;

use Yii1x\Inertia\Inertia;
use Yiisoft\Yii\View\Renderer\CommonParametersInjectionInterface;

class InertiaViewInjection implements CommonParametersInjectionInterface
{
    public function __construct(
        protected Inertia $inertia,
    )
    {

    }

    public function getCommonParameters(): array
    {
        return ['inertia' => $this->inertia];
    }
}
