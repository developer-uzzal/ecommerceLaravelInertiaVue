<template>
    <div>
        <h1>hi</h1>
        <Link href="/test1">test</Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

</script>

<style lang="scss" scoped>

</style>