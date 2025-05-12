<script setup lang="ts">
import ProductFilters from '@/Components/Products/ProductFilters.vue';
import ProductList from '@/Components/Products/ProductList.vue';
import { useFiltersStore } from '@/stores/filters';
// import ProductFilters from '../../Components/Products/ProductFilters.vue';
// import ProductList from '../../Components/Products/ProductList.vue';
// import AppLayout from '@/Layouts/AppLayout.vue'; // Assuming a main layout, adjust if needed

// Interface for a single product, based on ProductResource
interface IProduct {
  id: number;
  name: string;
  description: string | null;
  price: string; // Or number, depending on formatting
  status: string;
  category: {
    id: number;
    name: string;
  };
  created_at: string; // Or Date
}

// Interface for paginated product data
interface IPaginatedProducts {
  data: IProduct[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number | null;
    last_page: number;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
  };
}

// Interface for filter state
interface IFilterState {
  name?: string;
  category_id?: number | null;
  status?: string;
}

// Interface for a category (for filter dropdown)
interface ICategory {
  id: number;
  name: string;
}

/**
 * Component props.
 * @property {IPaginatedProducts} products - Paginated list of products.
 * @property {IFilterState} filters - Current filter values.
 * @property {ICategory[]} categories - List of categories for filter dropdown.
 */
interface Props {
  products: IPaginatedProducts;
  filters: IFilterState;
  categories: ICategory[];
}

const props = withDefaults(defineProps<Props>(), {});

const filtersStore = useFiltersStore();
// Placeholder for filter update logic
function handleFilterUpdate(newFilters: IFilterState) {
  filtersStore.updateFilters(newFilters);
  // console.log('Filters updated:', newFilters);
  // router.get(route('products.index'), newFilters, { preserveState: true, preserveScroll: true });
}
</script>

<template>
  <!-- <AppLayout title="Products"> -->
    <div>
      <h1 class="text-2xl font-semibold mb-4">Product Listing</h1>

      <!-- Product Filters Component -->
      <ProductFilters
        :filters="props.filters"
        :categories="props.categories"
        class="mb-4"
        placeholder="ProductFilters component placeholder"
        aria-label="Product Filters Section"
        />
        <!-- @update:filters="handleFilterUpdate" -->


      <!-- Product List Component -->
      <ProductList
        :products="props.products.data"
        class="mt-4"
        placeholder="ProductList component placeholder"
        aria-label="Product List Section"
        />

      <!-- Placeholder for Pagination -->
      <div class="mt-4">
        Pagination placeholder (e.g., props.products.links)
      </div>
    </div>
  <!-- </AppLayout> -->
</template>

<style scoped>
/* Scoped styles for Index.vue */
</style>
