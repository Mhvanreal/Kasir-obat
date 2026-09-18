@extends('layouts.app')

@section('title', 'Tambah Obat')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('karyawan.obat.index') }}"
                    class="inline-flex items-center justify-center w-10 h-10 text-gray-600 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Obat Baru</h1>
                    <p class="text-sm text-gray-600">Lengkapi form di bawah untuk menambahkan obat baru</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Obat</h2>
            </div>

            <form action="{{ route('karyawan.obat.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Kode Obat -->
                    <div>
                        <label for="kd_obat" class="block text-sm font-medium text-gray-700">
                            Kode Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kd_obat" id="kd_obat" value="{{ old('kd_obat') }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('kd_obat') border-red-500 @enderror"
                            placeholder="Contoh: OBT001">
                        @error('kd_obat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Obat -->
                    <div>
                        <label for="nm_obat" class="block text-sm font-medium text-gray-700">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nm_obat" id="nm_obat" value="{{ old('nm_obat') }}" required
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
                            <option value="Tablet" {{ old('jenis') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Kapsul" {{ old('jenis') == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                            <option value="Sirup" {{ old('jenis') == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                            <option value="Salep" {{ old('jenis') == 'Salep' ? 'selected' : '' }}>Salep</option>
                            <option value="Injeksi" {{ old('jenis') == 'Injeksi' ? 'selected' : '' }}>Injeksi</option>
                            <option value="Lainnya" {{ old('jenis') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                            <option value="Strip" {{ old('satuan') == 'Strip' ? 'selected' : '' }}>Strip</option>
                            <option value="Box" {{ old('satuan') == 'Box' ? 'selected' : '' }}>Box</option>
                            <option value="Botol" {{ old('satuan') == 'Botol' ? 'selected' : '' }}>Botol</option>
                            <option value="Tube" {{ old('satuan') == 'Tube' ? 'selected' : '' }}>Tube</option>
                            <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="Pack" {{ old('satuan') == 'Pack' ? 'selected' : '' }}>Pack</option>
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
                            <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}"
                                min="0" step="0.01" required
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
                            <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}"
                                min="0" step="0.01" required
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
                            Stok Awal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', 0) }}"
                            min="0" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('stok') border-red-500 @enderror"
                            placeholder="0">
                        @error('stok')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gambar -->
                    <div class="md:col-span-2">
                        <label for="gambar" class="block text-sm font-medium text-gray-700">
                            Gambar Obat
                        </label>
                        <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        <p class="mt-1 text-xs text-gray-500">Format: jpeg, png, jpg · Maksimal 2 MB</p>
                        @error('gambar')
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
                                    {{ old('kd_supplier') == $supplier->kd_supplier ? 'selected' : '' }}>
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
                    <a href="{{ route('karyawan.obat.index') }}"
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
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
