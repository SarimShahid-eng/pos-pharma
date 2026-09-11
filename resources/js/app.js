//
import Alpine from 'alpinejs';

// Shared UI state (sidebar open/closed on mobile) accessible from any component
Alpine.store('ui', {
    sidebarOpen: false,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    closeSidebar() {
        this.sidebarOpen = false;
    },
});
Alpine.data('dateRangeSelect', (initialFrom, initialTo) => ({
    fromDate: initialFrom,
    toDate: initialTo,
    preset: 'custom',

    init() {
        this.preset = this.detectPreset();
    },

    // Figures out which preset (if any) the current from/to values match,
    // so reloading a filtered page shows the right option selected
    // instead of always resetting to "Custom Range".
    detectPreset() {
        const today = this.fmt(new Date());
        const yesterday = this.fmt(this.addDays(new Date(), -1));
        const weekStart = this.fmt(this.startOfWeek(new Date()));
        const monthStart = this.fmt(this.startOfMonth(new Date()));

        if (this.fromDate === today && this.toDate === today) return 'today';
        if (this.fromDate === yesterday && this.toDate === yesterday) return 'yesterday';
        if (this.fromDate === weekStart && this.toDate === today) return 'this_week';
        if (this.fromDate === monthStart && this.toDate === today) return 'this_month';
        return 'custom';
    },

    applyPreset() {
        const today = new Date();

        if (this.preset === 'today') {
            this.fromDate = this.fmt(today);
            this.toDate = this.fmt(today);
        } else if (this.preset === 'yesterday') {
            const y = this.addDays(today, -1);
            this.fromDate = this.fmt(y);
            this.toDate = this.fmt(y);
        } else if (this.preset === 'this_week') {
            this.fromDate = this.fmt(this.startOfWeek(today));
            this.toDate = this.fmt(today);
        } else if (this.preset === 'this_month') {
            this.fromDate = this.fmt(this.startOfMonth(today));
            this.toDate = this.fmt(today);
        }
        // 'custom' -> leave fromDate/toDate exactly as the user set them
    },

    // Week starts Monday — adjust here if your business week starts Sunday.
    startOfWeek(d) {
        const date = new Date(d);
        const day = date.getDay(); // 0 = Sunday ... 6 = Saturday
        const diff = (day === 0 ? -6 : 1) - day;
        date.setDate(date.getDate() + diff);
        return date;
    },

    startOfMonth(d) {
        return new Date(d.getFullYear(), d.getMonth(), 1);
    },

    addDays(d, n) {
        const date = new Date(d);
        date.setDate(date.getDate() + n);
        return date;
    },

    fmt(d) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    },
}));


Alpine.directive('select2', (el, { expression }, { evaluateLater, effect }) => {
    const getValue = evaluateLater(expression);
    let syncing = false;     // guards the effect()'s programmatic .val().trigger()
    let dispatching = false; // guards the manual dispatchEvent below from re-entering itself

    Alpine.nextTick(() => {
        const $el = $(el);

        $el.select2({
            width: '100%',
            placeholder: el.getAttribute('placeholder') || 'Select an option',
            allowClear: Boolean(el.getAttribute('data-allow-clear')),
        });

        // Sync Select2 changes to Alpine model / variable
        $el.on('change', () => {
            if (syncing || dispatching) return; // stop re-entrant calls
            dispatching = true;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
            dispatching = false;
        });

        // Watch external reactivity changes (e.g., form reset or scanner updates)
        effect(() => {
            getValue(value => {
                if ($el.val() !== value) {
                    syncing = true;
                    $el.val(value).trigger('change.select2');
                    syncing = false;
                }
            });
        });
    });
});

window.Alpine = Alpine;
Alpine.start();
