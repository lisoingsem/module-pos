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
                class="flex h-36 cursor-pointer flex-col items-center justify-between p-3 text-center transition-all hover:bg-accent hover:shadow-md"
                @click="handleAddProduct(product)"
            >
                <div class="flex flex-1 flex-col items-center gap-2">
                    <div v-if="product.image_url" class="flex h-16 w-16 items-center justify-center">
                        <img :src="product.image_url" :alt="product.name" class="max-h-full max-w-full object-contain" />
                    </div>
                    <div v-else class="flex h-16 w-16 items-center justify-center rounded bg-muted">
                        <span class="text-2xl">📦</span>
                    </div>
                </div>
                <div class="w-full space-y-1">
                    <div class="line-clamp-2 text-sm font-medium">{{ product.name }}</div>
                    <div class="text-lg font-bold text-primary">${{ parseFloat(product.price || 0).toFixed(2) }}</div>
                </div>
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Button } from '@/components/ui/button';
    import { useSound } from '../composables/useSound';
    import { useCartStore } from '../stores/cart-store';

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
