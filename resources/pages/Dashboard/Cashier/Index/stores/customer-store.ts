import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCustomerStore = defineStore('pos-customer', () => {
    const selectedCustomer = ref<any>(null);

    function selectCustomer(customer: any) {
        selectedCustomer.value = customer;
    }

    function clearCustomer() {
        selectedCustomer.value = null;
    }

    return {
        selectedCustomer,
        selectCustomer,
        clearCustomer,
    };
});

