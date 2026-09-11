<!-- Return Sale Modal -->
<div x-show="showReturnModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40" @click="closeReturnModal()"></div>

    <!-- Modal Panel -->
    <div class="relative w-full max-w-3xl bg-white rounded-[var(--radius-s)] border border-line shadow-xl max-h-[90vh] flex flex-col"
        @click.outside="closeReturnModal()">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-line">
            <div>
                <h2 class="text-sm font-bold text-forest">Return Sale Items</h2>
                <p class="text-[11px] text-muted font-mono" x-text="'Invoice #' + (returnSale?.invoice_number || '')"></p>
            </div>
            <button type="button" @click="closeReturnModal()"
                class="rounded-[var(--radius-s)] p-1.5 text-muted hover:text-ink hover:bg-paper transition-colors">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="px-5 py-4 space-y-3 overflow-y-auto">
            <p class="text-[11px] text-muted">
                Set the quantity to return for each product. Leave at 0 to skip an item. Return quantity
                cannot exceed the quantity purchased.
            </p>

            <template x-if="returnError">
                <div class="rounded-[var(--radius-s)] border border-tag-red bg-tag-red/5 px-3 py-2 text-xs font-medium text-tag-red"
                    x-text="returnError"></div>
            </template>

            <template x-if="returnSuccess">
                <div class="rounded-[var(--radius-s)] border border-forest bg-forest-tint/40 px-3 py-2 text-xs font-medium text-forest">
                    Return recorded successfully. Refreshing...
                </div>
            </template>

            <!-- Table -->
            <div class="overflow-x-auto border border-line rounded-[var(--radius-s)]">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper border-b border-line text-[11px] font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-2.5 px-3">Product</th>
                            <th class="py-2.5 px-3 w-28 text-center">Sale Qty</th>
                            <th class="py-2.5 px-3 w-32 text-center">Return Qty</th>
                            <th class="py-2.5 px-3 w-32 text-center">Remaining Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <template x-for="(item, index) in returnItems" :key="item.purchase_item_id">
                            <tr>
                                <td class="py-2 px-3 text-xs font-medium" x-text="item.product_name"></td>
                                <td class="py-2 px-3 text-center font-mono text-xs" x-text="item.sold_qty"></td>
                                <td class="py-2 px-3">
                                    <input type="number" step="1" min="0" :max="item.sold_qty"
                                        x-model.number="item.return_qty"
                                        @input="clampReturnQty(index)"
                                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-2 py-1.5 text-xs font-mono text-center text-ink focus:border-forest focus:bg-white focus:outline-none">
                                </td>
                                <td class="py-2 px-3 text-center font-mono text-xs text-muted"
                                    x-text="calculateRemainingQuantity(index)">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Date and Reason Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Return Date Input -->
                <div>
                    <label for="return_date" class="block text-[11px] font-semibold uppercase tracking-wider text-muted mb-1">
                        Return Date <span class="text-tag-red">*</span>
                    </label>
                    <input type="date" id="return_date" name="return_date" x-model="returnDate" required
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-2 text-xs text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                </div>

                <!-- Reason Field -->
                <div class="md:col-span-2">
                    <label for="return_reason" class="block text-[11px] font-semibold uppercase tracking-wider text-muted mb-1">
                        Reason (optional)
                    </label>
                    <textarea id="return_reason" name="reason" x-model="returnReason" rows="1"
                        placeholder="e.g. Damaged stock, wrong item supplied..."
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper p-2 text-xs text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors"></textarea>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-2.5 px-5 py-4 border-t border-line">
            <button type="button" @click="closeReturnModal()" :disabled="returnSubmitting"
                class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Cancel
            </button>
            <button type="button" @click="submitReturn()" :disabled="returnSubmitting"
                class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-tag-red px-4 py-2 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-text="returnSubmitting ? 'Processing...' : 'Confirm Return'"></span>
            </button>
        </div>
    </div>
</div>
