<script setup lang="ts">
import { defineProps } from 'vue';
import { IProduct  } from '@/Types';


/**
 * Component props.
 * @property {IProduct[]} products - Array of products to display.
 */
const props = defineProps<{
  products: IProduct[];
}>();
</script>

<template>
  <div class="border rounded-md">
    <h2 class="text-lg font-medium mb-2 p-4 border-b">Product List</h2>
    <div v-if="props.products.length === 0" class="p-4 text-gray-500">
      No products found.
    </div>
    <ul v-else class="divide-y divide-gray-200">
      <li v-for="product in props.products" :key="product.id" class="p-4">
        <h3 class="text-md font-semibold">{{ product.name }}</h3>
        <p class="text-sm text-gray-600">Price: {{ product.price }}</p>
        <p class="text-sm text-gray-600">Category: {{ product.category?.name }}</p>
        <p class="text-sm text-gray-600">Status: {{ product.status }}</p>
      </li>
    </ul>
    <p class="p-4 text-xs text-gray-500 border-t">ProductList component placeholder content.</p>
  </div>
</template>

<style scoped>
/* Scoped styles for ProductList.vue */
</style>
