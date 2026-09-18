<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PembelianRequest extends FormRequest
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
            'kd_supplier' => 'required|exists:suppliers,kd_supplier',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.kd_obat' => 'required|exists:obats,kd_obat',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'nullable|numeric|min:0',
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
            'kd_supplier.required' => 'Supplier wajib dipilih',
            'items.required' => 'Minimal satu item harus ditambahkan',
            'items.min' => 'Minimal satu item harus ditambahkan',
            'items.*.kd_obat.required' => 'Obat wajib dipilih',
            'items.*.jumlah.required' => 'Jumlah wajib diisi',
            'items.*.jumlah.min' => 'Jumlah minimal 1',
        ];
    }
}
