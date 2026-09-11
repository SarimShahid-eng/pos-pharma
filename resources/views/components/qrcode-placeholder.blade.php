{{--
    Temporary QR placeholder. NOT scannable — a deterministic pseudo-random
    module grid (same value always renders the same pattern) with the three
    classic QR finder squares stamped on top for visual authenticity.

    To swap in the real thing later: replace the <svg>...</svg> block below
    with something like:
        {!! QrCode::size($size ?? 90)->generate($value) !!}
    (from simplesoftwareio/simple-qrcode) and delete the $matrix generation
    logic — everything else stays the same.

    Props:
        value (string) — the value to encode, e.g. invoice number
        size  (int, optional, default 90)
--}}
@php
    $qrValue = $value ?? '';
    $size = $size ?? 90;
    $grid = 21; // matches a real QR v1 module count, for visual proportions
    $cell = $size / $grid;
    $seed = crc32($qrValue);

    // Deterministic pseudo-random fill — placeholder density only.
    $matrix = [];
    for ($r = 0; $r < $grid; $r++) {
        for ($c = 0; $c < $grid; $c++) {
            $n = ($r * 37 + $c * 17 + $seed) * 2654435761;
            $matrix[$r][$c] = (abs($n) % 100) < 45;
        }
    }

    // Stamp the three corner finder squares over the random fill.
    $stampFinder = function (&$matrix, $originR, $originC) {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                $isRing = ($r === 0 || $r === 6 || $c === 0 || $c === 6);
                $isCore = ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4);
                $matrix[$originR + $r][$originC + $c] = $isRing || $isCore;
            }
        }
    };
    $stampFinder($matrix, 0, 0);
    $stampFinder($matrix, 0, $grid - 7);
    $stampFinder($matrix, $grid - 7, 0);
@endphp

<svg viewBox="0 0 {{ $size }} {{ $size }}" width="{{ $size }}" height="{{ $size }}" xmlns="http://www.w3.org/2000/svg">
    <rect x="0" y="0" width="{{ $size }}" height="{{ $size }}" fill="#fff" />
    @foreach ($matrix as $r => $row)
        @foreach ($row as $c => $dark)
            @if ($dark)
                <rect x="{{ $c * $cell }}" y="{{ $r * $cell }}" width="{{ $cell }}" height="{{ $cell }}" fill="#000" />
            @endif
        @endforeach
    @endforeach
</svg>
