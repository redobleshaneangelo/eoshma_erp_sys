<template>
    <div class="space-y-8">
        <!-- Identity & Registration Section -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Identity & Registration
            </h2>
            <div class="grid grid-cols-2 gap-6 bg-gray-50 rounded-lg p-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Legal Company Name</label>
                    <p class="mt-2 text-gray-900 font-semibold">{{ client.identity.legal_name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Trade Name</label>
                    <p class="mt-2 text-gray-900 font-semibold">{{ client.identity.trade_name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Client ID</label>
                    <p class="mt-2 text-gray-900 font-semibold font-mono">{{ client.identity.client_id }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Client Type</label>
                    <p class="mt-2 text-gray-900 font-semibold">{{ client.identity.type }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Status</label>
                    <p class="mt-2">
                        <span :class="statusStyles[client.identity.status]" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                            {{ client.identity.status }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Compliance Status</label>
                    <p class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ client.identity.compliance_status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Registration Details -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Registration Details</h3>
            <div class="space-y-4 bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Tax Identification Number (TIN)</label>
                        <p class="text-gray-900 font-mono">{{ client.registration.tin }}</p>
                        <span class="inline-flex items-center gap-1 text-xs text-green-700 font-semibold mt-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Verified
                        </span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Business Registration Number</label>
                        <p class="text-gray-900 font-mono">{{ client.registration.business_registration }}</p>
                        <span class="inline-flex items-center gap-1 text-xs text-green-700 font-semibold mt-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Verified
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Registered Address</label>
                    <p class="text-gray-900">{{ client.registration.address }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Jurisdiction</label>
                    <p class="text-gray-900">{{ client.registration.jurisdiction }}</p>
                </div>
            </div>
        </div>

        <!-- Business Profile -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Business Profile</h3>
            <div class="grid grid-cols-2 gap-6 bg-gray-50 rounded-lg p-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Industry</label>
                    <p class="mt-2 text-gray-900">{{ client.profile.industry }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Client Category</label>
                    <p class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            {{ client.profile.category }}
                        </span>
                    </p>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase">First Engagement Date</label>
                    <p class="mt-2 text-gray-900">{{ formatDate(client.profile.first_engagement) }}</p>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Internal Notes</label>
                    <p class="mt-2 text-gray-900 bg-white rounded p-3 border border-gray-200">{{ client.profile.internal_notes }}</p>
                </div>
            </div>
        </div>

        <!-- Status & Restrictions -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Status & Restrictions</h3>
            <div class="grid grid-cols-2 gap-6 bg-gray-50 rounded-lg p-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Current Status</label>
                    <p class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ client.restrictions.status }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Restriction Indicator</label>
                    <p class="mt-2 text-gray-900">{{ client.restrictions.reason || 'None' }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    client: {
        type: Object,
        required: true
    }
})

const statusStyles = {
    'Active': 'bg-green-100 text-green-800',
    'Prospect': 'bg-blue-100 text-blue-800',
    'On Hold': 'bg-yellow-100 text-yellow-800',
    'Archived': 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>
