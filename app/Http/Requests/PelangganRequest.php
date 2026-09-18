<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PelangganRequest extends FormRequest
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
            'nm_pelanggan' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:50',
            'telpon' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nm_pelanggan.required' => 'Nama pelanggan wajib diisi',
            'nm_pelanggan.max' => 'Nama pelanggan maksimal 100 karakter',
            'kota.max' => 'Kota maksimal 50 karakter',
            'telpon.max' => 'Telepon maksimal 20 karakter',
        ];
    }
}
