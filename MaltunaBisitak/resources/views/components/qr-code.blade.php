@props(['data', 'size' => 240])

@php
    $dataUri = (new \Endroid\QrCode\Writer\PngWriter)
        ->write(new \Endroid\QrCode\QrCode($data, size: $size, margin: 8))
        ->getDataUri();
@endphp

<img src="{{ $dataUri }}" {{ $attributes->merge(['alt' => 'QR', 'width' => $size, 'height' => $size]) }}>
