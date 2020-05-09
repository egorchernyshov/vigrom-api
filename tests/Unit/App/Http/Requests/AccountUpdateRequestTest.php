<?php

namespace Tests\Unit\App\Http\Requests;

use App\Http\Requests\AccountUpdateRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AccountUpdateRequestTest extends TestCase
{
    use WithFaker;

    public function test_empty_request_validation(): void
    {
        $request = new AccountUpdateRequest();
        self::assertCount(4, Validator::make([], $request->rules())->errors());
    }

    public function test_invalid_parameters_values(): void
    {
        $params = [
            'value' => null,
            'transaction_type' => null,
            'currency' => null,
            'change_reason' => null,
        ];
        $request = new AccountUpdateRequest($params);

        self::assertCount(4, Validator::make($params, $request->rules())->errors());
    }

    public function test_valid_parameters_values(): void
    {
        $params['value'] = $this->faker->numberBetween(-10000, 10000);
        $params['transaction_type'] = $this->faker->randomElement(['debit', 'credit']);
        $params['currency'] = $this->faker->randomElement(['USD', 'RUB']);
        $params['change_reason'] = $this->faker->randomElement(['stock', 'refund']);
        $request = new AccountUpdateRequest($params);

        self::assertCount(0, Validator::make($params, $request->rules())->errors());
    }
}
