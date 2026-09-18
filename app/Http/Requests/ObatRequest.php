<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObatRequest extends FormRequest
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
            'kd_obat' => $this->isMethod('post')
                ? 'required|string|min:1|max:10|unique:obats,kd_obat'
                : 'nullable|string',
            'nm_obat' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kd_supplier' => 'required|exists:suppliers,kd_supplier',
            'gambar' => $this->hasFile('gambar')
                ? 'image|mimes:jpeg,png,jpg|max:2048'
                : 'nullable',
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
            'kd_obat.required' => 'Kode obat wajib diisi',
            'kd_obat.unique' => 'Kode obat sudah digunakan',
            'nm_obat.required' => 'Nama obat wajib diisi',
            'jenis.required' => 'Jenis obat wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
            'harga_beli.required' => 'Harga beli wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'kd_supplier.required' => 'Supplier wajib dipilih',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar.max' => 'Ukuran gambar maksimal 2 MB',
        ];
    }
}
