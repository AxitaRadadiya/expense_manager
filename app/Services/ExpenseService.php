<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use App\Models\UserBalanceHistory;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function createExpense(User $user, array $data): Expense
    {
        return DB::transaction(function () use ($user, $data) {
            $user->refresh();

            $balanceBefore = round((float) ($user->amount ?? 0), 2);
            $expenseAmount = round((float) ($data['amount'] ?? 0), 2);
            $balanceAfter = round($balanceBefore - $expenseAmount, 2);

            $expense = Expense::create([
                ...$data,
                'users_id' => $user->id,
                'bill_path' => $data['bill_path'] ?? '',
                'bill_original_name' => $data['bill_original_name'] ?? '',
                'sub_category' => $data['sub_category'] ?? null,
                'payment_mode' => $data['payment_mode'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ]);

            $user->update([
                'amount' => $balanceAfter,
            ]);

            $this->createHistory(
                userId: $user->id,
                changeType: 'expense',
                changeAmount: -$expenseAmount,
                balanceBefore: $balanceBefore,
                balanceAfter: $balanceAfter,
                referenceType: 'expense',
                referenceId: $expense->id,
                createdBy: auth()->id(),
                note: $data['note'] ?? 'Expense added'
            );

            return $expense;
        });
    }

    private function createHistory(
        int $userId,
        string $changeType,
        float $changeAmount,
        float $balanceBefore,
        float $balanceAfter,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $createdBy = null,
        ?string $note = null
    ): void {
        UserBalanceHistory::create([
            'user_id' => $userId,
            'change_type' => $changeType,
            'change_amount' => round($changeAmount, 2),
            'balance_before' => round($balanceBefore, 2),
            'balance_after' => round($balanceAfter, 2),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $createdBy,
            'note' => $note,
        ]);
    }
}
