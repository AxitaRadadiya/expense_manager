<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Reports\ReportExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function index(Request $request): View
    {
        $authUser = auth()->user();
        $this->authorizeReportAccess($authUser);
        $filters = $this->validatedFilters($request);

        $projects = $this->reportService->getVisibleProjectsForUser($authUser);
        $users = $this->reportService->getVisibleUsersForUser($authUser);
        $projectSummary = $this->reportService->getProjectWiseSummary($authUser, $filters);
        $userSummary = $this->reportService->getUserWiseSummary($authUser, $filters);
        $totals = $this->reportService->getTotals($projectSummary, $userSummary);

        // Build download query from filters
        $downloadQuery = array_filter([
            'projects_id' => $filters['projects_id'] ?? '',
            'users_id' => $filters['users_id'] ?? '',
            'from_date' => $filters['from_date'] ?? '',
            'to_date' => $filters['to_date'] ?? '',
            'entry_type' => $filters['entry_type'] ?? 'all',
        ], fn ($value) => filled($value) && $value !== 'all');

        return view('admin.report.index', compact(
            'filters',
            'projects',
            'users',
            'projectSummary',
            'userSummary',
            'totals',
            'downloadQuery'
        ));
    }

    public function download(Request $request): BinaryFileResponse
    {
        $authUser = auth()->user();
        $this->authorizeReportAccess($authUser);
        $filters = $this->validatedFilters($request);

        $projects = $this->reportService->getVisibleProjectsForUser($authUser)->keyBy('id');
        $users = $this->reportService->getVisibleUsersForUser($authUser)->keyBy('id');

        $selectedProjectName = ! empty($filters['projects_id'])
            ? optional($projects->get((int) $filters['projects_id']))->name
            : null;
        $selectedUserName = ! empty($filters['users_id'])
            ? optional($users->get((int) $filters['users_id']))->name
            : null;

        $filenameParts = array_filter([
            'report',
            $selectedUserName ? str($selectedUserName)->slug()->value() : null,
            $selectedProjectName ? str($selectedProjectName)->slug()->value() : null,
            now()->format('Y-m-d-His'),
        ]);

        $filename = implode('-', $filenameParts) . '.xlsx';

        return Excel::download(
            new ReportExport($authUser, $filters, $this->reportService),
            $filename
        );
    }

    protected function authorizeReportAccess($authUser): void
    {
        abort_unless(
            $authUser && ($authUser->can('expense-view') || $authUser->can('credit-view')),
            Response::HTTP_FORBIDDEN
        );
    }

    protected function validatedFilters(Request $request): array
    {
        return $request->validate([
            'projects_id' => 'nullable|integer|exists:projects,id',
            'users_id' => 'nullable|integer|exists:users,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'entry_type' => 'nullable|in:all,expense,credit,transfer',
        ]);
    }

    public function timelineList(Request $request)
    {
        $authUser = auth()->user();
        $this->authorizeReportAccess($authUser);
        $filters = $this->validatedFilters($request);

        $timeline = $this->reportService->getTransactionTimeline($authUser, $filters);

        // Show transfers only when user filter is applied
        if (empty($filters['users_id'])) {
            $timeline = $timeline->filter(function ($item) {
                return $item->type !== 'transfer';
            })->values();
        }

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $needle = mb_strtolower($search);
            $timeline = $timeline->filter(function ($item) use ($needle) {
                return str_contains(mb_strtolower((string) $item->label), $needle)
                    || str_contains(mb_strtolower((string) $item->project_name), $needle)
                    || str_contains(mb_strtolower((string) $item->user_name), $needle)
                    || str_contains(mb_strtolower((string) $item->amount), $needle)
                    || str_contains(mb_strtolower((string) optional($item->timeline_at)?->format('d M Y h:i A')), $needle);
            })->values();
        }

        $recordsTotal = $timeline->count();
        $recordsFiltered = $recordsTotal;
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $pagedTimeline = $length > 0 ? $timeline->slice($start, $length)->values() : $timeline->values();

        $data = $pagedTimeline->map(function ($item, $index) use ($start) {
            $badgeClass = match ($item->type) {
                'credit' => 'is-credit',
                'transfer' => 'is-transfer',
                default => 'is-expense',
            };

            $amountClass = $item->type === 'expense' ? 'text-danger' : 'text-success';
            $amountPrefix = $item->type === 'expense' ? '-' : '+';

            return [
                'id' => $start + $index + 1,
                'type' => '<span class="timeline-badge ' . $badgeClass . '">' . e($item->label) . '</span>',
                'date' => optional($item->timeline_at)?->format('d M Y') ?? '-',
                'time' => optional($item->timeline_at)?->format('h:i A') ?? '-',
                'project' => e($item->project_name),
                'user' => e($item->user_name),
                'amount' => '<span class="font-weight-bold ' . $amountClass . '">' . $amountPrefix . ' Rs. ' . number_format((float) $item->amount, 2) . '</span>',
            ];
        })->all();

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
