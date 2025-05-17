<script setup>
import { Link } from '@inertiajs/vue3';
import logoSrc from '../../../public/images/snap-svg-seeklogo.svg';
</script>

<template>
    <Link :href="'/'">
        <img :src="logoSrc" alt="Logo" class="size-16" />
    </Link>
</template>
