<script setup lang="ts">
import TestLayout from '@/Layouts/TestLayout.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
// @ts-ignore
import { watch, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import ProductFilters from '@/Components/Products/ProductFilters.vue';
import ProductList from '@/Components/Products/ProductList.vue';
import Pagination from '@/Components/Pagination.vue';
import { ICategory, IPaginatedProducts, IFilterState } from '@/Types';
import { useProductFilterStore } from '@/Stores/useProductFilterStore';

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

// Initialize and use the Pinia store
const productFilterStore = useProductFilterStore();

// Make safe versions of props
// const safeFilters = computed(() => props.filters || { name: '', status: '' });
const safeProducts = computed(() => props.products || { data: [], meta: { links: [] } });
const safeCategories = computed(() => props.categories || []);

const filterFormForComponent = computed<IFilterState>({
  get: () => productFilterStore.currentFilters,
  set: (newValues: IFilterState) => {
    // When ProductFilters updates its model, this calls the store action
    // which handles debouncing and fetching.
    productFilterStore.updateAndFetchFilters(newValues);
  }
});

const isLoading = computed(() => productFilterStore.isLoadingStatus);

const handlePageChange = (url: string | null) => {
  productFilterStore.handlePageChange(url);
};

const resetFiltersInComponent = () => {
  productFilterStore.resetFilters();
};

watch(() => props.filters, (newServerFilters: IFilterState | undefined) => {
  if (newServerFilters) {
    productFilterStore.initializeFilters(newServerFilters as IFilterState);
  }
}, { deep: true });

onMounted(() => {
  productFilterStore.initializeFilters(props.filters as IFilterState);
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
          <div class="bg-white dark:bg-gray-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Product Filters -->
            <ProductFilters
              v-if="safeCategories.length > 0"
              :categories="safeCategories"
              v-model="filterFormForComponent"
              @reset-filters="resetFiltersInComponent"
              class="mb-6"
              aria-label="Product filter controls"
            />

            <Transition name="fade" mode="out-in">
              <!-- Loading Indicator -->
              <div v-if="isLoading" class="text-center py-4" role="status" aria-live="polite">
                <p>Loading products...</p>
              </div>
              <!-- Product List and Pagination -->
              <div v-else>
                <!-- Product List -->
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
            </Transition>

          </div>
        </div>
      </div>

    </TestLayout>
  </div>
</template>


<style scoped>
/* Transition for fading between loading/content states */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Transition for the product list appearance/disappearance and height changes */
.list-transition-enter-active,
.list-transition-leave-active {
  transition: all 0.5s ease-in-out; /* Animate opacity, max-height, and transform */
  overflow: hidden; /* Important for max-height transition */
}

.list-transition-enter-from {
  max-height: 0;
  opacity: 0;
  transform: translateY(20px); /* Optional: slight upward movement from bottom */
}
.list-transition-enter-to {
  max-height: 1500px; /* Adjust if your content can exceed this height. Needs to be larger than the maximum possible content height. */
  opacity: 1;
  transform: translateY(0);
}

.list-transition-leave-from {
  max-height: 1500px; /* Adjust to match enter-to */
  opacity: 1;
  transform: translateY(0);
}
.list-transition-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(20px); /* Optional: slight downward movement */
}

/* Optional: Add some styling to the wrappers if needed, though Tailwind classes are mostly used */
.product-list-wrapper {
  /* Styles for the div wrapping ProductList and Pagination */
}
.no-products-message {
  /* Styles for the "No products found" message container */
}
</style>
