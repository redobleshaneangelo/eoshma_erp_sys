<template>
    <router-link
        v-if="hasPermission('Clients.view')"
        to="/clients"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="$route.path.startsWith('/clients')
            ? 'bg-[#0c8ce9] text-white'
            : 'text-gray-700 hover:bg-gray-100'"
    >
        <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/clients') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-5 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm2.5 1.5c0 1.1-1.34 2.5-4.5 2.5s-4.5-1.4-4.5-2.5c0-.28.22-.5.5-.5h8c.28 0 .5.22.5.5z"/>
        </svg>
        <span>Clients</span>
    </router-link>
    
    <router-link
        v-if="hasPermission('Prebiddings.view')"
        to="/prebiddings"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="$route.path.startsWith('/prebiddings')
            ? 'bg-[#0c8ce9] text-white'
            : 'text-gray-700 hover:bg-gray-100'"
    >
        <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/prebiddings') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
            <path d="M1 21h12v2H1v-2zM5.24 8.07l2.83-2.83 14.14 14.14-2.83 2.83L5.24 8.07zM12.32 1l5.66 5.66-2.83 2.83-5.66-5.66L12.32 1z"/>
        </svg>
        <span>Prebiddings</span>
    </router-link>
</template>


<script setup>
    import { useAuthStore } from '@/stores/auth'
    const auth = useAuthStore()
    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }
</script>