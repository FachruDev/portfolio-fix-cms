<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PortfolioContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioContentController extends Controller
{
    public function __construct(private readonly PortfolioContentService $portfolioContent) {}

    public function __invoke(): JsonResponse
    {
        return $this->respond($this->portfolioContent->bootstrap());
    }

    public function global(): JsonResponse
    {
        return $this->respond($this->portfolioContent->global());
    }

    public function landingPage(): JsonResponse
    {
        return $this->respond($this->portfolioContent->landingPage());
    }

    public function aboutPage(): JsonResponse
    {
        return $this->respond($this->portfolioContent->aboutPage());
    }

    public function projectsPage(): JsonResponse
    {
        return $this->respond($this->portfolioContent->projectsPage());
    }

    public function projectCategories(): JsonResponse
    {
        return $this->respond($this->portfolioContent->projectCategories());
    }

    public function projects(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'category' => ['sometimes', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:24'],
        ]);

        if ($request->has('featured')) {
            $filters['featured'] = $request->boolean('featured');
        }

        return $this->respond($this->portfolioContent->projects($filters), wrap: false);
    }

    public function project(string $slug): JsonResponse
    {
        return $this->respond($this->portfolioContent->project($slug));
    }

    public function notesPage(): JsonResponse
    {
        return $this->respond($this->portfolioContent->notesPage());
    }

    public function notesCategories(): JsonResponse
    {
        return $this->respond($this->portfolioContent->notesCategories());
    }

    public function notes(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'category' => ['sometimes', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:24'],
        ]);

        if ($request->has('featured')) {
            $filters['featured'] = $request->boolean('featured');
        }

        return $this->respond($this->portfolioContent->notes($filters), wrap: false);
    }

    public function note(string $slug): JsonResponse
    {
        return $this->respond($this->portfolioContent->note($slug));
    }

    public function contactInformation(): JsonResponse
    {
        return $this->respond($this->portfolioContent->contactInformation());
    }

    public function mediaAssets(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'collection' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:48'],
        ]);

        return $this->respond($this->portfolioContent->mediaAssets($filters), wrap: false);
    }

    /**
     * @param  array<string, mixed>|array<int, array<string, mixed>>|null  $data
     */
    private function respond(?array $data, bool $wrap = true): JsonResponse
    {
        return response()
            ->json($wrap ? ['data' => $data] : $data)
            ->setPublic()
            ->setMaxAge(300);
    }
}
