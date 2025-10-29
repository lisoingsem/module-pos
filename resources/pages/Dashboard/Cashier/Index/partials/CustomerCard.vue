<template>
    <div class="rounded-lg border p-4">
        <div class="mb-2 flex items-center justify-between">
            <h3 class="font-semibold">{{ __('pos.cashier.customer-card.customer') }}</h3>
            <Button variant="outline" size="sm" @click="uiStore.openCustomerDialog">
                {{ customerStore.selectedCustomer ? __('pos.cashier.customer-card.change') : __('pos.cashier.customer-card.select') }}
            </Button>
        </div>

        <div v-if="customerStore.selectedCustomer" class="space-y-1">
            <div class="font-medium">{{ customerStore.selectedCustomer.name }}</div>
            <div class="text-sm text-muted-foreground">{{ customerStore.selectedCustomer.email }}</div>
            <div v-if="customerStore.selectedCustomer.loyalty_points" class="text-sm text-primary">
                {{ customerStore.selectedCustomer.loyalty_points }} {{ __('pos.cashier.customer-card.points') }}
            </div>
        </div>
        <div v-else class="text-sm text-muted-foreground">
            {{ __('pos.cashier.customer-card.no-customer-selected') }}
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Button } from '@/components/ui/button';
    import { useCustomerStore } from '../stores/customer-store';
    import { useUIStore } from '../stores/ui-store';

    const customerStore = useCustomerStore();
    const uiStore = useUIStore();
</script>
