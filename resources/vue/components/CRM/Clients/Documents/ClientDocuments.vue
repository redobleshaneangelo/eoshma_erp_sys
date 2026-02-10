<template>
    <div class="space-y-6">
        <!-- Upload Document Button -->
        <div class="flex justify-end">
            <button 
                @click="showUploadModal = true"
                class="flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Document
            </button>
        </div>

        <!-- Documents Grid -->
        <div v-if="documents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="doc in documents" :key="doc.id" class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-all">
                <!-- Document Icon -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ doc.name }}</p>
                            <p class="text-xs text-gray-500">{{ doc.type }}</p>
                        </div>
                    </div>
                    <span :class="statusStyles[doc.status]" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold">
                        {{ doc.status }}
                    </span>
                </div>

                <!-- Document Details -->
                <div class="space-y-2 mb-4 text-sm text-gray-600 border-t border-gray-200 pt-4">
                    <div class="flex justify-between">
                        <span>Upload Date:</span>
                        <span class="font-semibold">{{ doc.upload_date }}</span>
                    </div>
                    <div v-if="doc.expiry_date" class="flex justify-between">
                        <span>Expiry Date:</span>
                        <span class="font-semibold" :class="isExpiringSoon(doc.expiry_date) ? 'text-red-600' : 'text-gray-900'">
                            {{ doc.expiry_date }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button @click="viewDocument(doc.id)" class="flex-1 px-3 py-2 text-sm font-semibold text-[#0c8ce9] hover:bg-blue-50 rounded-lg transition-all border border-[#0c8ce9]">
                        View
                    </button>
                    <button @click="downloadDocument(doc.id)" class="flex-1 px-3 py-2 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-all">
                        Download
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-600">No documents uploaded yet. Click "Upload Document" to get started.</p>
        </div>

        <!-- Document Types Reference -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-8">
            <p class="text-sm font-semibold text-blue-900 mb-2">Supported Document Types:</p>
            <div class="grid grid-cols-2 gap-2 text-sm text-blue-800">
                <div>• Business Permit</div>
                <div>• Tax Certificate</div>
                <div>• Framework Agreement</div>
                <div>• NDA</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    clientId: {
        type: [String, Number],
        required: true
    }
})

const showUploadModal = ref(false)
const documents = ref([
    {
        id: 1,
        name: 'Business Permit 2026',
        type: 'Business Permit',
        status: 'Valid',
        upload_date: 'Jan 15, 2026',
        expiry_date: 'Dec 31, 2026'
    },
    {
        id: 2,
        name: 'Tax Certificate - BIR',
        type: 'Tax Certificate',
        status: 'Valid',
        upload_date: 'Jan 10, 2026',
        expiry_date: 'Jan 10, 2027'
    },
    {
        id: 3,
        name: 'Framework Agreement',
        type: 'Framework Agreement',
        status: 'Valid',
        upload_date: 'Dec 01, 2023',
        expiry_date: null
    }
])

const statusStyles = {
    'Valid': 'bg-green-100 text-green-800',
    'Pending': 'bg-yellow-100 text-yellow-800',
    'Expiring': 'bg-orange-100 text-orange-800',
    'Expired': 'bg-red-100 text-red-800'
}

const isExpiringSoon = (expiryDate) => {
    if (!expiryDate) return false
    const days = Math.floor((new Date(expiryDate) - new Date()) / (1000 * 60 * 60 * 24))
    return days <= 30 && days > 0
}

const viewDocument = (docId) => {
    alert(`Opening document ${docId}`)
}

const downloadDocument = (docId) => {
    alert(`Downloading document ${docId}`)
}
</script>
