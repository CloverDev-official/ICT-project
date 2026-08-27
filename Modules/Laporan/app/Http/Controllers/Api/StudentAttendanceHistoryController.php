<?php

namespace Modules\Laporan\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Laporan\Http\Requests\Api\StudentAttendanceHistoryRequest;
use Modules\Laporan\Http\Resources\Api\StudentAttendanceHistoryCollection;
use Modules\Laporan\Http\Resources\Api\StudentAttendanceSummaryResource;
use Modules\Laporan\Services\StudentAttendance\StudentAttendanceHistoryQuery;

class StudentAttendanceHistoryController extends Controller
{
    public function index(
        StudentAttendanceHistoryRequest $request,
        StudentAttendanceHistoryQuery $query,
    ): JsonResponse {
        /** @var User $user */
        $user = $request->user('web');
        $paginator = $query->paginate($request->validated(), $user);

        return (new StudentAttendanceHistoryCollection($paginator))->response();
    }

    public function summary(
        StudentAttendanceHistoryRequest $request,
        StudentAttendanceHistoryQuery $query,
    ): JsonResponse {
        /** @var User $user */
        $user = $request->user('web');
        $summary = $query->summary($request->validated(), $user);

        return (new StudentAttendanceSummaryResource($summary))->response();
    }
}
