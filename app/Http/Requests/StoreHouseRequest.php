<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->check() && auth()->user()->userable_type == 'App\Models\Office') {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'office_id'     => 'nullable|exists:offices,id',
            'district_id'   => 'required|exists:districts,id',
            'type'          => 'required|in:rent,sale',
            'status'        => 'required|in:empty,occupied',
            'rooms'         => 'required|integer|min:0',
            'floor'         => 'required|integer|min:0',
            'area'          => 'required|numeric|min:0',
            'direction'     => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
        ];
    }
}
