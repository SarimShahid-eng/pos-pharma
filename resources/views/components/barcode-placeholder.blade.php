{{--
    Temporary barcode placeholder. NOT scannable — purely visual until a real
    barcode is wired in (e.g. milon/barcode's Code128 generator).

    To swap in the real thing later: replace the <svg>...</svg> block below
    with something like:
        {!! DNS1D::getBarcodeSVG($value, 'C128', 1.4, 45) !!}
    and delete the $hash/$bars generation logic — everything else (the
    wrapping div, the text label, the props) stays the same.

    Props:
        value  (string) — the value to encode, e.g. invoice number
        width  (int, optional, default 220)
        height (int, optional, default 50)
--}}
@php
    $barValue = $value ?? '';
    $width = $width ?? 220;
    $height = $height ?? 50;

    $hash = md5($barValue);
    $chars = str_split($hash);

    $bars = [];
    $x = 2;
    foreach ($chars as $char) {
        $barWidth = (hexdec($char) % 3) + 1; // 1-3px bar
        $bars[] = ['x' => $x, 'w' => $barWidth];
        $x += $barWidth + 2; // 2px gap between bars
    }
    $totalWidth = max($width, $x + 2);
@endphp

<div class="inline-block text-center">
    <svg viewBox="0 0 {{ $totalWidth }} {{ $height }}" width="{{ $width }}" height="{{ $height }}"
        xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <rect x="0" y="0" width="{{ $totalWidth }}" height="{{ $height }}" fill="#fff" />
        @foreach ($bars as $bar)
            <rect x="{{ $bar['x'] }}" y="3" width="{{ $bar['w'] }}" height="{{ $height - 14 }}" fill="#000" />
        @endforeach
    </svg>
    <p class="font-mono text-[9px] tracking-widest mt-0.5">{{ $barValue }}</p>
</div>
