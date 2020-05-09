<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\AccountHistory;
use App\Http\Requests\AccountUpdateRequest;
use App\Services\CurrencyConversionService;
use DB;
use Illuminate\Http\JsonResponse;
use Throwable;

class AccountController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $accountNumber
     *
     * @return JsonResponse
     */
    public function show(int $accountNumber): JsonResponse
    {
        return new JsonResponse(
            ['balance' => Account::whereKey($accountNumber)->value('balance')]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $accountNumber
     * @param AccountUpdateRequest $request
     *
     * @throws Throwable
     *
     * @return JsonResponse
     */
    public function update(int $accountNumber, AccountUpdateRequest $request): JsonResponse
    {
        DB::beginTransaction();

        $this->writeHistory($request, $accountNumber);

        if ($this->updateBalance($accountNumber, $request)) {
            DB::commit();

            return new JsonResponse(null, 204);
        }

        DB::rollBack();

        return new JsonResponse('Operation canceled.', 409);
    }

    /**
     * @param AccountUpdateRequest $request
     * @param int $accountNumber
     */
    private function writeHistory(AccountUpdateRequest $request, int $accountNumber): void
    {
        AccountHistory::create([
            'value' => $request->input(AccountUpdateRequest::VALUE),
            'transaction_type' => $request->input(AccountUpdateRequest::TRANSACTION_TYPE),
            'currency' => $request->input(AccountUpdateRequest::CURRENCY),
            'change_reason' => $request->input(AccountUpdateRequest::CHANGE_REASON),
            'account_number' => $accountNumber,
        ])->save();
    }

    /**
     * @param int $accountNumber
     * @param AccountUpdateRequest $request
     *
     * @throws Throwable
     *
     * @return bool
     */
    private function updateBalance(int $accountNumber, AccountUpdateRequest $request): bool
    {
        $account = Account::whereKey($accountNumber);
        $currentBalance = $account->value('balance');

        return $account->update([
            'balance' => $currentBalance + $this->getValue($request)
        ]);
    }

    /**
     * @param AccountUpdateRequest $request
     *
     * @return int
     */
    private function getValue(AccountUpdateRequest $request): int
    {
        $value = $request->input(AccountUpdateRequest::VALUE);

        if ('USD' === $request->input(AccountUpdateRequest::CURRENCY)) {
            $value = app(CurrencyConversionService::class)->convertUsdToRub($value);
        }

        return $value;
    }
}
