<template>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <div class="font-medium text-[#1F2937] mb-3">Document List</div>
            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Document Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Uploaded By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in documents"
                                :key="row.name"
                                class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">{{ row.date }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ row.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ row.uploader }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <button 
                                        class="bg-[#0C8CE9] text-white px-4 py-2 rounded text-sm shadow-sm hover:bg-blue-700 transition-colors inline-block"
                                    >
                                        View
                                    </button>
                                </td>
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
    Documents Table Data
    ===================================================== */
    const documents = ref([
        {
            date: 'January 6, 2024',
            name: 'Document 1',
            uploader: 'Tokai Teio',
        },
        {
            date: 'January 6, 2024',
            name: 'Document 2',
            uploader: 'Tokai Teio',
        },
        {
            date: 'January 6, 2024',
            name: 'Document 3',
            uploader: 'Tokai Teio',
        },
    ])

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // initialization logic
    })
</script>
