<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Models\DebugLog;
use App\Policies\DebugLogPolicy;

class DebugController
{
    public function show(
        #[ModelContext(DebugLog::class, 'debug', scenario: 'view', policy: DebugLogPolicy::class)]
        DebugLog       $debug,
        ResponseHelper $response,
    ): \Psr\Http\Message\ResponseInterface
    {
        return $response->json(['debug' => $debug], headers: ['X-DEBUG-IGNORE' => 'true']);
    }
}
