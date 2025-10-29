<template>
    <div class="flex-1 overflow-y-auto rounded-lg border p-4">
        <div v-if="isLoading" class="flex h-full items-center justify-center text-muted-foreground">
            <span class="mr-2 h-5 w-5 animate-spin rounded-full border-2 border-primary border-t-transparent"></span>
            {{ __('pos.cashier.product-grid.loading') }}
        </div>

        <div v-else-if="products.length === 0" class="flex h-full flex-col items-center justify-center text-muted-foreground">
            <div class="mb-4 text-6xl">📦</div>
            <p class="text-sm">{{ __('pos.cashier.product-grid.no-products-found') }}</p>
        </div>

        <div v-else class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            <Button
                v-for="product in products"
                :key="product.id"
                variant="outline"
                class="flex h-32 flex-col items-center justify-center space-y-1 p-2 text-center"
                @click="handleAddProduct(product)"
            >
                <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="mb-1 h-12 w-12 object-contain" />
                <div class="text-sm font-medium">{{ product.name }}</div>
                <div class="text-xs text-muted-foreground">${{ parseFloat(product.price || 0).toFixed(2) }}</div>
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Button } from '@/components/ui/button';
    import { useSound } from '../Index/composables/useSound';
    import { useCartStore } from '../Index/stores/cart-store';

    interface Product {
        id: number;
        name: string;
        price: string | number;
        sku: string;
        image_url?: string;
    }

    interface Props {
        products: Product[];
        isLoading: boolean;
    }

    const props = defineProps<Props>();

    const cartStore = useCartStore();
    const { play } = useSound();

    function handleAddProduct(product: Product) {
        const price = parseFloat(String(product.price || 0));

        cartStore.addItem({
            product_id: product.id,
            product_name: product.name,
            quantity: 1,
            unit_price: price,
            discount: 0,
        });

        play('add');
    }
</script>
