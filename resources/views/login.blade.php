<!DOCTYPE html>
<html lang="en" class="h-full bg-paper">
@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - {{ $shopName }}</title>

    <!-- Google Fonts: Fraunces & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-paper text-ink font-sans antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 selection:bg-forest selection:text-white">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Brand Header / Logo Icon -->
        <div class="flex justify-center">
            <div
                class="h-14 w-14 rounded-[var(--radius-m)] bg-forest flex items-center justify-center text-wheat shadow-md ring-4 ring-forest/10">
                <svg class="w-7 h-7 stroke-current fill-none stroke-[2]" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
        </div>

        <h2 class="mt-5 text-center font-display text-3xl font-bold tracking-tight text-forest">
            {{ $shopName }}
        </h2>
        <p class="mt-1.5 text-center text-xs font-medium uppercase tracking-wider text-muted">
            POS & Inventory Terminal Management
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Main Panel -->
        <div class="panel border border-line bg-white px-6 py-8 shadow-sm sm:rounded-[var(--radius-m)] sm:px-10">
            <form class="space-y-5" action="{{ route('login.auth') }}" method="POST">
                @csrf

                <!-- Session Status / General Alerts -->
                @if (session('status'))
                    <div
                        class="rounded-[var(--radius-s)] bg-forest/10 p-3 text-xs font-medium text-forest border border-forest/20">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Email Address -->
                <div>
                    <label for="email"
                        class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}" autofocus
                            class="block w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2.5 text-sm text-ink placeholder:text-muted/50 focus:border-forest focus:bg-white focus:outline-none focus:ring-2 focus:ring-forest/20 transition-all"
                            placeholder="operator@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-tag-red font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password with Toggle -->
                <div x-data="{ showPassword: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-muted">
                            Password
                        </label>
                        {{-- @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-semibold text-forest hover:text-forest-light transition-colors">
                                Forgot password?
                            </a>
                        @endif --}}
                    </div>
                    <div class="relative">
                        <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                            autocomplete="current-password" required
                            class="block w-full rounded-[var(--radius-s)] border border-line bg-paper pl-3.5 pr-10 py-2.5 text-sm text-ink placeholder:text-muted/50 focus:border-forest focus:bg-white focus:outline-none focus:ring-2 focus:ring-forest/20 transition-all"
                            placeholder="••••••••">

                        <!-- Eye Toggle Button -->
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-muted hover:text-ink transition-colors"
                            aria-label="Toggle password visibility">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 014.288-.938c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-1.53 1.285L3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-tag-red font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Keep Signed In -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 rounded-[3px] border-line text-forest focus:ring-forest/20 accent-forest cursor-pointer">
                        <span class="text-xs font-medium text-muted">Keep me signed in</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-light focus:outline-none focus:ring-2 focus:ring-forest/30 focus:ring-offset-2 transition-all cursor-pointer">
                        <span>Sign In</span>
                        <svg class="w-4 h-4 stroke-current fill-none stroke-[2]" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-muted space-y-1">
            <p class="font-medium">{{ $shopName }} POS &bull; Enterprise Edition</p>
            <p class="text-[11px] text-muted/70">&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>

</body>

</html>
