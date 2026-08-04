<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PortfolioContentService;
use Illuminate\Http\JsonResponse;

class PortfolioContentController extends Controller
{
    public function __invoke(PortfolioContentService $portfolioContent): JsonResponse
    {
        return response()
            ->json([
                'data' => $portfolioContent->get(),
            ])
            ->setPublic()
            ->setMaxAge(300);
    }
}
