import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export interface CartItem {
    id: number;
    product_id: number;
    product_name: string;
    quantity: number;
    unit_price: number;
    discount: number;
}

export interface HeldOrder {
    id: string;
    items: CartItem[];
    customer_id: number | null;
    discount: number;
    timestamp: number;
}

export const useCartStore = defineStore('pos-cart', () => {
    // State
    const items = ref<CartItem[]>([]);
    const orderDiscount = ref(0);
    const customerId = ref<number | null>(null);
    const shiftId = ref<number | null>(null);
    const heldOrders = ref<HeldOrder[]>([]);

    // Getters
    const itemCount = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0));

    const subtotal = computed(() => {
        return items.value.reduce((sum, item) => {
            const itemTotal = item.unit_price * item.quantity;
            const itemDiscount = (itemTotal * item.discount) / 100;
            return sum + (itemTotal - itemDiscount);
        }, 0);
    });

    const discountAmount = computed(() => {
        return (subtotal.value * orderDiscount.value) / 100;
    });

    const subtotalAfterDiscount = computed(() => {
        return subtotal.value - discountAmount.value;
    });

    const tax = computed(() => {
        return subtotalAfterDiscount.value * 0.1; // 10% tax
    });

    const total = computed(() => {
        return subtotalAfterDiscount.value + tax.value;
    });

    const isEmpty = computed(() => items.value.length === 0);

    // Actions
    function addItem(item: Omit<CartItem, 'id'>) {
        const existing = items.value.find((i) => i.product_id === item.product_id);

        if (existing) {
            existing.quantity += item.quantity;
        } else {
            items.value.push({
                ...item,
                id: Date.now(),
            });
        }
    }

    function removeItem(itemId: number) {
        const index = items.value.findIndex((i) => i.id === itemId);
        if (index > -1) {
            items.value.splice(index, 1);
        }
    }

    function updateQuantity(itemId: number, quantity: number) {
        const item = items.value.find((i) => i.id === itemId);
        if (item) {
            item.quantity = Math.max(1, quantity);
        }
    }

    function updateItemDiscount(itemId: number, discount: number) {
        const item = items.value.find((i) => i.id === itemId);
        if (item) {
            item.discount = Math.max(0, Math.min(100, discount));
        }
    }

    function setOrderDiscount(discount: number) {
        orderDiscount.value = Math.max(0, Math.min(100, discount));
    }

    function setCustomer(id: number | null) {
        customerId.value = id;
    }

    function setShift(id: number | null) {
        shiftId.value = id;
    }

    function clearCart() {
        items.value = [];
        orderDiscount.value = 0;
        customerId.value = null;
    }

    function holdOrder() {
        if (items.value.length === 0) return;

        const order: HeldOrder = {
            id: `hold-${Date.now()}`,
            items: JSON.parse(JSON.stringify(items.value)),
            customer_id: customerId.value,
            discount: orderDiscount.value,
            timestamp: Date.now(),
        };

        heldOrders.value.push(order);
        clearCart();
    }

    function recallOrder(orderId: string) {
        const order = heldOrders.value.find((o) => o.id === orderId);
        if (!order) return;

        items.value = JSON.parse(JSON.stringify(order.items));
        customerId.value = order.customer_id;
        orderDiscount.value = order.discount;

        // Remove from held orders
        const index = heldOrders.value.findIndex((o) => o.id === orderId);
        if (index > -1) {
            heldOrders.value.splice(index, 1);
        }
    }

    function loadOrder(order: any) {
        if (order?.items) {
            items.value = order.items;
        }
    }

    async function loadCurrentOrder() {
        try {
            // Fetch current draft order from backend
            const response = await axios.get(route('dashboard.pos.cashier.index'));
            // Since we removed currentOrder from props, cart starts empty
            // User can recall held orders if needed
        } catch (error) {
            console.error('Failed to load current order:', error);
        }
    }

    async function checkout(paymentData: any) {
        try {
            // Step 1: Create cart/order (prices fetched from DB for security)
            const cartData = {
                items: items.value.map((item) => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    // Note: unit_price and discount are NOT sent to prevent price manipulation
                    // Backend fetches prices from database
                })),
                customer_id: customerId.value,
                order_discount: orderDiscount.value,
                shift_id: shiftId.value,
            };

            const cartResponse = await axios.post(route('dashboard.pos.cashier.cart.create'), cartData);

            if (!cartResponse.data.success) {
                throw new Error('Failed to create order');
            }

            const orderId = cartResponse.data.order.id;

            // Step 2: Process checkout with payment
            const checkoutData = {
                payment_method: paymentData.payment_method,
                amount: paymentData.amount,
                cash_tendered: paymentData.cash_tendered,
                change: paymentData.change,
            };

            const checkoutResponse = await axios.post(
                route('dashboard.pos.cashier.checkout', { order: orderId }),
                checkoutData
            );

            if (checkoutResponse.data.success) {
                // Clear cart after successful checkout
                clearCart();
                return checkoutResponse.data;
            }

            throw new Error(checkoutResponse.data.message || 'Checkout failed');
        } catch (error) {
            console.error('Checkout error:', error);
            throw error;
        }
    }

    return {
        // State
        items,
        orderDiscount,
        customerId,
        shiftId,
        heldOrders,
        // Getters
        itemCount,
        subtotal,
        discountAmount,
        subtotalAfterDiscount,
        tax,
        total,
        isEmpty,
        // Actions
        addItem,
        removeItem,
        updateQuantity,
        updateItemDiscount,
        setOrderDiscount,
        setCustomer,
        setShift,
        clearCart,
        holdOrder,
        recallOrder,
        loadOrder,
        loadCurrentOrder,
        checkout,
    };
});
