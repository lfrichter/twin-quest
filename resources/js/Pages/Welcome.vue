<script setup lang="ts">
// Import necessary components from Inertia and Vue
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-4">
        <!-- Logo Section -->
        <div class="mb-12">
            <img src="/images/snap-svg-seeklogo.svg" alt="Logo" class="size-16" />
        </div>

        <!-- Welcome Message Section -->
        <div class="text-center mb-12">
            <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Navigation Links Section -->
        <nav class="flex flex-col sm:flex-row items-center space-y-6 sm:space-y-0 sm:space-x-8">
            <Link
                :href="route('products.index')"
                class="px-10 py-4 bg-indigo-600 text-white text-xl font-semibold rounded-lg shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-400 focus:ring-opacity-75 transition-transform transform hover:scale-105 duration-200 ease-in-out"
            >
                Products
            </Link>
            <Link
                :href="route('registrations.create')"
                class="px-10 py-4 bg-purple-500 text-white text-xl font-semibold rounded-lg shadow-xl hover:bg-purple-600 focus:outline-none focus:ring-4 focus:ring-purple-300 focus:ring-opacity-75 transition-transform transform hover:scale-105 duration-200 ease-in-out"
            >
                Registration
            </Link>
        </nav>
    </div>
</template>

<style scoped>
/* Add any page-specific styles if needed */
</style>
