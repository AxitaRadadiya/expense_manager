<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Collection;

class TransferService
{
    public function __construct(protected BalanceService $balanceService)
    {
    }

    public function createTransfer(User $sender, User $recipient, array $data): Transfer
    {
        return $this->balanceService->createTransfer($sender, $recipient, $data);
    }

    public function getTransfersForUser(User $user, bool $receivedOnly = false): Collection
    {
        $query = Transfer::query();

        if ($receivedOnly) {
            $query->where('user_id', $user->id);
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function getSummary(array $filters = []): Collection
    {
        $query = Transfer::query();

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        return $query->orderByDesc('created_at')->get();
    }
}
