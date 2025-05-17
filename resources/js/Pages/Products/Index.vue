<script setup lang="ts">
import { route } from 'ziggy-js'
import TestLayout from '@/Layouts/TestLayout.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
// @ts-ignore
import { ref, watch, onMounted, computed } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import ProductFilters from '@/Components/Products/ProductFilters.vue';
import ProductList from '@/Components/Products/ProductList.vue';
import Pagination from '@/Components/Pagination.vue';
import { ICategory, IPaginatedProducts, IFilterState } from '@/Types';

// Define props with TypeScript interfaces and provide default values to prevent null errors
const props = withDefaults(defineProps<{
  products?: IPaginatedProducts;
  filters?: IFilterState;
  categories?: ICategory[];
}>(), {
  products: () => ({ data: [], meta: { links: [] }, links: [] } as unknown as IPaginatedProducts),
  filters: () => ({ name: '', status: '' } as IFilterState),
  categories: () => []
});

// Debug props
console.log('Props received:', props);

// Make safe versions of props
const safeFilters = computed(() => props.filters || { name: '', status: '' });
const safeProducts = computed(() => props.products || { data: [], meta: { links: [] } });
const safeCategories = computed(() => props.categories || []);

// Reactive filter form, initialized with safe props.filters
const filterForm = ref<IFilterState>({
  name: safeFilters.value.name || '',
  category_id: safeFilters.value.category_id,
  status: safeFilters.value.status || '',
});

// Loading state for UI feedback
const isLoading = ref(false);

// Debounce utility for API calls - Definido antes de ser usado
function debounce<T extends (...args: any[]) => void>(fn: T, delay: number) {
  let timeoutId: ReturnType<typeof setTimeout>;
  return function (this: ThisParameterType<T>, ...args: Parameters<T>) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
}

/**
 * Fetches products based on filter state.
 * @param options - Navigation options for Inertia.js
 */
const fetchProducts = (options: { preserveScroll?: boolean; page?: number } = {}) => {
  isLoading.value = true;
  console.log('Fetching products with filters:', filterForm.value, 'Page:', options.page);
  try {
    const url = route('products.index');
    console.log('URL generated:', url);
    router.get(url, { ...filterForm.value, page: options.page },
    {
      preserveState: true,
      preserveScroll: options.preserveScroll || false,
      replace: true,
      onFinish: () => {
        isLoading.value = false;
        console.log('Fetch completed, isLoading:', isLoading.value);
      },
    });
  } catch (e) {
    console.error("Erro ao usar a função route():", e); // Captura erro específico aqui
  }
};

// Debounced fetch for filter changes - Usando a função definida acima
const debouncedFetchProducts = debounce(fetchProducts, 500);

/**
 * Handles pagination navigation.
 * @param url - Pagination URL
 */
const handlePageChange = (url: string | null) => {
  if (!url) return;
  isLoading.value = true;
  console.log('Navigating to pagination URL:', url);
  router.get(
    url,
    { ...filterForm.value },
    {
      preserveState: true,
      preserveScroll: false,
      replace: false,
      onFinish: () => {
        isLoading.value = false;
        console.log('Pagination fetch completed');
      },
    }
  );
};

/**
 * Resets filters to default values.
 */
const resetFilters = () => {
  filterForm.value = {
    name: '',
    category_id: undefined,
    status: '',
  };
  console.log('Filters reset:', filterForm.value);
  fetchProducts(); // Chamar diretamente fetchProducts após reset
};

// Watch filterForm for changes
watch(
  filterForm,
  () => {
    console.log('Filter form changed:', filterForm.value);
    debouncedFetchProducts({ preserveScroll: true });
  },
  { deep: true }
);

// Sync filterForm with props.filters on mount
onMounted(() => {
  filterForm.value = {
    name: safeFilters.value.name || '',
    category_id: safeFilters.value.category_id,
    status: safeFilters.value.status || '',
  };
  console.log('Mounted, filterForm initialized:', filterForm.value);
});
</script>

<template>
  <div>
    <Head title="Product Listing" />

    <TestLayout>
      <template #logo>
          <AuthenticationCardLogo />
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Product Filters -->
            <ProductFilters
              v-if="safeCategories.length > 0"
              :categories="safeCategories"
              v-model="filterForm"
              @reset-filters="resetFilters"
              class="mb-6"
              aria-label="Product filter controls"
            />

            <!-- Loading Indicator -->
            <div v-if="isLoading" class="text-center py-4" role="status" aria-live="polite">
              <p>Loading products...</p>
            </div>

            <!-- Product List and Pagination -->
            <div v-else>
              <ProductList
                v-if="safeProducts.data && safeProducts.data.length > 0"
                :products="safeProducts.data"
              />

              <!-- Pagination -->
              <Pagination
                v-if="safeProducts.meta?.total > 0 && safeProducts.data?.length > 0"
                :links="safeProducts.meta.links || []"
                @navigate="handlePageChange"
                class="mt-6"
                aria-label="Pagination controls"
              />
              <div
                v-else-if="safeProducts.data?.length === 0"
                class="text-center py-4 text-gray-500"
                role="alert"
              >
                No products found matching your criteria.
              </div>
            </div>
          </div>
        </div>
      </div>

    </TestLayout>
  </div>
</template>
