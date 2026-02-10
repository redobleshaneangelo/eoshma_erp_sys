<template>
    <router-link
        v-if="hasPermission('Accounting.view')"
        to="/project-finance"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/project-finance')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/project-finance') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M19 13h-6v6h6v-6zm4-12h-8v2h8V1zM4 5h16v2H4zm10 16h-6v6h6v-6zM4 9h4v2H4zm0 4h4v2H4zm5-4h4v2H9zm5 0h4v2h-4z"
            />
        </svg>
        <span>Project Finance</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounting.view')"
        to="/general-ledger"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/general-ledger')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/general-ledger') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z"
            />
        </svg>
        <span>General Ledger</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounting.view')"
        to="/payables"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/payables')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/payables') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"
            />
        </svg>
        <span>Payables</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounting.view')"
        to="/receivables"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/receivables')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/receivables') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.13-2.59-1.41 1.41L10.5 17l4.96-6.29z"
            />
        </svg>
        <span>Receivables</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounting.view')"
        to="/cashflow"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/cashflow')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/cashflow') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"
            />
        </svg>
        <span>Cashflow</span>
    </router-link>
</template>


<script setup>
    import { useAuthStore } from '@/stores/auth'
    const auth = useAuthStore()
    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }
</script>