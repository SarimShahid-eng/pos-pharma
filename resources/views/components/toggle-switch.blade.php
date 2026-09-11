@props(['id', 'active' => false, 'route' => ''])

<div x-data="{
    active: {{ json_encode((bool) $active) }},
    loading: false,
    async toggleStatus() {
        if (this.loading) return;

        const previousState = this.active;
        this.active = !this.active;
        this.loading = true;

        try {
            const response = await fetch('{{ $route }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: this.active })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to update status.');
            }

            if (window.Toast) {
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
            }
        } catch (error) {
            this.active = previousState; // Revert toggle on error

            if (window.Toast) {
                Toast.fire({
                    icon: 'error',
                    title: error.message || 'Could not update status.'
                });
            }
        } finally {
            this.loading = false;
        }
    }
}" class="inline-flex items-center">
    <button type="button" @click="toggleStatus()" :disabled="loading" :class="active ? 'bg-forest' : 'bg-gray-300'"
        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-forest/20 disabled:opacity-50"
        role="switch" :aria-checked="active">
        <span :class="active ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
            <!-- Loading indicator inside toggle knob -->
            <svg x-show="loading" class="h-5 w-5 animate-spin text-muted p-0.5" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </span>
    </button>
</div>
