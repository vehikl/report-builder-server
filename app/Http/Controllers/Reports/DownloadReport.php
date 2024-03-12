<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\PreviewReportRequest;
use App\Models\Core\Column;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use XLSXWriter;

class DownloadReport extends Controller
{
    public function __invoke(PreviewReportRequest $request): StreamedResponse
    {
        logger('-------');

        ini_set('max_execution_time', 600);
        $report = $request->report();

        /** @var XLSXWriter $writer */
        $writer = null;

        $writeDuration = Benchmark::measure(function () use ($request, $report, &$writer) {
            //            $writer = $this->generateFromQuery($report->getQuery($request->input('sort')), $report->name, $report->columns);
            $writer = $this->generateFromData($report->spreadsheet($request->input('sort')), $report->name);
        });

        //        [$contents, $contentsDuration] = Benchmark::value(fn () => $writer->writeToString());

        logger('DownloadReport', [
            'write_to_xlsx' => $writeDuration,
            //            'read' => $contentsDuration,
            //            'total' => $writeDuration + $contentsDuration,
        ]);

        $fileName = $report->name.' '.Carbon::now()->format('Y-m-d H:i:s').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->writeToStdOut();
        }, $fileName, [
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'private',
            'X-File-Name' => $fileName,
        ]);
    }

    /** @param  Collection<int, Column>  $columns */
    private function generateFromQuery(Builder $query, string $name, Collection $columns): XLSXWriter
    {
        $writer = new XLSXWriter();

        $writer->writeSheetRow($name, $columns->map(fn (Column $column) => $column->name)->toArray());

        $query->chunk(1000, function (Collection $records, int $i) use ($name, $writer) {
            foreach ($records as $record) {
                $writer->writeSheetRow($name, (array) $record);
            }
            logger("end chunk $i");
        });

        return $writer;
    }

    private function generateFromData(array $data, string $name): XLSXWriter
    {
        $writer = new XLSXWriter();

        $writer->writeSheetHeader($name, $data['headers']);

        foreach ($data['records'] as $record) {
            $writer->writeSheetRow($name, $record);
        }

        return $writer;
    }
}
