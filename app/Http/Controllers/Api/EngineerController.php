<?php

namespace App\Http\Controllers\Api;

use App\Exports\EngineersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\EngineerAccountantResource;
use App\Http\Resources\EngineerResource;
use App\Services\EngineerService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EngineerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $engineers = EngineerService::applyFilters($request);

        return EngineerResource::collection($engineers);
    }

    public function accountant(EngineerService $engineerService, Request $request): AnonymousResourceCollection
    {
        $engineers = $engineerService->accountantEngineers($request->all());

        return EngineerAccountantResource::collection($engineers);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $engineers = EngineerService::applyFilters($request);
        return Excel::download(new EngineersExport($engineers), 'engineers.xlsx');
    }
}
