@props(['obat', 'class' => ''])

@if ($obat->gambar)
    <img src="{{ asset('storage/'.$obat->gambar) }}" alt="{{ $obat->nm_obat }}" loading="lazy"
        class="{{ $class }} object-cover">
@else
    <img src="{{ asset('images/no-image.svg') }}" alt="{{ $obat->nm_obat }}" loading="lazy"
        class="{{ $class }} object-cover">
@endif