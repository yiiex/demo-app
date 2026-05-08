<?php

namespace App\Infrastructure\Middlewares;

use App\Infrastructure\View\NavManager;
use App\Shared\ApplicationParams;
use Yii1x\Inertia\Inertia;
use Yiisoft\User\CurrentUser;

class InertiaMiddleware extends \Yii1x\Inertia\InertiaMiddleware
{
    protected string $rootView {
        get => 'layouts/app.php';
    }

    public function __construct(
        protected Inertia     $inertia,
        protected CurrentUser $currentUser,
        protected NavManager  $navManager,
        protected ApplicationParams $params,
    )
    {
        parent::__construct($inertia);
    }

    protected function beforeAction(): void
    {
        $this->inertia->share([
            'php' => PHP_VERSION,
            'appName' => $this->params->name,
            'auth' => [
                'check' => !$this->currentUser->isGuest(),
                'user' => $this->currentUser->getIdentity(),
            ],
            'title' => fn() => $this->navManager->getTitle(),
            'breadcrumbs' => fn() => $this->navManager->getBreadcrumbs(),
        ]);
    }
}
