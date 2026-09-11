{{--
    Reusable date-range quick-select. Drop this into any filter form —
    it renders its own "Quick Range" <select> plus the actual from/to
    <input type="date"> fields, named to match the from_date/to_date
    convention already used across every other filter form in the app.
    So it's a straight drop-in replacement for a plain pair of date
    inputs, not an extra thing you wire up alongside them.

    Usage:
        <x-date-range-select :from="$fromDate" :to="$toDate" />

    With custom field names (e.g. two ranges on one page):
        <x-date-range-select :from="$fromDate" :to="$toDate"
            from-name="range2_from" to-name="range2_to" />

    Props:
        from     (string, optional) - initial from-date value (Y-m-d). Defaults to today.
        to       (string, optional) - initial to-date value (Y-m-d). Defaults to today.
        fromName (string, optional) - name attribute for the from input, default 'from_date'
        toName   (string, optional) - name attribute for the to input, default 'to_date'
--}}
@props([
    'from' => null,
    'to' => null,
    'fromName' => 'from_date',
    'toName' => 'to_date',
])

<div x-data="dateRangeSelect(@js($from ?? now()->format('Y-m-d')), @js($to ?? now()->format('Y-m-d')))" class="flex flex-col sm:flex-row gap-3">
    <div class="w-full sm:w-44">
        <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">Quick Range</label>
        <select x-model="preset" @change="applyPreset()"
            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="this_week">This Week</option>
            <option value="this_month">This Month</option>
            <option value="custom">Custom Range</option>
        </select>
    </div>
    <div class="flex-1">
        <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">From Date</label>
        <input type="date" name="{{ $fromName }}" x-model="fromDate" @change="preset = 'custom'"
            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
    </div>
    <div class="flex-1">
        <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">To Date</label>
        <input type="date" name="{{ $toName }}" x-model="toDate" @change="preset = 'custom'"
            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
    </div>
</div>
