<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserBalanceHistory;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    public function getDirectBalance(User $user): float
    {
        return (float) ($user->amount ?? 0);
    }

    public function getTransferBalance(User $user): float
    {
        return 0.0;
    }

    public function getCurrentBalance(User $user): float
    {
        return $this->getDirectBalance($user);
    }

    public function recordOpeningBalance(User $user, float $amount, ?int $actorId = null, ?string $note = null): void
    {
        if ($amount == 0.0) {
            return;
        }

        UserBalanceHistory::create([
            'user_id' => $user->id,
            'change_type' => 'opening',
            'change_amount' => $amount,
            'balance_before' => 0,
            'balance_after' => $amount,
            'reference_type' => 'user',
            'reference_id' => $user->id,
            'created_by' => $actorId,
            'note' => $note ?? 'Opening balance set',
        ]);
    }

    public function recordAdjustment(User $user, float $oldAmount, float $newAmount, ?int $actorId = null, ?string $note = null): void
    {
        if ($oldAmount === $newAmount) {
            return;
        }

        UserBalanceHistory::create([
            'user_id' => $user->id,
            'change_type' => 'adjustment',
            'change_amount' => $newAmount - $oldAmount,
            'balance_before' => $oldAmount,
            'balance_after' => $newAmount,
            'reference_type' => 'user',
            'reference_id' => $user->id,
            'created_by' => $actorId,
            'note' => $note ?? 'Admin updated balance',
        ]);
    }

    public function createTransfer(User $sender, User $recipient, array $data): Transfer
    {
        return DB::transaction(function () use ($recipient, $data) {
            return Transfer::create([
                'user_id' => $recipient->id,
                'created_by' => auth()->id(),
                'start_date' => $data['start_date'] ?? null,
                'amount' => (float) $data['amount'],
                'note' => $data['note'] ?? null,
            ]);
        });
    }

    public function createExpense(User $user, array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            return Expense::create($data);
        });
    }

    public function createCredit(User $user, array $data): Credit
    {
        return DB::transaction(function () use ($user, $data) {
            return Credit::create([
                ...$data,
                'users_id' => $user->id,
            ]);
        });
    }

    protected function createHistory(
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
            'change_amount' => $changeAmount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $createdBy,
            'note' => $note,
        ]);
    }
}
