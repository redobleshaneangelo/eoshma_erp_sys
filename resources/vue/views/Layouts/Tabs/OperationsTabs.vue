<template>
    <router-link
        v-if="hasPermission('Projects.view')"
        to="/projects"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="$route.path.startsWith('/projects')
            ? 'bg-[#0c8ce9] text-white'
            : 'text-gray-700 hover:bg-gray-100'"
    >
        <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/projects') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11zm-3-7v2h-2v2h2v2h2v-2h2v-2h-2v-2h-2z"/>
        </svg>
        <span>Projects</span>
    </router-link>
</template>


<script setup>
    import { useAuthStore } from '@/stores/auth'
    const auth = useAuthStore()
    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }
</script>