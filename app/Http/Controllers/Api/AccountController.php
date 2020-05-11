<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\AccountHistory;
use App\Http\Requests\AccountUpdateRequest;
use App\Services\CurrencyConversionService;
use Cache;
use DB;
use Illuminate\Http\JsonResponse;
use Throwable;

class AccountController extends Controller
{
    /**
     * @param int $accountNumber
     *
     * @return JsonResponse
     * @example `GET /api/balance/241`
     *
     */
    public function show(int $accountNumber): JsonResponse
    {
        return new JsonResponse(
            ['balance' => Account::whereKey($accountNumber)->value('balance')]
        );
    }

    /**
     * @param int $accountNumber
     * @param AccountUpdateRequest $request
     *
     * @throws Throwable
     *
     * @return JsonResponse
     * @example `PUT /api/balance/241`
     *
     */
    public function update(int $accountNumber, AccountUpdateRequest $request): JsonResponse
    {
        DB::beginTransaction();

        if ($this->updateBalance($accountNumber, $request)) {
            DB::commit();

            return new JsonResponse(null, 204);
        }

        DB::rollBack();

        return new JsonResponse('Operation canceled.', 409);
    }

    /**
     * @param int $accountNumber
     * @param AccountUpdateRequest $request
     *
     * @return int
     */
    public function updateBalance(int $accountNumber, AccountUpdateRequest $request): int
    {
        $account = Account::whereKey($accountNumber);
        $currentBalance = $account->value('balance');
        $value = $this->getValue($accountNumber, $request);

        $balance = ('debit' === $request->input(AccountUpdateRequest::TRANSACTION_TYPE))
            ? $currentBalance - $value
            : $currentBalance + $value;

        return $account->update(['balance' => $balance]);
    }

    /**
     * @param AccountUpdateRequest $request
     * @param int $accountNumber
     *
     * @return int
     */
    public function getValue(int $accountNumber, AccountUpdateRequest $request): int
    {
        if ('USD' === $request->input(AccountUpdateRequest::CURRENCY)) {
            $exchangeRate = Cache::exchangeRate();
            $convertedValue = CurrencyConversionService::convertUsd(
                $request->input(AccountUpdateRequest::VALUE),
                $exchangeRate['value']
            );
        }

        $value = $convertedValue ?? $request->input(AccountUpdateRequest::VALUE);

        $this->writeHistory(
            $accountNumber,
            $request,
            $value,
            $exchangeRate['id'] ?? null
        );

        return $value;
    }

    public function writeHistory(
        int $accountNumber,
        AccountUpdateRequest $request,
        int $value,
        ?int $id = null
    ): void {
        $data = [
            'account_number' => $accountNumber,
            'value' => $value,
            'exchange_rate_id' => $id,
            'original_value' => $request->input(AccountUpdateRequest::VALUE),
            'transaction_type' => $request->input(AccountUpdateRequest::TRANSACTION_TYPE),
            'currency' => $request->input(AccountUpdateRequest::CURRENCY),
            'change_reason' => $request->input(AccountUpdateRequest::CHANGE_REASON),
        ];

        AccountHistory::create($data)->save();
    }
}
