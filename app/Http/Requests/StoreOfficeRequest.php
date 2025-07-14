<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRequest extends FormRequest
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
        return [
            //'owner_id'     => 'required|exists:users,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required|string',
            //'name.regex' => 'The name must not contain numbers or special characters.',
            //'email.email' => 'The email must be a valid email address.',
            'address'      => 'required|string|max:255',
            'district_id'  => 'required|exists:districts,id',
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
