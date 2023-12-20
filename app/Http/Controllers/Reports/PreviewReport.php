<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\PreviewReportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class PreviewReport extends Controller
{
    public function __invoke(PreviewReportRequest $request):JsonResponse
    {
        $report = $request->report();

        $ModelClass = config('models')[$report->entity->table];

        $models = $ModelClass::query()->with($report->relations())->get();

        return JsonResource::make($report->preview($models))->toResponse($request);
    }
}
