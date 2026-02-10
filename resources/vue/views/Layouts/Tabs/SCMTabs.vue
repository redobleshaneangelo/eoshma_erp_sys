<template>
    <router-link
        v-if="hasPermission('Suppliers.view')"
        to="/inventory"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="$route.path.startsWith('/inventory')
            ? 'bg-[#0c8ce9] text-white'
            : 'text-gray-700 hover:bg-gray-100'"
    >
        <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/inventory') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h18v4H3V3zm0 14h7v4H3v-4zm9 0h9v4h-9v-4zM3 9h18v6H3V9z"/>
        </svg>
        <span>Inventory</span>
    </router-link>
    
    <router-link
        v-if="hasPermission('Suppliers.view')"
        to="/suppliers"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="$route.path.startsWith('/suppliers')
            ? 'bg-[#0c8ce9] text-white'
            : 'text-gray-700 hover:bg-gray-100'"
    >
        <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/suppliers') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
        </svg>
        <span>Suppliers</span>
    </router-link>

    <router-link
        v-if="hasPermission('Suppliers.view')"
        to="/material-requests"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="$route.path.startsWith('/material-requests')
            ? 'bg-[#0c8ce9] text-white'
            : 'text-gray-700 hover:bg-gray-100'"
    >
        <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/material-requests') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-1-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.28 0-.5-.22-.5-.5 0-.09.02-.18.06-.27L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
        </svg>
        <span>Material Requests</span>
    </router-link>
</template>


<script setup>
    import { useAuthStore } from '@/stores/auth'
    const auth = useAuthStore()
    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }
</script>