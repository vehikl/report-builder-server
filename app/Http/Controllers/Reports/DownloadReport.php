<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\PreviewReportRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Carbon;
use XLSXWriter;

class DownloadReport extends Controller
{
    public function __invoke(PreviewReportRequest $request): Response
    {
        $writer = new XLSXWriter();

        $data = $request->report()->spreadsheet($request->input('sort'));

        $writeDuration = Benchmark::measure(function () use ($request, $writer, $data) {
            $writer->writeSheet($data, $request->report()->name);
        });

        [$contents, $contentsDuration] = Benchmark::value(fn () => $writer->writeToString());

        logger('spreadsheet_build', [
            'write' => $writeDuration,
            'read' => $contentsDuration,
            'total' => $writeDuration + $contentsDuration,
        ]);

        $fileName = $request->report()->name.' '.Carbon::now()->format('Y-m-d H:i:s').'.xlsx';

        return new Response($contents, 200, [
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'private',
            'X-File-Name' => $fileName,
        ]);
    }
}
