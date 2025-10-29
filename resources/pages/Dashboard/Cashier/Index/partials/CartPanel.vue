<template>
    <div class="flex flex-1 flex-col rounded-lg border">
        <!-- Cart Header -->
        <div class="flex items-center justify-between border-b p-4">
            <h2 class="text-lg font-bold">{{ __('pos.cashier.cart-panel.current-order') }}</h2>
            <div class="flex gap-2">
                <Button variant="outline" size="sm" class="cursor-pointer" @click="handleHold" :disabled="cartStore.isEmpty">
                    <span class="mr-1">💾</span>
                    {{ __('pos.cashier.cart-panel.hold') }}
                </Button>
                <Button variant="outline" size="sm" class="cursor-pointer" @click="uiStore.openRecallDialog">
                    <span class="mr-1">📋</span>
                    {{ __('pos.cashier.cart-panel.recall') }}
                </Button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 space-y-2 overflow-y-auto p-4">
            <!-- Empty State -->
            <div v-if="cartStore.isEmpty" class="flex h-full flex-col items-center justify-center text-muted-foreground">
                <div class="mb-4 text-6xl">🛒</div>
                <p class="text-sm">{{ __('pos.cashier.cart-panel.cart-empty') }}</p>
                <p class="text-xs">{{ __('pos.cashier.cart-panel.add-products') }}</p>
            </div>

            <!-- Cart Items -->
            <div v-for="item in cartStore.items" :key="item.id" class="group relative rounded-lg border p-3 transition-colors hover:bg-accent">
                <div class="mb-2 flex items-start justify-between">
                    <div class="flex-1">
                        <div class="font-medium">{{ item.product_name }}</div>
                        <div class="text-sm text-muted-foreground">${{ item.unit_price.toFixed(2) }} × {{ item.quantity }}</div>
                    </div>
                    <Button variant="ghost" size="sm" class="cursor-pointer opacity-0 group-hover:opacity-100" @click="handleRemove(item)">
                        ×
                    </Button>
                </div>

                <!-- Quantity Controls & Price -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" class="cursor-pointer" @click="handleDecrease(item)">-</Button>
                        <span class="w-12 text-center">{{ item.quantity }}</span>
                        <Button variant="outline" size="sm" class="cursor-pointer" @click="handleIncrease(item)">+</Button>
                    </div>

                    <div class="flex items-center gap-2">
                        <Button v-if="!item.discount" variant="ghost" size="sm" class="cursor-pointer" @click="uiStore.openDiscountDialog(item)">
                            <span class="mr-1">%</span>
                            {{ __('pos.cashier.cart-panel.discount') }}
                        </Button>
                        <div v-else class="text-sm text-green-600">-{{ item.discount.toFixed(1) }}%</div>
                        <div class="text-lg font-bold">${{ calculateItemTotal(item).toFixed(2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-2 border-t p-4">
            <div class="flex justify-between text-sm">
                <span>{{ __('pos.cashier.cart-panel.subtotal') }}:</span>
                <span>${{ cartStore.subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="cartStore.orderDiscount > 0" class="flex justify-between text-sm text-green-600">
                <span>{{ __('pos.cashier.cart-panel.order-discount', { args: { discount: cartStore.orderDiscount.toFixed(1) } }) }}:</span>
                <span>-${{ cartStore.discountAmount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>{{ __('pos.cashier.cart-panel.tax') }} (10%):</span>
                <span>${{ cartStore.tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between border-t pt-2 text-2xl font-bold">
                <span>{{ __('pos.cashier.cart-panel.total') }}:</span>
                <span>${{ cartStore.total.toFixed(2) }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2 border-t p-4">
            <Button class="w-full cursor-pointer" size="lg" variant="default" @click="handleCheckout" :disabled="cartStore.isEmpty">
                <span class="mr-2">💳</span>
                {{ __('pos.cashier.cart-panel.checkout') }} (F9)
            </Button>
            <div class="grid grid-cols-2 gap-2">
                <Button variant="outline" class="cursor-pointer" @click="uiStore.openDiscountDialog()" :disabled="cartStore.isEmpty">
                    <span class="mr-1">%</span>
                    {{ __('pos.cashier.cart-panel.discount') }}
                </Button>
                <Button variant="outline" class="cursor-pointer" @click="handleClear" :disabled="cartStore.isEmpty">
                    <span class="mr-1">🗑️</span>
                    {{ __('pos.cashier.cart-panel.clear') }}
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Button } from '@/components/ui/button';
    import { useSound } from '../composables/useSound';
    import type { CartItem } from '../stores/cart-store';
    import { useCartStore } from '../stores/cart-store';
    import { useUIStore } from '../stores/ui-store';

    const cartStore = useCartStore();
    const uiStore = useUIStore();
    const { play } = useSound();

    function handleIncrease(item: CartItem) {
        cartStore.updateQuantity(item.id, item.quantity + 1);
    }

    function handleDecrease(item: CartItem) {
        cartStore.updateQuantity(item.id, Math.max(1, item.quantity - 1));
    }

    function handleRemove(item: CartItem) {
        cartStore.removeItem(item.id);
        play('remove');
    }

    function handleHold() {
        cartStore.holdOrder();
        play('success');
    }

    function handleClear() {
        cartStore.clearCart();
        play('clear');
    }

    function handleCheckout() {
        console.log('Checkout clicked!');
        console.log('Cart isEmpty:', cartStore.isEmpty);
        console.log('Cart items:', cartStore.items);
        console.log('Cart total:', cartStore.total);

        if (cartStore.isEmpty) {
            console.warn('Cart is empty, cannot checkout');
            return;
        }

        console.log('Opening payment dialog...');
        uiStore.openPaymentDialog();
        console.log('Payment dialog state:', uiStore.showPaymentDialog);
    }

    function calculateItemTotal(item: CartItem): number {
        const subtotal = item.unit_price * item.quantity;
        const discount = (subtotal * item.discount) / 100;
        return subtotal - discount;
    }
</script>
