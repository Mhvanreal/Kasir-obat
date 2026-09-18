@extends('layouts.app')

@section('title', 'Pembelian Stok')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-8xl sm:px-6 lg:px-8" x-data="pembelianData()">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <div
                    class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pembelian Stok</h1>
                    <p class="text-sm text-gray-600">Restok obat dari supplier (stok & harga beli diperbarui)</p>
                </div>
            </div>
        </div>

        {{-- Flash success/error ditampilkan otomatis lewat <x-flash-toast /> di layouts.app --}}

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left: Tabel Obat untuk Restok -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-white rounded-lg shadow-md">
                    <!-- Header dengan Filter -->
                    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Obat untuk Restok</h3>
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" x-model="cari"
                                    class="block w-56 py-2 pl-10 pr-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="Cari obat...">
                            </div>
                            <select x-model="supplierFilter"
                                class="block py-2 pl-3 pr-8 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">Semua Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->kd_supplier }}">{{ $supplier->nm_supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tabel Obat -->
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase w-12">
                                        #
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Nama Obat
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Supplier
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase w-24">
                                        Stok
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase w-32">
                                        Harga Beli
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase w-32">
                                        Jumlah Restok
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase w-28">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(obat, idx) in filteredObats" :key="obat.kd_obat">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap" x-text="idx + 1"></td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10 mr-3">
                                                    <img class="object-cover w-10 h-10 rounded-lg" 
                                                        :src="obat.gambar ? '/storage/' + obat.gambar : '/images/no-image.svg'"
                                                        :alt="obat.nm_obat">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900" x-text="obat.nm_obat"></p>
                                                    <p class="text-xs text-gray-500" x-text="obat.jenis + ' · ' + obat.satuan"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                            <span x-text="obat.supplier ? obat.supplier.nm_supplier : '-'"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span :class="obat.stok < 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'"
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                x-text="obat.stok + ' ' + obat.satuan"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            <input type="number" 
                                                :value="obat.harga_beli"
                                                @change="updateHargaBeli(obat.kd_obat, $event.target.value)"
                                                min="0" step="100"
                                                class="w-full px-2 py-1 text-sm text-right border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                                placeholder="Harga beli">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                :value="getJumlahRestok(obat.kd_obat)"
                                                @input="updateJumlahRestok(obat.kd_obat, $event.target.value)"
                                                min="0" step="1"
                                                class="w-24 px-2 py-1 text-sm text-center border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                                placeholder="0">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" 
                                                @click="tambahKeKeranjang(obat)"
                                                :disabled="!getJumlahRestok(obat.kd_obat) || getJumlahRestok(obat.kd_obat) <= 0"
                                                :class="getJumlahRestok(obat.kd_obat) > 0 ? 'bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold transition-all duration-200 rounded-lg">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Tambah
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div x-show="filteredObats.length === 0" class="py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500">Tidak ada obat yang cocok dengan filter</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Ringkasan + Riwayat (Tabs) -->
            <div class="lg:col-span-1" x-data="{ activeTab: 'ringkasan' }">
                <div class="sticky top-6 overflow-hidden bg-white rounded-lg shadow-md">
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-200">
                        <button @click="activeTab = 'ringkasan'" 
                            :class="activeTab === 'ringkasan' ? 'border-teal-500 text-teal-600 bg-teal-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Ringkasan
                        </button>
                        <button @click="activeTab = 'riwayat'"
                            :class="activeTab === 'riwayat' ? 'border-teal-500 text-teal-600 bg-teal-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Riwayat
                        </button>
                    </div>

                    <!-- Tab Content: Ringkasan -->
                    <div x-show="activeTab === 'ringkasan'" class="p-6">
                        <form action="{{ route('admin.pembelian.store') }}" method="POST">
                            @csrf

                            <!-- Hidden cart items -->
                            <template x-for="(item, index) in cart" :key="index">
                                <div>
                                    <input type="hidden" :name="'items[' + index + '][kd_obat]'" :value="item.kd_obat">
                                    <input type="hidden" :name="'items[' + index + '][jumlah]'" :value="item.jumlah">
                                    <input type="hidden" :name="'items[' + index + '][harga_beli]'" :value="item.harga_beli">
                                </div>
                            </template>

                            <!-- Supplier -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <select name="kd_supplier" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->kd_supplier }}">{{ $supplier->nm_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Diskon -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Diskon (%)</label>
                                <div class="relative mt-1">
                                    <input type="number" x-model.number="diskonInput" name="diskon" min="0" max="100"
                                        class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">%</span>
                                </div>
                            </div>

                            <!-- Cart Items -->
                            <div class="mt-6" x-show="cart.length > 0">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 uppercase">Keranjang Pembelian</h4>
                                <div class="overflow-y-auto max-h-64">
                                    <template x-for="(item, index) in cart" :key="index">
                                        <div class="flex items-start justify-between py-2 border-b border-gray-100">
                                            <div class="flex-1 min-w-0 pr-2">
                                                <p class="text-sm font-medium text-gray-900" x-text="item.nm_obat"></p>
                                                <p class="text-xs text-gray-500">
                                                    <span x-text="'Rp ' + Number(item.harga_beli).toLocaleString('id-ID')"></span>
                                                    <span> × </span>
                                                    <span x-text="item.jumlah"></span>
                                                    <span x-text="' ' + item.satuan"></span>
                                                </p>
                                                <p class="text-xs font-semibold text-teal-600">
                                                    <span x-text="'Rp ' + (item.harga_beli * item.jumlah).toLocaleString('id-ID')"></span>
                                                </p>
                                            </div>
                                            <button type="button" @click="hapusDariKeranjang(index)" 
                                                class="text-red-500 hover:text-red-700 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="mt-6 space-y-2 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Total Belanja</span>
                                    <span class="text-sm font-semibold text-gray-900"
                                        x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Diskon</span>
                                    <span class="text-sm font-semibold text-red-600"
                                        x-text="'-Rp ' + diskon.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                    <span class="text-base font-bold text-gray-900">Grand Total</span>
                                    <span class="text-base font-bold text-teal-600"
                                        x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <button type="submit" x-show="cart.length > 0"
                                class="inline-flex items-center justify-center w-full px-4 py-3 mt-6 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Pembelian
                            </button>

                            <p x-show="cart.length === 0" class="mt-6 text-sm text-center text-gray-500">
                                Tambahkan obat untuk restok terlebih dahulu
                            </p>
                        </form>
                    </div>

                    <!-- Tab Content: Riwayat -->
                    <div x-show="activeTab === 'riwayat'" class="overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-emerald-50">
                            <h3 class="text-lg font-semibold text-gray-900">Riwayat Pembelian</h3>
                            <p class="text-xs text-gray-600 mt-1">50 pembelian terakhir</p>
                        </div>
                        <div class="overflow-y-auto" style="max-height: calc(100vh - 250px);">
                            @forelse ($pembelians as $pembelian)
                                <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <p class="text-xs font-semibold text-teal-800 bg-teal-100 inline-block px-2 py-0.5 rounded-full mb-1">
                                                {{ $pembelian->nota }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $pembelian->tgl_nota->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Supplier:</span>
                                            <span class="font-medium text-gray-900">{{ $pembelian->supplier->nm_supplier ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total:</span>
                                            <span class="text-gray-900">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between pt-1 border-t border-gray-200">
                                            <span class="text-gray-900 font-semibold">Grand Total:</span>
                                            <span class="font-bold text-teal-600">Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-500">Petugas:</span>
                                            <span class="text-gray-600">{{ $pembelian->user->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-gray-900">Belum ada pembelian</p>
                                    <p class="mt-1 text-xs text-gray-500">Mulai dengan merestok obat dari supplier</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.obatsData = @json($obats);
        
        function pembelianData() {
            return {
                cart: [],
                cari: '',
                supplierFilter: '',
                diskonInput: 0,
                jumlahRestok: {}, // Track jumlah restok per obat
                
                get filteredObats() {
                    const query = this.cari.toLowerCase();
                    return window.obatsData.filter(o => {
                        const cocokCari = !query ||
                            o.nm_obat.toLowerCase().includes(query) ||
                            (o.kd_obat || '').toLowerCase().includes(query);
                        const cocokSupplier = !this.supplierFilter || o.kd_supplier === this.supplierFilter;
                        return cocokCari && cocokSupplier;
                    });
                },
                
                get total() {
                    return this.cart.reduce((sum, item) => sum + (parseFloat(item.harga_beli) || 0) * item.jumlah, 0);
                },
                
                get diskon() {
                    return (this.total * parseFloat(this.diskonInput || 0)) / 100;
                },
                
                get grandTotal() {
                    return this.total - this.diskon;
                },
                
                getJumlahRestok(kd_obat) {
                    return this.jumlahRestok[kd_obat] || 0;
                },
                
                updateJumlahRestok(kd_obat, value) {
                    this.jumlahRestok[kd_obat] = parseInt(value) || 0;
                },
                
                updateHargaBeli(kd_obat, value) {
                    const obat = window.obatsData.find(o => o.kd_obat === kd_obat);
                    if (obat) {
                        obat.harga_beli = parseFloat(value) || 0;
                    }
                    // Update di cart jika sudah ada
                    const cartItem = this.cart.find(i => i.kd_obat === kd_obat);
                    if (cartItem) {
                        cartItem.harga_beli = parseFloat(value) || 0;
                    }
                },
                
                tambahKeKeranjang(obat) {
                    const jumlah = this.getJumlahRestok(obat.kd_obat);
                    if (jumlah <= 0) return;
                    
                    const existing = this.cart.find(i => i.kd_obat === obat.kd_obat);
                    if (existing) {
                        existing.jumlah = jumlah;
                        existing.harga_beli = parseFloat(obat.harga_beli) || 0;
                    } else {
                        this.cart.push({
                            kd_obat: obat.kd_obat,
                            nm_obat: obat.nm_obat,
                            satuan: obat.satuan || '',
                            harga_beli: parseFloat(obat.harga_beli) || 0,
                            jumlah: jumlah
                        });
                    }
                    
                    // Reset jumlah restok setelah ditambahkan
                    this.jumlahRestok[obat.kd_obat] = 0;
                },
                
                hapusDariKeranjang(index) {
                    this.cart.splice(index, 1);
                }
            };
        }
    </script>
@endpush
