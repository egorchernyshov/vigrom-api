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
     * @example `GET /api/balance/241`
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
     * @example `PUT /api/balance/241`
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

        $this->writeHistory($accountNumber, $request);

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
     */
    private function writeHistory(int $accountNumber, AccountUpdateRequest $request): void
    {
        $data = [
            'value' => $request->input(AccountUpdateRequest::VALUE),
            'transaction_type' => $request->input(AccountUpdateRequest::TRANSACTION_TYPE),
            'currency' => $request->input(AccountUpdateRequest::CURRENCY),
            'change_reason' => $request->input(AccountUpdateRequest::CHANGE_REASON),
            'account_number' => $accountNumber,
        ];

        AccountHistory::create($data)->save();
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
