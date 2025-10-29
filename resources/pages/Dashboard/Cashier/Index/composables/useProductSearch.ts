import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface Product {
    id: number;
    name: string;
    price: number;
    sku: string;
    barcode?: string;
    image?: string;
    stock: number;
    category_id?: number;
    media?: Array<{ full_url: string }>;
}

interface Category {
    id: number;
    name: string;
    children?: Category[];
}

export function useProductSearch() {
    const searchQuery = ref('');
    const selectedCategory = ref<number | null>(null);
    const products = ref<Product[]>([]);
    const categories = ref<Category[]>([]);
    const isLoading = ref(false);

    // Fetch products from API  
    async function fetchProducts() {
        isLoading.value = true;
        try {
            const response = await axios.get(route('api.product.dashboard.data.products'));
            if (response.data.success) {
                products.value = response.data.data;
            }
        } catch (error) {
            console.error('Failed to fetch products:', error);
        } finally {
            isLoading.value = false;
        }
    }

    // Fetch categories from API
    async function fetchCategories() {
        try {
            const response = await axios.get(route('api.product.dashboard.data.categories'));
            if (response.data.success) {
                categories.value = response.data.data;
            }
        } catch (error) {
            console.error('Failed to fetch categories:', error);
        }
    }

    const filteredProducts = computed(() => {
        let filtered = products.value;

        // Filter by category
        if (selectedCategory.value !== null) {
            filtered = filtered.filter((p) => p.category_id === selectedCategory.value);
        }

        // Filter by search (name, SKU, or barcode)
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            filtered = filtered.filter(
                (p) =>
                    p.name.toLowerCase().includes(query) ||
                    p.sku?.toLowerCase().includes(query) ||
                    p.barcode?.toLowerCase().includes(query),
            );
        }

        return filtered;
    });

    function selectCategory(categoryId: number | null) {
        selectedCategory.value = categoryId;
    }

    function setSearch(query: string) {
        searchQuery.value = query;
    }

    function clearSearch() {
        searchQuery.value = '';
        selectedCategory.value = null;
    }

    // Auto-fetch on mount
    onMounted(() => {
        fetchProducts();
        fetchCategories();
    });

    return {
        searchQuery,
        selectedCategory,
        products,
        categories,
        filteredProducts,
        isLoading,
        selectCategory,
        setSearch,
        clearSearch,
        fetchProducts,
        fetchCategories,
    };
}

