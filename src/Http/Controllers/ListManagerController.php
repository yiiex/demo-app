<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Shared\ListProvider\ListProviderManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Status;

final class ListManagerController
{
    public function fetch(ListProviderManager $manager, ServerRequestInterface $request, ResponseHelper $response): ResponseInterface
    {
        $params = $request->getParsedBody();
        if (($provider = $params['provider'] ?? null) && $manager->has($provider)) {
            return $response->json([
                'success' => true,
                'data' => $manager->get($provider)->fetch($params),
            ]);
        }
        return $response->json([
            'success' => false,
            'message' => 'Invalid provider',
        ], Status::UNPROCESSABLE_ENTITY);
    }
}
