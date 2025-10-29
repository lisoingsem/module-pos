<template>
    <Dialog
        v-model:open="uiStore.showCustomerDialog"
        :title="__('pos.cashier.customer-dialog.title')"
        :description="__('pos.cashier.customer-dialog.description')"
    >
        <div class="space-y-4">
            <!-- Search Customer -->
            <Input v-model="searchQuery" :placeholder="__('pos.cashier.customer-dialog.search-placeholder')" />

            <!-- Customer List -->
            <div class="max-h-60 overflow-y-auto rounded-md border">
                <div v-if="filteredCustomers.length === 0" class="p-4 text-center text-muted-foreground">
                    {{ __('pos.cashier.customer-dialog.no-customers-found') }}
                </div>
                <div
                    v-for="customer in filteredCustomers"
                    :key="customer.id"
                    class="flex cursor-pointer items-center justify-between p-3 hover:bg-accent"
                    @click="handleSelectCustomer(customer)"
                >
                    <div>
                        <div class="font-medium">{{ customer.name }}</div>
                        <div class="text-sm text-muted-foreground">{{ customer.email }}</div>
                    </div>
                    <div v-if="customer.loyalty_points" class="text-sm text-primary">
                        {{ customer.loyalty_points }} {{ __('pos.cashier.customer-dialog.points') }}
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" @click="handleCancel">
                    {{ __('common.cancel') }}
                </Button>
                <Button @click="handleClearCustomer" variant="destructive" :disabled="!customerStore.selectedCustomer">
                    {{ __('pos.cashier.customer-dialog.clear-customer') }}
                </Button>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
    import { computed, onMounted, ref } from 'vue';

    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useSound } from '../composables/useSound';
    import { useCartStore } from '../stores/cart-store';
    import { useCustomerStore } from '../stores/customer-store';
    import { useUIStore } from '../stores/ui-store';

    import Dialog from '@/components/modify/Dialog.vue';
    const uiStore = useUIStore();
    const cartStore = useCartStore();
    const customerStore = useCustomerStore();
    const { play } = useSound();

    const searchQuery = ref('');
    const customers = ref<any[]>([]);

    const filteredCustomers = computed(() => {
        const query = searchQuery.value.toLowerCase();
        return customers.value.filter((c) => c.name.toLowerCase().includes(query) || c.email.toLowerCase().includes(query));
    });

    async function fetchCustomers() {
        try {
            // TODO: Implement customer API endpoint
            // const response = await axios.get('/api/dashboard/data/customers');
            // if (response.data.success) {
            //     customers.value = response.data.data;
            // }

            // Mock data for now
            customers.value = [
                { id: 1, name: 'John Doe', email: 'john@example.com', loyalty_points: 150 },
                { id: 2, name: 'Jane Smith', email: 'jane@example.com', loyalty_points: 200 },
                { id: 3, name: 'Bob Wilson', email: 'bob@example.com', loyalty_points: 75 },
            ];
        } catch (error) {
            console.error('Failed to fetch customers:', error);
        }
    }

    function handleSelectCustomer(customer: any) {
        customerStore.selectCustomer(customer);
        cartStore.setCustomer(customer.id);
        play('success');
        uiStore.closeCustomerDialog();
    }

    function handleClearCustomer() {
        customerStore.clearCustomer();
        cartStore.setCustomer(null);
        play('remove');
        uiStore.closeCustomerDialog();
    }

    function handleCancel() {
        uiStore.closeCustomerDialog();
    }

    onMounted(() => {
        fetchCustomers();
    });
</script>
