<template>
    <Dialog
        v-model:open="uiStore.showRecallDialog"
        :title="__('pos.cashier.hold-recall-dialog.title')"
        :description="__('pos.cashier.hold-recall-dialog.description')"
    >
        <div class="space-y-4">
            <div v-if="cartStore.heldOrders.length === 0" class="p-4 text-center text-muted-foreground">
                {{ __('pos.cashier.hold-recall-dialog.no-held-orders') }}
            </div>

            <div v-else class="max-h-60 overflow-y-auto rounded-md border">
                <div
                    v-for="order in cartStore.heldOrders"
                    :key="order.id"
                    class="flex cursor-pointer items-center justify-between p-3 hover:bg-accent"
                    @click="handleRecall(order.id)"
                >
                    <div>
                        <div class="font-medium">
                            {{ __('pos.cashier.hold-recall-dialog.order-id', { args: { id: order.id } }) }}
                        </div>
                        <div class="text-sm text-muted-foreground">
                            {{ __('pos.cashier.hold-recall-dialog.items', { args: { count: order.items.length } }) }} |
                            {{ formatDate(order.timestamp) }}
                        </div>
                    </div>
                    <div class="font-bold">${{ calculateHeldOrderTotal(order).toFixed(2) }}</div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" @click="handleCancel">
                    {{ __('common.cancel') }}
                </Button>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
    import { Button } from '@/components/ui/button';
    import { useSound } from '../composables/useSound';
    import type { HeldOrder } from '../stores/cart-store';
    import { useCartStore } from '../stores/cart-store';
    import { useUIStore } from '../stores/ui-store';

    import Dialog from '@/components/modify/Dialog.vue';
    const uiStore = useUIStore();
    const cartStore = useCartStore();
    const { play } = useSound();

    function calculateHeldOrderTotal(order: HeldOrder): number {
        const subtotal = order.items.reduce((sum, item) => {
            const itemTotal = item.unit_price * item.quantity;
            const itemDiscount = (itemTotal * item.discount) / 100;
            return sum + (itemTotal - itemDiscount);
        }, 0);
        const orderDiscountAmount = (subtotal * order.discount) / 100;
        const subtotalAfterDiscount = subtotal - orderDiscountAmount;
        const tax = subtotalAfterDiscount * 0.1; // 10% tax
        return subtotalAfterDiscount + tax;
    }

    function formatDate(timestamp: number): string {
        return new Date(timestamp).toLocaleString();
    }

    function handleRecall(orderId: string) {
        cartStore.recallOrder(orderId);
        play('success');
        uiStore.closeRecallDialog();
    }

    function handleCancel() {
        uiStore.closeRecallDialog();
    }
</script>
