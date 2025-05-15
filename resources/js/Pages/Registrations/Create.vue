<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Access flash messages
const page = usePage();
const successMessage = computed(() => page.props.flash?.success);
</script>

<template>
    <Head title="Create Registration" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Display success message if any -->
                    <div v-if="successMessage" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                        {{ successMessage }}
                    </div>

                    <p>Registration form will be here.</p>
                    <p class="mt-4">This is a placeholder page for the registration form.</p>
                    <p class="mt-2">The actual form fields and real-time validation will be implemented in the next steps.</p>
                </div>
            </div>
        </div>
    </div>
</template>
