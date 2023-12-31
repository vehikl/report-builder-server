<?php

use App\Http\Controllers\Entities\ListEntities;
use App\Http\Controllers\Reports\PreviewReport;
use App\Http\Controllers\Reports\ShowReportPreview;
use App\Http\Controllers\Reports\ListReports;
use App\Http\Controllers\Reports\ShowReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

Route::get('entities', ListEntities::class);

Route::prefix('reports')->group(function () {
    Route::get('/', ListReports::class);
    Route::get('{report}', ShowReport::class);
    Route::get('{report}/preview', ShowReportPreview::class);
});

Route::get('preview-report', PreviewReport::class);
