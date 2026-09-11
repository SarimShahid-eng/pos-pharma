  <!-- Payment Detail Modal -->
        <div x-show="showModal" x-cloak @keydown.escape.window="closeModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div @click.away="closeModal()"
                class="bg-white border border-line rounded-[var(--radius-s)] max-w-lg w-full shadow-xl overflow-hidden flex flex-col">

                <!-- Modal Header -->
                <div class="px-5 py-4 bg-paper border-b border-line flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted">Payment Voucher</span>
                        <h3 class="font-display text-lg font-bold text-forest"
                            x-text="activePayment?.voucher_no"></h3>
                    </div>
                    <button @click="closeModal()"
                        class="text-muted hover:text-ink text-xl font-bold leading-none">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4 p-4 bg-paper border border-line rounded-[var(--radius-s)] text-xs">
                        <div>
                            <span class="text-muted block font-semibold uppercase tracking-wider">Supplier</span>
                            <span class="font-bold text-ink text-sm block"
                                x-text="activePayment?.supplier?.name || 'N/A'"></span>
                            <span class="font-mono text-muted block"
                                x-text="activePayment?.supplier?.phone_number || ''"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-muted block font-semibold uppercase tracking-wider">Date</span>
                            <span class="font-mono text-ink block font-medium"
                                x-text="formatDate(activePayment?.date)"></span>
                        </div>
                    </div>

                    {{-- <div
                        class="flex justify-between items-center p-4 border border-line rounded-[var(--radius-s)] bg-white text-xs">
                        <span class="font-semibold uppercase tracking-wider text-muted">Transaction Type</span>
                        <span class="font-bold uppercase tracking-wider text-sm"
                            :class="activePayment?.type === 'debit' ? 'text-forest' : 'text-tag-red'"
                            x-text="activePayment?.type"></span>
                    </div> --}}

                    <div
                        class="flex justify-between items-center p-4 border border-line rounded-[var(--radius-s)] bg-paper text-xs">
                        <span class="font-semibold uppercase tracking-wider text-muted">Amount Paid</span>
                        <span class="font-mono text-xl font-bold text-forest"
                            x-text="'Rs ' + formatMoney(activePayment?.amount)"></span>
                    </div>

                    <div x-show="activePayment?.notes"
                        class="p-3 border border-line rounded-[var(--radius-s)] bg-paper text-xs">
                        <span class="text-muted block font-semibold uppercase tracking-wider mb-1">Remarks / Notes</span>
                        <p class="text-ink font-mono" x-text="activePayment?.notes"></p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-5 py-3 bg-paper border-t border-line flex justify-end">
                    <button @click="closeModal()"
                        class="px-4 py-2 bg-white border border-line hover:border-forest rounded-[var(--radius-s)] text-xs font-semibold text-ink transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
