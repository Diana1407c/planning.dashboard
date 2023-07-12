<?php

namespace App\Http\Controllers\Api;

use App\Exports\TeamworkExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamworkRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TeamworkController extends Controller
{
    public function index(TeamworkRequest $request): JsonResponse
    {
        return response()->json(ReportService::teamworkData($request));
    }

    public function export(TeamworkRequest $request): BinaryFileResponse
    {
        return Excel::download(new TeamworkExport(ReportService::teamworkData($request)), 'teamwork.xlsx');
    }
}
