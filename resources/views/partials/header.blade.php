<header class="sticky top-0 z-[5] flex items-center gap-4 border-b border-line bg-white px-5 py-3.5 md:px-7">
    <button @click="$store.ui.toggleSidebar()" class="text-lg text-ink md:hidden" aria-label="Toggle sidebar">☰</button>

    {{-- <div class="hidden max-w-[380px] flex-1 items-center gap-2 rounded-s border border-line bg-paper px-3 py-2 md:flex">
        <span class="text-[14px] text-muted">⌕</span>
        <input type="text" placeholder="Search products, orders, customers…"
            class="w-full bg-transparent text-[13.5px] outline-none placeholder:text-muted">
    </div> --}}

    <div class="ml-auto flex items-center gap-4">
        <span class="hidden text-[12.5px] text-muted sm:inline">{{ now()->format('l, d M Y') }}</span>

        {{-- Notifications dropdown --}}
        {{-- <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" type="button" class="relative p-1 text-muted hover:text-ink transition-colors"
                aria-label="Notifications">
                <svg class="w-6 h-6 stroke-current fill-none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>

                <!-- Badge -->
                <span
                    class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-tag-red text-[10px] font-bold text-white">
                    3
                </span>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false" x-transition
                class="absolute right-0 z-30 mt-3 w-64 rounded-m border border-line bg-white p-2 shadow-card">
                <p class="px-2 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-muted">Notifications</p>
                <a href="#" class="block rounded-s px-2 py-2 text-[13px] hover:bg-paper">3 items are running low
                    on stock</a>
                <a href="#" class="block rounded-s px-2 py-2 text-[13px] hover:bg-paper">Order #10230 is awaiting
                    payment</a>
                <a href="#" class="block rounded-s px-2 py-2 text-[13px] hover:bg-paper">Weekly stock count due
                    Friday</a>
            </div>
        </div> --}}
        {{-- username:u742548502_behroz --}}
        {{-- database:u742548502_behroz --}}
        {{-- password:v9GFIag#X --}}

        {{-- Profile dropdown --}}
        <div x-data="{ open: false }" class="relative border-l border-line pl-3.5">
            <button @click="open = !open" type="button" class="flex items-center gap-2">
               <img src="https://i.pravatar.cc/64?img=12" alt="" class="h-8 w-8 rounded-full object-cover">
                <div class="hidden text-left sm:block">
                    <strong class="block text-[12.5px]">{{ auth()->user()->name ?? 'Aisha Khan' }}</strong>
                    <small class="text-[11px] text-muted">Store Manager</small>
                </div>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false" x-transition
                class="absolute right-0 z-30 mt-3 w-48 rounded-m border border-line bg-white p-2 shadow-card">
                {{-- <a href="@" class="block rounded-s px-2.5 py-2 text-[13px] hover:bg-paper">My
                    Profile</a> --}}
                {{-- <a href="{{ url('/settings') }}"
                    class="block rounded-s px-2.5 py-2 text-[13px] hover:bg-paper">Settings</a> --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-s px-2.5 py-2 text-left text-[13px] text-tag-red hover:bg-tag-red-tint">Log
                        out</button>
                </form>
            </div>
        </div>
    </div>
</header>
