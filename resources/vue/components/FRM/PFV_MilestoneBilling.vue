<template>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <div class="font-medium text-[#1F2937] mb-3">Milestone Billing Setup</div>
            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Milestone Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">% of Completion</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Billing Trigger</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in milestones"
                                :key="row.name"
                                class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ row.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ row.percentage }}%</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ row.trigger }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, reactive, computed, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    /* =====================================================
    Assets
    ===================================================== */
    import close from '@/assets/close.png'
    import open from '@/assets/open.png'

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    Helpers / Utilities
    ===================================================== */
    const showToast = ({
        icon = 'info',
        title = '',
        html = '',
        position = 'top-end',
        timer = 3000
    }) => {
        Swal.fire({
            toast: true,
            position,
            icon,
            title,
            html,
            showConfirmButton: false,
            timer,
            customClass: { popup: 'no-wrap-toast' }
        })
    }

    /* =====================================================
    Milestone Table Data
    ===================================================== */
    const milestones = ref([
        {
            name: 'Foundation',
            percentage: 25,
            trigger: 'Auto',
        },
        {
            name: 'Framing',
            percentage: 25,
            trigger: 'Auto',
        },
        {
            name: 'Completion',
            percentage: 25,
            trigger: 'Auto',
        },
    ])

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // initialization logic
    })
</script>
