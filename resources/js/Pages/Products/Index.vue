<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; // Ou o layout que estiver usando, ex: AuthenticatedLayout
import ProductFilters from '@/Components/Products/ProductFilters.vue';
import ProductList from '@/Components/Products/ProductList.vue';
import Pagination from '@/Components/Pagination.vue'; // Componente de paginação do Laravel
import { ICategory, IPaginatedProducts, IFilterState,  } from '@/Types';

// Define props received from the controller
const props = defineProps<{
  products: IPaginatedProducts;
  filters: IFilterState; // Initial filters from backend
  categories: ICategory[];
}>();

// Reactive state for the filter form
// Initialize with current filters from props or defaults
const filterForm = reactive<IFilterState>({
  name: props.filters?.name || '',
  category_id: props.filters?.category_id || null,
  status: props.filters?.status || '',
});

// Loading state for UI feedback
const isLoading = ref(false);

/**
 * Debounce function to limit the rate of API calls.
 * @param fn The function to debounce.
 * @param delay The delay in milliseconds.
 */
function debounce<T extends (...args: any[]) => void>(fn: T, delay: number) {
  let timeoutId: ReturnType<typeof setTimeout>;
  return function(this: ThisParameterType<T>, ...args: Parameters<T>) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
}

/**
 * Fetches products from the backend based on the current filterForm state.
 * This function is called when filters change or pagination is used.
 */
const fetchProducts = (options: { preserveScroll?: boolean } = {}) => {
  isLoading.value = true;
  router.get(route('products.index'), // Assuming 'products.index' is the named route for listing products
    { ...filterForm } as any, // Cast to any to satisfy Inertia's data type, or use PickBy for defined keys
    {
      preserveState: true,
      preserveScroll: options.preserveScroll || false, // Preserve scroll on filter, reset on page change unless specified
      replace: true, // Replace history state for filter changes to avoid too many entries
      onFinish: () => {
        isLoading.value = false;
      },
    }
  );
};

// Debounced version of fetchProducts for filter changes
const debouncedFetchProducts = debounce(fetchProducts, 500); // 500ms debounce delay

// Watch for changes in filterForm and fetch new products
// Deep watch is needed for object changes
watch(filterForm, () => {
  // When filters change, typically we want to go to page 1 and preserve scroll position
  // However, the current fetchProducts will use the current page from props.products.meta.current_page
  // To go to page 1, we'd need to modify filterForm to include page: 1 or handle it in fetchProducts
  // For now, let's assume fetchProducts re-fetches with current page, or backend resets to page 1 on filter change.
  // A common UX is to reset to page 1 when filters change.
  // To do this, you might call fetchProducts with a specific page parameter or ensure your backend handles it.
  debouncedFetchProducts({ preserveScroll: true });
}, { deep: true });

/**
 * Handles page navigation events from the Pagination component.
 * @param url The URL of the page to navigate to.
 */
const handlePageChange = (url: string | null) => {
  if (!url) return;
  isLoading.value = true;
  router.get(url,
    { ...filterForm } as any, // Pass current filters
    {
      preserveState: true, // Preserve filter state in components
      preserveScroll: false, // Usually, scroll to top on page change
      replace: false, // Add to history for page changes
      onFinish: () => {
        isLoading.value = false;
      },
    }
  );
};

// Initialize filterForm with props.filters on component mount
onMounted(() => {
  // props.filters contains the filters active on the server-side render
  // Update filterForm to match these, if they differ from defaults
  if (props.filters) {
    filterForm.name = props.filters.name || '';
    filterForm.category_id = props.filters.category_id || null;
    filterForm.status = props.filters.status || '';
  }
});

</script>

<template>
  <AppLayout title="Products">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Product Listing
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Product Filters Component -->
          <ProductFilters
            :categories="categories"
            :filters="filterForm"
            @update:filters="filterForm = $event"
            class="mb-6"
          />

          <!-- Loading Indicator -->
          <div v-if="isLoading" class="text-center py-4">
            <p>Loading products...</p>
            <!-- You can use a spinner component here -->
          </div>

          <!-- Product List Component -->
          <div v-else>
            <ProductList :products="props.products.data" />

            <!-- Pagination Component -->
            <Pagination
                v-if="props.products.meta.total > 0 && props.products.data.length > 0"
                :links="props.products.meta.links"
                @navigate="handlePageChange"
                class="mt-6"
            />
            <div v-else-if="!isLoading && props.products.data.length === 0" class="text-center py-4 text-gray-500">
                No products found matching your criteria.
            </div>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>
