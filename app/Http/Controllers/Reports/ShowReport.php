<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Structure\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowReport extends Controller
{
    public function __invoke(Request $request, Report $report): JsonResponse
    {
        return JsonResource::make($report->load('columns'))->toResponse($request);
    }
}
