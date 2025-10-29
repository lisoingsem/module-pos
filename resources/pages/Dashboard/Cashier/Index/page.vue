<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="flex h-[calc(100vh-4rem)] gap-4 p-4">
            <!-- Left Panel: Product Grid -->
            <div class="flex w-2/3 flex-col space-y-4">
                <!-- Search & Filters -->
                <div class="space-y-3">
                    <!-- Search with Barcode -->
                    <div class="relative">
                        <Input
                            ref="searchInput"
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search products or scan barcode (F2)..."
                            class="w-full pr-10"
                        />
                        <div class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground">
                            <kbd class="rounded border px-1.5 py-0.5 text-xs">F2</kbd>
                        </div>
                    </div>

                    <!-- Category Filters -->
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        <Button
                            v-for="category in categories"
                            :key="category.id"
                            :variant="selectedCategory === category.id ? 'default' : 'outline'"
                            size="sm"
                            @click="selectCategory(category.id)"
                        >
                            {{ category.name }}
                        </Button>
                        <Button :variant="selectedCategory === null ? 'default' : 'outline'" size="sm" @click="selectCategory(null)"> All </Button>
                    </div>
                </div>

                <!-- Product Grid -->
                <ProductGrid :products="filteredProducts" :is-loading="isLoading" />
            </div>

            <!-- Right Panel: Cart & Actions -->
            <div class="flex w-1/3 flex-col space-y-4">
                <!-- Customer Section -->
                <CustomerCard />

                <!-- Cart -->
                <CartPanel />
            </div>
        </div>

        <!-- Dialogs - All state managed by stores -->
        <PaymentDialog />
        <DiscountDialog />
        <CustomerDialog />
        <HoldRecallDialog />
        <ReceiptPreview />
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { onMounted, ref } from 'vue';

    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';

    import { useKeyboardShortcuts } from './composables/useKeyboardShortcuts';
    import { useProductSearch } from './composables/useProductSearch';
    import { useSound } from './composables/useSound';
    import { useCartStore } from './stores/cart-store';
    import { useUIStore } from './stores/ui-store';

    import CartPanel from './partials/CartPanel.vue';
    import CustomerCard from './partials/CustomerCard.vue';
    import CustomerDialog from './partials/CustomerDialog.vue';
    import DiscountDialog from './partials/DiscountDialog.vue';
    import HoldRecallDialog from './partials/HoldRecallDialog.vue';
    import PaymentDialog from './partials/PaymentDialog.vue';
    import ProductGrid from './partials/ProductGrid.vue';
    import ReceiptPreview from './partials/ReceiptPreview.vue';

    interface Props {
        title: string;
        terminal?: any;
        shift?: any;
    }

    const props = defineProps<Props>();

    // Stores & Composables
    const cartStore = useCartStore();
    const uiStore = useUIStore();
    const { play } = useSound();
    const { searchQuery, selectedCategory, categories, filteredProducts, isLoading, selectCategory } = useProductSearch();

    // Local state
    const searchInput = ref<any>(null);
    const breadcrumbs = [
        { title: 'Dashboard', href: route('dashboard.index') },
        { title: 'POS', href: '#' },
        { title: 'Cashier', href: route('dashboard.pos.cashier.index') },
    ];

    // Helper to focus search input
    function focusSearchInput() {
        if (searchInput.value) {
            // Handle both direct input element and component with $el
            const inputElement = searchInput.value.$el || searchInput.value;
            if (inputElement && typeof inputElement.focus === 'function') {
                inputElement.focus();
            }
        }
    }

    // Keyboard shortcuts (only setup after mount to avoid focus errors)
    onMounted(() => {
        // Set shift ID if available
        if (props.shift?.id) {
            cartStore.setShift(props.shift.id);
        }

        useKeyboardShortcuts({
            onSearch: focusSearchInput,
            onCheckout: () => !cartStore.isEmpty && uiStore.openPaymentDialog(),
            onDiscount: () => !cartStore.isEmpty && uiStore.openDiscountDialog(),
            onHold: () => !cartStore.isEmpty && cartStore.holdOrder(),
            onRecall: () => uiStore.openRecallDialog(),
            onClear: () => !cartStore.isEmpty && cartStore.clearCart(),
            onCustomer: () => uiStore.openCustomerDialog(),
        });

        focusSearchInput();
        cartStore.loadCurrentOrder();
    });
</script>
