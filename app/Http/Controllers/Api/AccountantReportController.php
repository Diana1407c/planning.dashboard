<?php

namespace App\Http\Controllers\Api;

use App\Exports\AccountantExport;
use App\Http\Controllers\Controller;
use App\Matrix\AccountantMatrix;
use App\Models\PlannedHour;
use App\Services\HolidayService;
use App\Support\Filters\PlannedHoursFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccountantReportController extends Controller
{
    public function __construct(protected HolidayService $holidayService)
    {}

    public function index(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $requestData['planable_type'] = PlannedHour::ENGINEER_TYPE;
        $requestData['period_type'] = PlannedHour::MONTH_PERIOD_TYPE;

        $filter = PlannedHoursFilter::fromArray($requestData);
        $hoursCount = $this->holidayService->monthWorkHours($filter->period->from, $filter->period->to);

        $matrix = new AccountantMatrix($filter, $hoursCount);

        return response()->json([
            'table' => $matrix->matrix(),
            'hours_count' => $hoursCount,
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $requestData = $request->all();
        $requestData['planable_type'] = PlannedHour::ENGINEER_TYPE;
        $requestData['period_type'] = PlannedHour::MONTH_PERIOD_TYPE;

        $filter = PlannedHoursFilter::fromArray($requestData);
        $hoursCount = $this->holidayService->monthWorkHours($filter->period->from, $filter->period->to);

        $matrix = new AccountantMatrix($filter, $hoursCount);

        $fileName = $filter->period->from->toDateString() . '_monthly_planning.xlsx';

        return Excel::download(new AccountantExport($matrix->matrix()), $fileName);
    }
}
