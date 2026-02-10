<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">
                <!-- Page Header -->
                <div class="mb-8">
                    <router-link to="/project-finance" class="text-[#0c8ce9] text-sm font-semibold hover:underline mb-4 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Projects
                    </router-link>
                    <h1 class="text-3xl font-bold text-gray-900">{{ isEditing ? 'Edit' : 'Create New' }} Project Finance</h1>
                </div>

                <div class="grid grid-cols-3 gap-8">
                    <!-- Form -->
                    <div class="col-span-2">
                        <form @submit.prevent="submitForm" class="space-y-6">
                            <!-- Basic Information Section -->
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                                </div>
                                <div class="px-6 py-6 space-y-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                                            <input 
                                                v-model="form.name"
                                                type="text"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                                placeholder="Enter project name"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Code *</label>
                                            <input 
                                                v-model="form.code"
                                                type="text"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                                placeholder="e.g., MTD-001"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                                        <select 
                                            v-model="form.clientId"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                        >
                                            <option value="">Select a client</option>
                                            <option value="1">Urban Development Corp</option>
                                            <option value="2">Retail Group Ltd</option>
                                            <option value="3">Healthcare Services</option>
                                            <option value="4">Corporate Solutions</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Project Address</label>
                                        <input 
                                            v-model="form.address"
                                            type="text"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            placeholder="Enter project address"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Project Notes</label>
                                        <textarea 
                                            v-model="form.notes"
                                            rows="3"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            placeholder="Additional project notes"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Bidding Information Section -->
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Bidding Information</h3>
                                </div>
                                <div class="px-6 py-6 space-y-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Bid Type *</label>
                                            <select 
                                                v-model="form.bidType"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            >
                                                <option value="">Select bid type</option>
                                                <option value="open">Open Bidding</option>
                                                <option value="selective">Selective Bidding</option>
                                                <option value="negotiated">Negotiated</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Bond Type *</label>
                                            <select 
                                                v-model="form.bondType"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            >
                                                <option value="">Select bond type</option>
                                                <option value="performance">Performance Bond</option>
                                                <option value="bid">Bid Bond</option>
                                                <option value="payment">Payment Bond</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Bid Deadline</label>
                                            <input 
                                                v-model="form.bidDeadline"
                                                type="date"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Bid Opening Date</label>
                                            <input 
                                                v-model="form.bidOpening"
                                                type="date"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Security Amount *</label>
                                        <input 
                                            v-model="form.securityAmount"
                                            type="number"
                                            step="0.01"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information Section -->
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Financial Information</h3>
                                </div>
                                <div class="px-6 py-6 space-y-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Budget *</label>
                                            <input 
                                                v-model="form.totalBudget"
                                                type="number"
                                                step="0.01"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Contract Amount *</label>
                                            <input 
                                                v-model="form.contractAmount"
                                                type="number"
                                                step="0.01"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                                placeholder="0.00"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Billing Type *</label>
                                            <select 
                                                v-model="form.billingType"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                            >
                                                <option value="">Select billing type</option>
                                                <option value="monthly">Monthly Progress Billing</option>
                                                <option value="milestone">Milestone-based</option>
                                                <option value="fixed">Fixed Price</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Retention Percentage *</label>
                                            <input 
                                                v-model="form.retention"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                                placeholder="5"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                        <select 
                                            v-model="form.status"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                        >
                                            <option value="">Select status</option>
                                            <option value="planning">Planning</option>
                                            <option value="active">Active</option>
                                            <option value="on-hold">On Hold</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-4">
                                <button
                                    type="submit"
                                    class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors"
                                >
                                    {{ isEditing ? 'Update Project' : 'Create Project' }}
                                </button>
                                <router-link
                                    to="/project-finance"
                                    class="px-6 py-2.5 bg-white text-gray-700 rounded-lg font-semibold text-sm border border-gray-300 hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </router-link>
                            </div>
                        </form>
                    </div>

                    <!-- Sidebar - Help & Tips -->
                    <div class="col-span-1">
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Form Guidance</h3>
                            <div class="space-y-4 text-sm text-gray-600">
                                <div>
                                    <p class="font-medium text-gray-900 mb-1">Project Code</p>
                                    <p>Use a unique identifier (e.g., MTD-001, SCE-002)</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 mb-1">Contract Amount</p>
                                    <p>The agreed contract value with the client</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 mb-1">Retention</p>
                                    <p>Standard percentage held until project completion (typically 5-10%)</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 mb-1">Security Amount</p>
                                    <p>Performance bond or bid security amount</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isEditing = ref(!!route.params.id)

const form = ref({
    name: '',
    code: '',
    clientId: '',
    address: '',
    notes: '',
    bidType: '',
    bondType: '',
    bidDeadline: '',
    bidOpening: '',
    securityAmount: 0,
    totalBudget: 0,
    contractAmount: 0,
    billingType: '',
    retention: 5,
    status: 'planning'
})

const submitForm = async () => {
    try {
        // Submit form to backend
        console.log('Form submitted:', form.value)
    } catch (error) {
        console.error('Error submitting form:', error)
    }
}
</script>

<style scoped></style>
