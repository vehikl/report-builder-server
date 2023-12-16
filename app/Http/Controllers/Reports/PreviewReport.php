<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\PreviewReportRequest;
use App\Models\Employee;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreviewReport extends Controller
{
    public function __invoke(PreviewReportRequest $request):JsonResponse
    {
        $report = $request->report();

        $employees = Employee::query()->with($report->relations())->get();

        return JsonResource::make($report->preview($employees))->toResponse($request);
    }
}
