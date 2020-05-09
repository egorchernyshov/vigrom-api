<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Account;
use App\AccountHistory;
use App\Services\CurrencyConversionService;
use Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider accountsProvider
     *
     * @param int $accountNumber
     */
    public function test_should_fetch_account_balance(int $accountNumber): void
    {
        $response = $this->get(sprintf('/api/balance/%d', $accountNumber));
        $response->assertStatus(200);

        self::assertIsNumeric($response['balance']);
    }
    /**
     * @dataProvider accountsProvider
     *
     * @param int $accountNumber
     */
    public function test_updating_balance(int $accountNumber): void
    {
        $currency = $this->faker->randomElement(['USD', 'RUB']);

        // Current balance
        $originalBalance = Account::whereKey($accountNumber)->value('balance');

        // Updating balance
        $params = [
            'value' => $value = $this->faker->numberBetween(-100, 100),
            'transaction_type' => $transactionType = $this->faker->randomElement(['debit', 'credit']),
            'currency' => $currency,
            'change_reason' => $changeReason = $this->faker->randomElement(['stock', 'refund']),
        ];
        $response = $this->put(sprintf('/api/balance/%d', $accountNumber), $params);
        $response->assertStatus(204);

        // New balance
        $changedBalance = Account::whereKey($accountNumber)->value('balance');
        if ($currency === 'USD') {
            $valueUsdToRub = $this->app->get(CurrencyConversionService::class)->convertUsdToRub($value);
            self::assertEquals($originalBalance, $changedBalance - $valueUsdToRub);
        } else {
            self::assertEquals($originalBalance, $changedBalance - $value);
        }

        // Last history entry
        $lastHistoryEntry = AccountHistory::getQuery()->orderByDesc('id')->first();
        self::assertEquals($value, $lastHistoryEntry->value);
        self::assertEquals($transactionType, $lastHistoryEntry->transaction_type);
        self::assertEquals($currency, $lastHistoryEntry->currency);
        self::assertEquals($changeReason, $lastHistoryEntry->change_reason);
    }

    public function accountsProvider(): Generator
    {
        yield 'Account number: 241' => [241];
    }
}
