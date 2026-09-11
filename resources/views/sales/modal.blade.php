 <!-- Purchase Details Modal -->
 <div x-show="showModal" x-cloak @keydown.escape.window="closeModal()"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
     <div @click.away="closeModal()"
         class="bg-white border border-line rounded-[var(--radius-s)] max-w-2xl w-full shadow-xl overflow-hidden flex flex-col max-h-[90vh]">

         <!-- Modal Header -->
         <div class="px-5 py-4 bg-paper border-b border-line flex items-center justify-between">
             <div>
                 <span class="text-xs font-semibold uppercase tracking-wider text-muted">Invoice Breakdown</span>
                 <h3 class="font-display text-lg font-bold text-forest" x-text="activeSale?.invoice_number"></h3>
             </div>
             <button @click="closeModal()"
                 class="text-muted hover:text-ink text-xl font-bold leading-none">&times;</button>
         </div>

         <!-- Modal Body -->
         <div class="p-5 space-y-5 overflow-y-auto">
             <!-- Metadata Header Card -->
             <div class="grid grid-cols-2 gap-4 p-4 bg-paper border border-line rounded-[var(--radius-s)] text-xs">
                 {{-- <div>
                     <span class="text-muted block font-semibold uppercase tracking-wider">Supplier Info</span>
                     <span class="font-bold text-ink text-sm block"
                         x-text="activeSale?.supplier?.name || 'N/A'"></span>
                     <span class="font-mono text-muted block"
                         x-text="activeSale?.supplier?.phone_number || 'No Phone'"></span>
                 </div> --}}
                 <div class="text-left">
                     <span class="text-muted block font-semibold uppercase tracking-wider">Date & Time</span>
                     <span class="font-mono text-ink block font-medium"
                         x-text="formatDate(activeSale?.created_at)"></span>
                 </div>
             </div>

             <!-- Line Items Table -->
             <div class="border border-line rounded-[var(--radius-s)] overflow-hidden">
                 <table class="w-full text-left text-xs text-ink">
                     <thead class="bg-paper border-b border-line font-semibold text-muted uppercase tracking-wider">
                         <tr>
                             <th class="py-2.5 px-3">Product Code</th>
                             <th class="py-2.5 px-3">Product Name</th>
                             <th class="py-2.5 px-3 text-center">Qty</th>
                             <th class="py-2.5 px-3 text-right">Unit Cost</th>
                             <th class="py-2.5 px-3 text-right">Subtotal</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-line">
                         <template x-for="item in activeSale?.sale_items" :key="item.id">
                             <tr class="hover:bg-paper/40 transition-colors">
                                 <td class="py-2.5 px-3 font-mono text-muted" x-text="item.product?.barcode || '—'">
                                 </td>
                                 <td class="py-2.5 px-3 font-medium text-ink"
                                     x-text="item.product?.name || 'Deleted Product'"></td>
                                 <td class="py-2.5 px-3 text-center font-mono font-semibold" x-text="item.qty"></td>
                                 {{-- <td class="py-2.5 px-3 text-right font-mono"
                                     x-text=" formatMoney(item.bonus_qty)"></td> --}}
                                 <td class="py-2.5 px-3 text-right font-mono"
                                     x-text="'Rs ' + formatMoney(item.rate)"></td>
                                 <td class="py-2.5 px-3 text-right font-mono font-semibold text-ink"
                                     x-text="'Rs ' + formatMoney(item.qty * item.rate)"></td>
                             </tr>
                         </template>
                     </tbody>
                 </table>
             </div>

             <!-- Summary Box -->
             <div class="flex justify-end">
                 <div class="w-1/2 p-3 bg-paper border border-line rounded-[var(--radius-s)] space-y-1.5 text-xs">
                     {{-- <div class="flex justify-between text-muted">
                         <span>Amount Paid:</span>
                         <span class="font-mono font-semibold text-ink" x-text="'Rs ' + formatMoney(activeSale?.supplier_payment.amount)"></span>
                     </div> --}}
                     <div class="flex justify-between text-muted">
                         <span>Total Quantities:</span>
                         <span class="font-mono font-semibold text-ink" x-text="calculateTotalQty()"></span>
                     </div>
                     <div class="flex justify-between text-sm font-bold border-t border-line pt-2 text-ink">
                         <span>Gross Total:</span>
                         <span class="font-mono text-forest"
                             x-text="'Rs ' + formatMoney(activeSale?.total_amount)"></span>
                     </div>
                     <div class="flex justify-between text-sm font-bold border-t border-line pt-2 text-ink">
                         <span>Discount:</span>
                         <span class="font-mono text-forest"
                             x-text="'Rs ' + formatMoney(activeSale?.discount_amount)"></span>
                     </div>
                     <div class="flex justify-between text-sm font-bold border-t border-line pt-2 text-ink">
                         <span>Net Amount:</span>
                         <span class="font-mono text-forest"
                             x-text="'Rs ' + formatMoney(activeSale?.net_amount)"></span>
                     </div>
                 </div>
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
