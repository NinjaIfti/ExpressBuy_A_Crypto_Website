<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiatStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->method() == 'GET') {
            return [];
        }
        return [
            'name' => 'required',
            'code' => 'required|min:2',
            'symbol' => 'required',
            'rate' => 'required|numeric|min:0|not_in:0',
            'min_send' => 'required|numeric|min:1',
            'max_send' => 'required|numeric|min:1',
            'processing_fee' => 'required|numeric|min:0|not_in:0',
            'processing_fee_type' => ['required', Rule::in(['percent', 'flat'])],
            'status' => ['required', Rule::in(['0', '1'])],

        ];
    }
}
