<template>
    <auth-layout/>
</template>

<script setup>
    import { onMounted } from 'vue';
    import { useAuthStore } from '@/stores/auth'
    const auth = useAuthStore()

    onMounted(() => {
        auth.pageTitle = 'Payroll';
    });
</script>