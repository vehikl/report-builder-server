<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Core\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowReportPreview extends Controller
{
    public function __invoke(Request $request, Report $report): JsonResponse
    {
        return JsonResource::make($report->preview(null))->toResponse($request);
    }
}
