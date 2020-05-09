<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    public const VALUE = 'value';
    public const TRANSACTION_TYPE = 'transaction_type';
    public const CURRENCY = 'currency';
    public const CHANGE_REASON = 'change_reason';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            self::VALUE => ['required', 'numeric'],
            self::TRANSACTION_TYPE => ['required', Rule::in(['debit', 'credit'])],
            self::CURRENCY => ['required', Rule::in(['USD', 'RUB'])],
            self::CHANGE_REASON => ['required', Rule::in(['stock', 'refund'])],
        ];
    }
}
