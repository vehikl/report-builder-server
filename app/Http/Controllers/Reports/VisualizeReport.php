<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisualizeReport extends Controller
{
    public function __invoke(Request $request, Report $report): JsonResponse
    {
        $employees = Employee::query()->with($report->relations())->get();

        return JsonResource::make($report->getVisualization($employees))->toResponse($request);
    }
}
