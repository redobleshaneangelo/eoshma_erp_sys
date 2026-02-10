<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link 
                    to="/clients"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Clients
                </router-link>

                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ client.legal_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ client.trade_name }} • {{ client.type }} • Client ID: {{ client.id }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="clientStatusStyles[client.status]" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold">
                            {{ client.status }}
                        </span>
                        <button @click="editClient" class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            Edit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Metrics Row -->
            <div class="grid grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Active Projects</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2">{{ client.metrics.active_projects }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Active Contracts</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2">{{ client.metrics.active_contracts }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Open Bids</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2">{{ client.metrics.open_bids }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Total Contract Value</div>
                    <div class="text-lg font-bold text-gray-900 mt-2">₱{{ formatCurrency(client.metrics.total_contract_value) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Outstanding Balance</div>
                    <div class="text-lg font-bold text-red-600 mt-2">₱{{ formatCurrency(client.metrics.outstanding_balance) }}</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex gap-0 px-6">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-4 px-4 border-b-2 font-semibold text-sm transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                : 'border-transparent text-gray-600 hover:text-gray-900'"
                        >
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-8">
                    <!-- Overview Tab -->
                    <ClientOverview v-if="activeTab === 'overview'" :client="client" />

                    <!-- Contacts Tab -->
                    <ClientContacts v-if="activeTab === 'contacts'" :clientId="clientId" />

                    <!-- Financial Tab -->
                    <ClientFinancial v-if="activeTab === 'financial'" :client="client" />

                    <!-- Documents Tab -->
                    <ClientDocuments v-if="activeTab === 'documents'" :clientId="clientId" />

                    <!-- History Tab -->
                    <ClientHistory v-if="activeTab === 'history'" :clientId="clientId" />

                    <!-- Associated Projects Tab -->
                    <AssociatedProjects v-if="activeTab === 'associated_projects'" :clientId="clientId" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import ClientOverview from '@/components/CRM/Clients/Overview/ClientOverview.vue'
import ClientContacts from '@/components/CRM/Clients/Contacts/ClientContacts.vue'
import ClientFinancial from '@/components/CRM/Clients/Financial/ClientFinancial.vue'
import ClientDocuments from '@/components/CRM/Clients/Documents/ClientDocuments.vue'
import ClientHistory from '@/components/CRM/Clients/History/ClientHistory.vue'
import AssociatedProjects from '@/components/CRM/AssociatedProjects.vue'

const route = useRoute()
const clientId = ref(route.params.id)
const activeTab = ref('overview')

// Mock data
const client = ref({
    id: 1,
    legal_name: 'Acme Corporation',
    trade_name: 'Acme Corp',
    type: 'Corporate',
    status: 'Active',
    crm_owner: 'John Doe',
    metrics: {
        active_projects: 5,
        active_contracts: 5,
        open_bids: 2,
        total_contract_value: 5000000,
        outstanding_balance: 250000,
        avg_payment_delay: 5
    },
    identity: {
        legal_name: 'Acme Corporation',
        trade_name: 'Acme Corp',
        client_id: 'CLIENT-001',
        type: 'Corporate',
        status: 'Active',
        compliance_status: 'Complete'
    },
    registration: {
        tin: '000-000-000-000',
        business_registration: 'BR-2020-001234',
        address: '123 Business St, Metro Manila',
        jurisdiction: 'Philippines',
        verification: {
            tin: 'Verified',
            business_registration: 'Verified'
        }
    },
    profile: {
        industry: 'Construction & Engineering',
        category: 'Repeat',
        first_engagement: '2020-01-15',
        internal_notes: 'Key client, proactive in project communication'
    },
    restrictions: {
        status: 'None',
        reason: '',
        effective_date: '',
        applied_by: ''
    }
})

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'contacts', label: 'Contacts' },
    { key: 'financial', label: 'Financial Snapshot' },
    { key: 'documents', label: 'Documents' },
    { key: 'history', label: 'History & Audit' },
    { key: 'associated_projects', label: 'Associated Projects' }
]

const clientStatusStyles = {
    'Active': 'bg-green-100 text-green-800',
    'Prospect': 'bg-blue-100 text-blue-800',
    'On Hold': 'bg-yellow-100 text-yellow-800',
    'Archived': 'bg-gray-100 text-gray-800'
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}

const editClient = () => {
    alert('Edit client functionality')
}
</script>
