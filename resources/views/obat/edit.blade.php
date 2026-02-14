@extends('layouts.app')

@section('title', 'Edit Obat')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('obat.index') }}"
                    class="inline-flex items-center justify-center w-10 h-10 text-gray-600 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Obat</h1>
                    <p class="text-sm text-gray-600">Ubah informasi obat {{ $obat->nm_obat }}</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Obat</h2>
                    <span
                        class="px-3 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full">{{ $obat->kd_obat }}</span>
                </div>
            </div>

            <form action="{{ route('obat.update', $obat->kd_obat) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Kode Obat (Read Only) -->
                    <div>
                        <label for="kd_obat" class="block text-sm font-medium text-gray-700">
                            Kode Obat
                        </label>
                        <input type="text" name="kd_obat" id="kd_obat" value="{{ $obat->kd_obat }}" readonly
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Kode obat tidak dapat diubah</p>
                    </div>

                    <!-- Nama Obat -->
                    <div>
                        <label for="nm_obat" class="block text-sm font-medium text-gray-700">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nm_obat" id="nm_obat" value="{{ old('nm_obat', $obat->nm_obat) }}"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('nm_obat') border-red-500 @enderror"
                            placeholder="Contoh: Paracetamol">
                        @error('nm_obat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis -->
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700">
                            Jenis Obat <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" id="jenis" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('jenis') border-red-500 @enderror">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Tablet" {{ old('jenis', $obat->jenis) == 'Tablet' ? 'selected' : '' }}>Tablet
                            </option>
                            <option value="Kapsul" {{ old('jenis', $obat->jenis) == 'Kapsul' ? 'selected' : '' }}>Kapsul
                            </option>
                            <option value="Sirup" {{ old('jenis', $obat->jenis) == 'Sirup' ? 'selected' : '' }}>Sirup
                            </option>
                            <option value="Salep" {{ old('jenis', $obat->jenis) == 'Salep' ? 'selected' : '' }}>Salep
                            </option>
                            <option value="Injeksi" {{ old('jenis', $obat->jenis) == 'Injeksi' ? 'selected' : '' }}>
                                Injeksi</option>
                            <option value="Lainnya" {{ old('jenis', $obat->jenis) == 'Lainnya' ? 'selected' : '' }}>
                                Lainnya</option>
                        </select>
                        @error('jenis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700">
                            Satuan <span class="text-red-500">*</span>
                        </label>
                        <select name="satuan" id="satuan" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('satuan') border-red-500 @enderror">
                            <option value="">-- Pilih Satuan --</option>
                            <option value="Strip" {{ old('satuan', $obat->satuan) == 'Strip' ? 'selected' : '' }}>Strip
                            </option>
                            <option value="Box" {{ old('satuan', $obat->satuan) == 'Box' ? 'selected' : '' }}>Box
                            </option>
                            <option value="Botol" {{ old('satuan', $obat->satuan) == 'Botol' ? 'selected' : '' }}>Botol
                            </option>
                            <option value="Tube" {{ old('satuan', $obat->satuan) == 'Tube' ? 'selected' : '' }}>Tube
                            </option>
                            <option value="Pcs" {{ old('satuan', $obat->satuan) == 'Pcs' ? 'selected' : '' }}>Pcs
                            </option>
                            <option value="Pack" {{ old('satuan', $obat->satuan) == 'Pack' ? 'selected' : '' }}>Pack
                            </option>
                        </select>
                        @error('satuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label for="harga_beli" class="block text-sm font-medium text-gray-700">
                            Harga Beli <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="number" name="harga_beli" id="harga_beli"
                                value="{{ old('harga_beli', $obat->harga_beli) }}" min="0" step="0.01" required
                                class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('harga_beli') border-red-500 @enderror"
                                placeholder="0">
                        </div>
                        @error('harga_beli')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Jual -->
                    <div>
                        <label for="harga_jual" class="block text-sm font-medium text-gray-700">
                            Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="number" name="harga_jual" id="harga_jual"
                                value="{{ old('harga_jual', $obat->harga_jual) }}" min="0" step="0.01" required
                                class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('harga_jual') border-red-500 @enderror"
                                placeholder="0">
                        </div>
                        @error('harga_jual')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok -->
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', $obat->stok) }}"
                            min="0" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('stok') border-red-500 @enderror"
                            placeholder="0">
                        @error('stok')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label for="kd_supplier" class="block text-sm font-medium text-gray-700">
                            Supplier <span class="text-red-500">*</span>
                        </label>
                        <select name="kd_supplier" id="kd_supplier" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('kd_supplier') border-red-500 @enderror">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->kd_supplier }}"
                                    {{ old('kd_supplier', $obat->kd_supplier) == $supplier->kd_supplier ? 'selected' : '' }}>
                                    {{ $supplier->nm_supplier }}
                                </option>
                            @endforeach
                        </select>
                        @error('kd_supplier')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end mt-8 space-x-3">
                    <a href="{{ route('apoteker.obat') }}"
                        class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
