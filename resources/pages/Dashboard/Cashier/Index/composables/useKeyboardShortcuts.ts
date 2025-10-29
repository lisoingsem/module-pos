import { onMounted, onUnmounted } from 'vue';

interface ShortcutHandlers {
    onSearch?: () => void;
    onCheckout?: () => void;
    onDiscount?: () => void;
    onHold?: () => void;
    onRecall?: () => void;
    onClear?: () => void;
    onCustomer?: () => void;
}

export function useKeyboardShortcuts(handlers: ShortcutHandlers) {
    function handleKeydown(event: KeyboardEvent) {
        // F2: Focus search
        if (event.key === 'F2') {
            event.preventDefault();
            handlers.onSearch?.();
        }

        // F9 or Ctrl+Enter: Checkout
        if (event.key === 'F9' || (event.ctrlKey && event.key === 'Enter')) {
            event.preventDefault();
            handlers.onCheckout?.();
        }

        // Ctrl+D: Discount
        if (event.ctrlKey && event.key === 'd') {
            event.preventDefault();
            handlers.onDiscount?.();
        }

        // Ctrl+H: Hold
        if (event.ctrlKey && event.key === 'h') {
            event.preventDefault();
            handlers.onHold?.();
        }

        // Ctrl+R: Recall
        if (event.ctrlKey && event.key === 'r') {
            event.preventDefault();
            handlers.onRecall?.();
        }

        // Ctrl+Shift+C: Clear (to avoid conflict with copy)
        if (event.ctrlKey && event.shiftKey && event.key === 'C') {
            event.preventDefault();
            handlers.onClear?.();
        }

        // Ctrl+K: Customer
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault();
            handlers.onCustomer?.();
        }
    }

    onMounted(() => {
        window.addEventListener('keydown', handleKeydown);
    });

    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeydown);
    });

    return {
        // No exposed methods needed - automatically registers/unregisters
    };
}

