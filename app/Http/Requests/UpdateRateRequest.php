<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:0',
            'house_id' => 'required|exists:houses,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
    protected function prepareForValidation(): void
    {

        $this->merge([
            'user_id' => Auth::guard('api')->user()->id
        ]);
    }
}
