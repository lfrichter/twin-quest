<script setup lang="ts">
// @ts-ignore
import { defineProps, defineEmits, ref, watch } from 'vue';
import { useFiltersStore } from '@/Stores/filters';
import { ICategory, IFilterState,  } from '@/Types';

/**
 * Component props.
 * @property {IFilterState} filters - Current filter values.
 * @property {ICategory[]} categories - List of categories for filter dropdown.
 */
const props = defineProps<{
  filters: IFilterState;
  categories: ICategory[];
}>();

/**
 * Component emits.
 * @event update:filters - Emitted when filter values change.
 */
const emit = defineEmits<{
  (e: 'update:filters', value: IFilterState): void;
}>();

const filtersStore = useFiltersStore();
const localFilters = ref({ ...filtersStore.filters });
// Local reactive state for form inputs, initialized from props
// const localFilters = ref<IFilterState>({ ...props.filters });

// Watch for changes in localFilters and emit update event
// This is a basic implementation; consider debouncing for text inputs in a real app
// watch(localFilters, (newFilters) => {
//   emit('update:filters', newFilters);
// }, { deep: true });

watch(localFilters, (newFilters: IFilterState) => {
  emit('update:filters', newFilters);
  filtersStore.updateFilters(newFilters);
}, { deep: true });

// Function to reset filters (example)
// function resetFilters() {
//   localFilters.value = { name: '', category_id: null, status: '' };
// }
</script>

<template>
  <div class="p-4 border rounded-md">
    <h2 class="text-lg font-medium mb-2">Filters</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label for="filter-name" class="block text-sm font-medium text-gray-700">Name</label>
        <input
          id="filter-name"
          v-model="localFilters.name"
          type="text"
          placeholder="Filter by name"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
      </div>
      <div>
        <label for="filter-category" class="block text-sm font-medium text-gray-700">Category</label>
        <select
          id="filter-category"
          v-model="localFilters.category_id"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        >
          <option :value="null">All Categories</option>
          <option v-for="category in props.categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>
      <div>
        <label for="filter-status" class="block text-sm font-medium text-gray-700">Status</label>
        <select
          id="filter-status"
          v-model="localFilters.status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        >
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="discontinued">Discontinued</option>
        </select>
      </div>
    </div>
    <!-- Example reset button -->
    <!-- <button @click="resetFilters" class="mt-2 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Reset Filters</button> -->
    <p class="mt-2 text-xs text-gray-500">ProductFilters component placeholder content.</p>
  </div>
</template>

<style scoped>
/* Scoped styles for ProductFilters.vue */
</style>
