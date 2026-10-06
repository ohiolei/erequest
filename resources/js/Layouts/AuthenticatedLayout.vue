<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import StaffAuthenticatedLayout from '@/Layouts/StaffAuthenticatedLayout.vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const page = usePage();
const layout = computed(() => (
    page.props.auth.user?.roles?.includes('student')
        ? StudentLayout
        : StaffAuthenticatedLayout
));
</script>

<template>
    <component :is="layout">
        <slot />
    </component>
</template>
