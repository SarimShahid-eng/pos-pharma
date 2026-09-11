@extends('layouts.master')

@section('title', 'Page Under Development')

@section('content')
<div class="flex min-h-[60vh] flex-col items-center justify-center text-center px-4">
    <div class="rounded-full bg-paper border border-line p-4 mb-4">
        <svg class="w-10 h-10 text-forest" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-ink mb-2">Page Under Development</h1>
    <p class="text-sm text-muted max-w-md mb-6">
        We are actively working on this feature. It will be available in an upcoming update.
    </p>

    <div class="flex items-center gap-3">
        <a href="{{ url()->previous() }}"
           class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
            Go Back
        </a>
        <a href="{{ route('dashboard') }}"
           class="rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white hover:opacity-90 transition-opacity">
            Return to Dashboard
        </a>
    </div>
</div>
@endsection
