<template>
    <Dialog v-model:open="uiStore.showReceiptDialog">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ __('pos.cashier.receipt-dialog.title') }}</DialogTitle>
                <DialogDescription>{{ __('pos.cashier.receipt-dialog.description') }}</DialogDescription>
            </DialogHeader>

            <!-- Receipt Preview (scrollable) -->
            <div class="max-h-[60vh] overflow-y-auto rounded border bg-white p-6">
                <div v-if="receiptHtml" v-html="receiptHtml"></div>
                <div v-else class="py-8 text-center text-muted-foreground">
                    <div class="mb-4 text-4xl">🧾</div>
                    <p>{{ __('pos.cashier.receipt-dialog.loading') }}</p>
                </div>
            </div>

            <DialogFooter class="flex justify-between">
                <div class="flex gap-2">
                    <Button variant="outline" class="cursor-pointer" @click="printReceipt">
                        <span class="mr-2">🖨️</span>
                        {{ __('pos.cashier.receipt-dialog.print') }}
                    </Button>
                    <Button variant="outline" class="cursor-pointer" @click="emailReceipt">
                        <span class="mr-2">📧</span>
                        {{ __('pos.cashier.receipt-dialog.email') }}
                    </Button>
                </div>
                <Button variant="default" class="cursor-pointer" @click="startNewOrder">
                    {{ __('pos.cashier.receipt-dialog.new-order') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    import { Button } from '@/components/ui/button';
    import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

    import { useUIStore } from '../stores/ui-store';

    const uiStore = useUIStore();

    // Computed receipt HTML
    const receiptHtml = computed(() => {
        if (!uiStore.receiptData) return '';

        const data = uiStore.receiptData;
        const order = data.order || {};
        const items = order.items || [];

        // Generate HTML receipt
        return `
            <div class="receipt" style="font-family: 'Courier New', monospace; font-size: 14px; max-width: 400px;">
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 18px; font-weight: bold;">${data.store_name || 'Store Name'}</h2>
                    <p style="margin: 5px 0;">${data.store_address || ''}</p>
                    <p style="margin: 5px 0;">${data.store_phone || ''}</p>
                </div>

                <div style="margin-bottom: 10px;">
                    <p style="margin: 2px 0;"><strong>Order #:</strong> ${order.id || 'N/A'}</p>
                    <p style="margin: 2px 0;"><strong>Date:</strong> ${new Date().toLocaleString()}</p>
                    <p style="margin: 2px 0;"><strong>Cashier:</strong> ${data.cashier || 'N/A'}</p>
                </div>

                <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin: 10px 0;">
                    ${items
                        .map(
                            (item: any) => `
                        <div style="margin-bottom: 8px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span><strong>${item.quantity}x</strong> ${item.product_name || item.name}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-left: 20px;">
                                <span>@ $${parseFloat(item.unit_price || item.price || 0).toFixed(2)}</span>
                                <span><strong>$${(parseFloat(item.unit_price || item.price || 0) * parseInt(item.quantity || 1)).toFixed(2)}</strong></span>
                            </div>
                        </div>
                    `,
                        )
                        .join('')}
                </div>

                <div style="margin: 10px 0;">
                    <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                        <span>Subtotal:</span>
                        <span>$${parseFloat(order.subtotal || 0).toFixed(2)}</span>
                    </div>
                    ${
                        order.discount > 0
                            ? `
                        <div style="display: flex; justify-content: space-between; margin: 5px 0; color: green;">
                            <span>Discount (${order.discount_percent || 0}%):</span>
                            <span>-$${parseFloat(order.discount || 0).toFixed(2)}</span>
                        </div>
                    `
                            : ''
                    }
                    <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                        <span>Tax (10%):</span>
                        <span>$${parseFloat(order.tax || 0).toFixed(2)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin: 10px 0; padding-top: 10px; border-top: 2px solid #000; font-size: 16px;">
                        <strong>TOTAL:</strong>
                        <strong>$${parseFloat(order.total || 0).toFixed(2)}</strong>
                    </div>
                </div>

                ${
                    data.payment_method
                        ? `
                    <div style="border-top: 1px dashed #000; padding-top: 10px; margin-top: 10px;">
                        <p style="margin: 5px 0;"><strong>Payment Method:</strong> ${data.payment_method.toUpperCase()}</p>
                        ${
                            data.cash_tendered
                                ? `
                            <p style="margin: 5px 0;"><strong>Cash Tendered:</strong> $${parseFloat(data.cash_tendered).toFixed(2)}</p>
                            <p style="margin: 5px 0;"><strong>Change:</strong> $${parseFloat(data.change || 0).toFixed(2)}</p>
                        `
                                : ''
                        }
                    </div>
                `
                        : ''
                }

                <div style="text-align: center; margin-top: 20px; padding-top: 10px; border-top: 2px solid #000;">
                    <p style="margin: 5px 0; font-size: 12px;">Thank you for your purchase!</p>
                    <p style="margin: 5px 0; font-size: 12px;">Please come again</p>
                </div>
            </div>
        `;
    });

    function printReceipt() {
        if (!receiptHtml.value) return;

        const printWindow = window.open('', '_blank');
        if (printWindow) {
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        @media print {
                            body { margin: 0; padding: 20px; }
                            @page { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${receiptHtml.value}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() {
                                window.close();
                            };
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    }

    function emailReceipt() {
        // TODO: Implement email functionality
        alert('Email functionality coming soon!');
    }

    function startNewOrder() {
        uiStore.closeReceiptDialog();
    }
</script>
