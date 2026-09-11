@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
@endphp
<footer class="flex justify-between border-t border-line px-5 py-4 text-[11.5px] text-muted md:px-7">
    <span>&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</span>
    <span>v1.0.0 &middot; Built with Laravel</span>
</footer>
