<?php

namespace App\Http\Requests;

use App\Models\Pengeluaran;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'tanggal'     => ['required', 'date'],
            'jenis'       => ['required', Rule::in([Pengeluaran::JENIS_OPERASIONAL, Pengeluaran::JENIS_GAJI])],
            'karyawan_id' => [
                'nullable',
                'required_if:jenis,'.Pengeluaran::JENIS_GAJI,
                'exists:users,id',
                // Karyawan_id harus milik user dengan role karyawan.
                function (string $attribute, $value, Closure $fail) {
                    if (! $value) {
                        return;
                    }
                    $user = User::find($value);
                    if (! $user || $user->role !== 'karyawan') {
                        $fail('Penerima gaji harus berupa akun dengan role karyawan.');
                    }
                },
            ],
            'deskripsi'   => ['required', 'string', 'max:255'],
            'jumlah'      => ['required', 'numeric', 'min:0.01'],
            'catatan'     => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required'     => 'Tanggal wajib diisi.',
            'jenis.required'       => 'Jenis pengeluaran wajib dipilih.',
            'jenis.in'             => 'Jenis pengeluaran tidak valid.',
            'karyawan_id.required_if' => 'Karyawan penerima gaji wajib dipilih.',
            'karyawan_id.exists'   => 'Karyawan tidak ditemukan.',
            'deskripsi.required'   => 'Deskripsi wajib diisi.',
            'jumlah.required'      => 'Jumlah wajib diisi.',
            'jumlah.numeric'       => 'Jumlah harus berupa angka.',
            'jumlah.min'           => 'Jumlah minimal 0.01.',
        ];
    }

    /**
     * Bersihkan karyawan_id kalau jenis bukan gaji, supaya data konsisten.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('jenis') !== Pengeluaran::JENIS_GAJI) {
            $this->merge(['karyawan_id' => null]);
        }
    }
}
