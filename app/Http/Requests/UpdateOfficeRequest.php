<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeRequest extends FormRequest
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
        // افترضنا أنك عم تمرر office model في الراوت باسم 'office'
        $office = $this->route('office');

        return [
            'email' => 'required|email|unique:users,email,' . $office->provider->id,
            'password' => 'required|min:6',
            'name' => 'required|string',
            'address' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
