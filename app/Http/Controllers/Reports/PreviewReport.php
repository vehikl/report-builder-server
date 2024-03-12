<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\PreviewReportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Benchmark;

class PreviewReport extends Controller
{
    public function __invoke(PreviewReportRequest $request): JsonResponse
    {
        [$report, $reportDuration] = Benchmark::value(fn () => $request->report());
        [$preview, $previewDuration] = Benchmark::value(fn () => $report->preview($request->input('sort')));

        logger('PreviewReport', [
            'report' => $reportDuration,
            'preview' => $previewDuration,
            'total' => $reportDuration + $previewDuration,
        ]);

        return JsonResource::make($preview)->toResponse($request);
    }
}
