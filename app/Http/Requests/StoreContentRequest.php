<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
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
            'name'          => 'required|string|max:255|unique:content,name',
            'description'   => 'nullable|string',
            'price_weekday' => 'nullable|string',
            'price_weekend' => 'nullable|string',
            'open_time'     => 'nullable|date_format:H:i',
            'close_time'    => 'nullable|date_format:H:i',
            'location'      => 'nullable|string|max:255',
            'location_embed'=> 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
            'instagram'     => 'nullable|string',
            'whatsapp'      => 'nullable|string|max:255',
            'capacity'      => ['nullable', 'integer', 'min:0'],
            'venue_type'    => ['nullable', 'string', 'max:255'],
            'is_indoor'     => ['nullable', 'boolean'],
            'is_outdoor'    => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price_weekday' => $this->cleanRupiah($this->price_weekday),
            'price_weekend' => $this->cleanRupiah($this->price_weekend),
            'is_indoor' => $this->boolean('is_indoor'),
            'is_outdoor' => $this->boolean('is_outdoor'),
        ]);
    }

    private function cleanRupiah($value)
    {
        return $value !== null ? preg_replace('/[^0-9]/', '', $value) : null;
    }
}
