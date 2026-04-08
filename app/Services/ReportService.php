<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportService
{
    public function getVisibleProjectsForUser(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        if ($user->hasRole('super-admin')) {
            return Project::orderBy('name')->get();
        }

        return Project::whereIn('id', $user->assignedProjectIds())
            ->orderBy('name')
            ->get();
    }

    public function getVisibleUsersForUser(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        if ($user->hasRole('super-admin')) {
            return User::orderBy('name')->get();
        }

        return User::where('id', $user->id)->orderBy('name')->get();
    }

    public function generateExpenseReport(array $filters = []): Collection
    {
        $query = Expense::query();

        if (! empty($filters['users_id'])) {
            $query->where('users_id', $filters['users_id']);
        }

        if (! empty($filters['projects_id'])) {
            $query->where('projects_id', $filters['projects_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('expense_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('expense_date', '<=', $filters['to_date']);
        }

        return $query->with(['user', 'project'])->orderByDesc('expense_date')->get();
    }

    public function generateCreditReport(array $filters = []): Collection
    {
        $query = Credit::query();

        if (! empty($filters['users_id'])) {
            $query->where('users_id', $filters['users_id']);
        }

        if (! empty($filters['projects_id'])) {
            $query->where('projects_id', $filters['projects_id']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->with(['user', 'project'])->orderByDesc('created_at')->get();
    }

    public function getProjectWiseSummary(?User $authUser, array $filters = []): Collection
    {
        $visibleProjectIds = $this->getVisibleProjectsForUser($authUser)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($visibleProjectIds)) {
            return collect();
        }

        $selectedProjectId = (int) ($filters['projects_id'] ?? 0);
        if ($selectedProjectId > 0) {
            $visibleProjectIds = in_array($selectedProjectId, $visibleProjectIds, true)
                ? [$selectedProjectId]
                : [];
        }

        if (empty($visibleProjectIds)) {
            return collect();
        }

        $projectNames = Project::query()
            ->whereIn('id', $visibleProjectIds)
            ->pluck('name', 'id');

        $expenseSummary = $this->buildExpenseSummaryQuery($authUser, $filters)
            ->whereIn('projects_id', $visibleProjectIds)
            ->groupBy('projects_id')
            ->selectRaw('projects_id, SUM(amount) as total_expense, COUNT(*) as expenses_count')
            ->get()
            ->keyBy('projects_id');

        $creditSummary = $this->buildCreditSummaryQuery($authUser, $filters)
            ->whereIn('projects_id', $visibleProjectIds)
            ->groupBy('projects_id')
            ->selectRaw('projects_id, SUM(amount) as total_credit, COUNT(*) as credits_count')
            ->get()
            ->keyBy('projects_id');

        return collect($visibleProjectIds)
            ->map(function (int $projectId) use ($projectNames, $expenseSummary, $creditSummary) {
                $expense = $expenseSummary->get($projectId);
                $credit = $creditSummary->get($projectId);
                $totalExpense = (float) ($expense->total_expense ?? 0);
                $totalCredit = (float) ($credit->total_credit ?? 0);

                return (object) [
                    'project_id' => $projectId,
                    'project_name' => $projectNames->get($projectId, 'Unknown Project'),
                    'total_expense' => round($totalExpense, 2),
                    'expenses_count' => (int) ($expense->expenses_count ?? 0),
                    'total_credit' => round($totalCredit, 2),
                    'credits_count' => (int) ($credit->credits_count ?? 0),
                    'net_amount' => round($totalCredit - $totalExpense, 2),
                    'current_balance' => round($totalCredit - $totalExpense, 2),
                ];
            })
            ->sortBy('project_name')
            ->values();
    }

    public function getUserWiseSummary(?User $authUser, array $filters = []): Collection
    {
        $visibleUsers = $this->getVisibleUsersForUser($authUser);
        $visibleUserIds = $visibleUsers->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($visibleUserIds)) {
            return collect();
        }

        $selectedUserId = (int) ($filters['users_id'] ?? 0);
        if ($selectedUserId > 0) {
            $visibleUserIds = in_array($selectedUserId, $visibleUserIds, true)
                ? [$selectedUserId]
                : [];
        }

        if (empty($visibleUserIds)) {
            return collect();
        }

        $userNames = $visibleUsers->pluck('name', 'id');
        $userBalances = $visibleUsers->pluck('amount', 'id');

        $expenseSummary = $this->buildExpenseSummaryQuery($authUser, $filters)
            ->whereIn('users_id', $visibleUserIds)
            ->groupBy('users_id')
            ->selectRaw('users_id, SUM(amount) as total_expense, COUNT(*) as expenses_count')
            ->get()
            ->keyBy('users_id');

        $creditSummary = $this->buildCreditSummaryQuery($authUser, $filters)
            ->whereIn('users_id', $visibleUserIds)
            ->groupBy('users_id')
            ->selectRaw('users_id, SUM(amount) as total_credit, COUNT(*) as credits_count')
            ->get()
            ->keyBy('users_id');

        return collect($visibleUserIds)
            ->map(function (int $userId) use ($userNames, $userBalances, $expenseSummary, $creditSummary) {
                $expense = $expenseSummary->get($userId);
                $credit = $creditSummary->get($userId);
                $totalExpense = (float) ($expense->total_expense ?? 0);
                $totalCredit = (float) ($credit->total_credit ?? 0);

                return (object) [
                    'user_id' => $userId,
                    'user_name' => $userNames->get($userId, 'Unknown User'),
                    'total_expense' => round($totalExpense, 2),
                    'expenses_count' => (int) ($expense->expenses_count ?? 0),
                    'total_credit' => round($totalCredit, 2),
                    'credits_count' => (int) ($credit->credits_count ?? 0),
                    'net_amount' => round($totalCredit - $totalExpense, 2),
                    'current_balance' => round((float) ($userBalances->get($userId) ?? 0), 2),
                ];
            })
            ->sortBy('user_name')
            ->values();
    }

    public function getTotals(Collection $projectSummary, Collection $userSummary): array
    {
        return [
            'project_total_expense' => round((float) $projectSummary->sum('total_expense'), 2),
            'project_total_credit' => round((float) $projectSummary->sum('total_credit'), 2),
            'project_net_amount' => round((float) $projectSummary->sum('net_amount'), 2),
            'user_total_expense' => round((float) $userSummary->sum('total_expense'), 2),
            'user_total_credit' => round((float) $userSummary->sum('total_credit'), 2),
            'user_net_amount' => round((float) $userSummary->sum('net_amount'), 2),
        ];
    }

    public function getTransactionTimeline(?User $authUser, array $filters = []): Collection
    {
        $expenses = $this->buildExpenseSummaryQuery($authUser, $filters)
            ->with(['project:id,name', 'user:id,name'])
            ->get()
            ->map(function (Expense $expense) {
                $timelineAt = $this->resolveTimelineAt($expense->expense_date, $expense->created_at);

                return (object) [
                    'type' => 'expense',
                    'label' => 'Expense',
                    'amount' => round((float) $expense->amount, 2),
                    'transaction_date' => $expense->expense_date,
                    'created_at' => $expense->created_at,
                    'timeline_at' => $timelineAt,
                    'project_name' => optional($expense->project)->name ?? '-',
                    'user_name' => optional($expense->user)->name ?? '-',
                ];
            });

        $credits = $this->buildCreditSummaryQuery($authUser, $filters)
            ->with(['project:id,name', 'user:id,name'])
            ->get()
            ->map(function (Credit $credit) {
                $timelineAt = $this->resolveTimelineAt($credit->credit_date, $credit->created_at);

                return (object) [
                    'type' => 'credit',
                    'label' => 'Credit',
                    'amount' => round((float) $credit->amount, 2),
                    'transaction_date' => $credit->credit_date,
                    'created_at' => $credit->created_at,
                    'timeline_at' => $timelineAt,
                    'project_name' => optional($credit->project)->name ?? '-',
                    'user_name' => optional($credit->user)->name ?? '-',
                ];
            });

        $transfers = $this->buildTransferTimelineQuery($authUser, $filters)
            ->with(['user:id,name', 'creator:id,name'])
            ->get()
            ->map(function (Transfer $transfer) {
                $timelineAt = $this->resolveTimelineAt($transfer->start_date, $transfer->created_at);
                $receiverName = optional($transfer->user)->name ?? '-';
                $senderName = optional($transfer->creator)->name ?? '-';

                return (object) [
                    'type' => 'transfer',
                    'label' => 'Transfer',
                    'amount' => round((float) $transfer->amount, 2),
                    'transaction_date' => $transfer->start_date,
                    'created_at' => $transfer->created_at,
                    'timeline_at' => $timelineAt,
                    'project_name' => '-',
                    'user_name' => $senderName . ' to ' . $receiverName,
                ];
            });

        $timeline = $expenses
            ->concat($credits)
            ->concat($transfers)
            ->sortByDesc(fn ($item) => optional($item->timeline_at)?->getTimestamp() ?? 0)
            ->values();

        if (! empty($filters['entry_type']) && $filters['entry_type'] !== 'all') {
            $timeline = $timeline
                ->where('type', $filters['entry_type'])
                ->values();
        }

        return $timeline;
    }

    public function getExpenseEntries(?User $authUser, array $filters = []): Collection
    {
        return $this->buildExpenseSummaryQuery($authUser, $filters)
            ->with(['project:id,name', 'user:id,name'])
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getCreditEntries(?User $authUser, array $filters = []): Collection
    {
        return $this->buildCreditSummaryQuery($authUser, $filters)
            ->with(['project:id,name', 'user:id,name'])
            ->orderByDesc('credit_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function generateTransferReport(array $filters = []): Collection
    {
        $query = Transfer::query();

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('start_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('start_date', '<=', $filters['to_date']);
        }

        return $query->with(['user', 'creator'])->orderByDesc('start_date')->get();
    }

    protected function buildExpenseSummaryQuery(?User $authUser, array $filters = []): Builder
    {
        $query = Expense::query();

        if ($authUser && ! $authUser->hasRole('super-admin')) {
            $query->whereIn('projects_id', $authUser->assignedProjectIds())
                ->where('users_id', $authUser->id);
        }

        if (! empty($filters['projects_id'])) {
            $query->where('projects_id', $filters['projects_id']);
        }

        if (! empty($filters['users_id'])) {
            $query->where('users_id', $filters['users_id']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('expense_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('expense_date', '<=', $filters['to_date']);
        }

        return $query;
    }

    protected function buildCreditSummaryQuery(?User $authUser, array $filters = []): Builder
    {
        $query = Credit::query();

        if ($authUser && ! $authUser->hasRole('super-admin')) {
            $query->whereIn('projects_id', $authUser->assignedProjectIds())
                ->where('users_id', $authUser->id);
        }

        if (! empty($filters['projects_id'])) {
            $query->where('projects_id', $filters['projects_id']);
        }

        if (! empty($filters['users_id'])) {
            $query->where('users_id', $filters['users_id']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('credit_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('credit_date', '<=', $filters['to_date']);
        }

        return $query;
    }

    protected function buildTransferTimelineQuery(?User $authUser, array $filters = []): Builder
    {
        $query = Transfer::query();

        if ($authUser && ! $authUser->hasRole('super-admin')) {
            $query->where(function (Builder $builder) use ($authUser) {
                $builder->where('user_id', $authUser->id)
                    ->orWhere('created_by', $authUser->id);
            });
        }

        if (! empty($filters['users_id'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('user_id', $filters['users_id'])
                    ->orWhere('created_by', $filters['users_id']);
            });
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('start_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('start_date', '<=', $filters['to_date']);
        }

        return $query;
    }

    protected function resolveTimelineAt($transactionDate, $createdAt): ?Carbon
    {
        if (! $transactionDate && ! $createdAt) {
            return null;
        }

        $datePart = $transactionDate
            ? Carbon::parse($transactionDate)->format('Y-m-d')
            : Carbon::parse($createdAt)->format('Y-m-d');

        $timePart = $createdAt
            ? Carbon::parse($createdAt)->format('H:i:s')
            : '00:00:00';

        return Carbon::parse($datePart . ' ' . $timePart);
    }
}
