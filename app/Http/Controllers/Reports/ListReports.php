<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Core\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListReports extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $reports = Report::query()->with('columns')->get();

        return JsonResource::collection($reports)->toResponse($request);
    }
}
