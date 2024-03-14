<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Core\ReportCollection;
use App\Models\Client\User;
use App\Models\Core\Field;
use App\Models\Core\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListReports extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::query()->with(['permissions', 'roles.permissions'])->first();

        $reports = Report::query()->with(['columns', 'columns.report'])->get();

        return (new ReportCollection($reports))->for($user, Field::query()->get())->toResponse($request);
    }
}
