import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('pos-ui', () => {
    // Dialog states
    const showPaymentDialog = ref(false);
    const showDiscountDialog = ref(false);
    const showCustomerDialog = ref(false);
    const showRecallDialog = ref(false);
    const showReceiptDialog = ref(false);

    // Discount dialog state
    const discountTarget = ref<any>(null);

    // Receipt dialog state
    const receiptData = ref<any>(null);

    // Actions
    function openPaymentDialog() {
        showPaymentDialog.value = true;
    }

    function closePaymentDialog() {
        showPaymentDialog.value = false;
    }

    function openDiscountDialog(target: any = null) {
        discountTarget.value = target;
        showDiscountDialog.value = true;
    }

    function closeDiscountDialog() {
        showDiscountDialog.value = false;
        discountTarget.value = null;
    }

    function openCustomerDialog() {
        showCustomerDialog.value = true;
    }

    function closeCustomerDialog() {
        showCustomerDialog.value = false;
    }

    function openRecallDialog() {
        showRecallDialog.value = true;
    }

    function closeRecallDialog() {
        showRecallDialog.value = false;
    }

    function openReceiptDialog(receipt: any) {
        receiptData.value = receipt;
        showReceiptDialog.value = true;
    }

    function closeReceiptDialog() {
        showReceiptDialog.value = false;
        receiptData.value = null;
    }

    return {
        // State
        showPaymentDialog,
        showDiscountDialog,
        showCustomerDialog,
        showRecallDialog,
        showReceiptDialog,
        discountTarget,
        receiptData,
        // Actions
        openPaymentDialog,
        closePaymentDialog,
        openDiscountDialog,
        closeDiscountDialog,
        openCustomerDialog,
        closeCustomerDialog,
        openRecallDialog,
        closeRecallDialog,
        openReceiptDialog,
        closeReceiptDialog,
    };
});

