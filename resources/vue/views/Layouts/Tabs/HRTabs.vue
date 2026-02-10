<template>
    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/accounts"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/accounts')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/accounts') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm0 4c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 12H6v-1.4c0-2 4-3.1 6-3.1s6 1.1 6 3.1V19z"
            />
        </svg>
        <span>Accounts</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/recruitment"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/recruitment')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/recruitment') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"
            />
        </svg>
        <span>Recruitment</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/disciplinary"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/disciplinary')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/disciplinary') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M1 21h12v2H1v-2zM5.24 8.07l2.83-2.83 14.14 14.14-2.83 2.83L5.24 8.07zM12.32 1l5.66 5.66-2.83 2.83-5.66-5.66L12.32 1z"
            />
        </svg>
        <span>Disciplinary</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/timekeeping"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/timekeeping')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/timekeeping') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"
            />
        </svg>
        <span>Timekeeping</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/payroll"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/payroll')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/payroll') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"
            />
        </svg>
        <span>Payroll</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/leave-and-absence"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/leave-and-absence')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/leave-and-absence') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.13-2.59-1.41 1.41L10.5 17l4.96-6.29z"
            />
        </svg>
        <span>Leave &amp; Absence</span>
    </router-link>

    <router-link
        v-if="hasPermission('Accounts.view')"
        to="/employee-clearance"
        class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
        :class="
            $route.path.startsWith('/employee-clearance')
                ? 'bg-[#0c8ce9] text-white'
                : 'text-gray-700 hover:bg-gray-100'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            :class="$route.path.startsWith('/employee-clearance') ? 'text-white' : 'text-gray-700'"
            fill="currentColor"
            viewBox="0 0 24 24"
        >
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
        </svg>
        <span>Employee Clearance</span>
    </router-link>
</template>


<script setup>
    import { useAuthStore } from '@/stores/auth'
    const auth = useAuthStore()
    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }
</script>