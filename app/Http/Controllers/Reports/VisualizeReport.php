<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Column;
use App\Models\Employee;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisualizeReport extends Controller
{
    public function __invoke(Request $request, Report $report): JsonResponse
    {
        $relations = $report->columns
            ->map(fn(Column $column) => array_slice(explode('.', $column->expression), 0, -1))
            ->filter()
            ->map(fn(array $value) => implode('.', $value))
            ->toArray();

        $records = Employee::query()->with($relations)->get()
            ->map(fn(Employee $employee) => $report->columns
                ->mapWithKeys(fn(Column $column) => [
                    $column->name => $this->getByPath($employee, $column->expression)
                ]));

        $visualization = [
            'headers' => $report->columns->pluck('name'),
            'records' => $records,
        ];

        return JsonResource::make($visualization)->toResponse($request);
    }

    function getByPath(mixed $data, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } else if (is_object($current) && isset($current->$key)) {
                $current = $current->$key;
            } else {
                return null;
            }
        }

        return $current;
    }
}
