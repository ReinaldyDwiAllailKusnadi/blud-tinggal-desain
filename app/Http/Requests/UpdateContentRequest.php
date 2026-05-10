<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentRequest extends FormRequest
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
        $contentId = $this->route('content')->id ?? $this->route('content');
        return [
            'name'          => 'sometimes|string|max:255|unique:content,name,' . $contentId,
            'slug'          => 'nullable|string|max:255|unique:content,slug,' . $contentId,
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
            'facility_names'     => ['nullable', 'array'],
            'facility_names.*'   => ['required_with:facility_names', 'string', 'max:255'],
            'features'           => ['nullable', 'array'],
            'features.*.bagian'  => ['nullable', 'string'],
            'features.*.luas'    => ['nullable', 'string'],
            'features.*.price'   => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $features = $this->features;
        if (is_array($features)) {
            foreach ($features as $key => $feature) {
                if (isset($feature['price'])) {
                    $features[$key]['price'] = $this->cleanRupiah($feature['price']);
                }
            }
        }

        $this->merge([
            'price_weekday' => $this->cleanRupiah($this->price_weekday),
            'price_weekend' => $this->cleanRupiah($this->price_weekend),
            'features' => $features,
            'is_indoor' => $this->boolean('is_indoor'),
            'is_outdoor' => $this->boolean('is_outdoor'),
        ]);
    }

    private function cleanRupiah($value)
    {
        return $value !== null ? preg_replace('/[^0-9]/', '', $value) : null;
    }
}
