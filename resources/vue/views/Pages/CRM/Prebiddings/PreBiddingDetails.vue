<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link
                    to="/prebiddings"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Pre-Biddings
                </router-link>

                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ prebidding.project_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ prebidding.project_code }} • Client: {{ prebidding.client_name }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="statusStyles[prebidding.status]" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold">
                            {{ formatStatus(prebidding.status) }}
                        </span>
                        <button
                            v-if="hasUnsavedChanges && activeTab === 'documents'"
                            @click="cancelDocumentChanges"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            v-if="hasUnsavedChanges && activeTab === 'documents'"
                            @click="saveDocumentChanges"
                            :disabled="savingChanges"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!savingChanges">Save Changes</span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                        <button
                            @click="showConstructionDrawer = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-[#0a7bc7] transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Sent to Operations
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
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

                <div class="p-8">
                    <!-- Overview Tab -->
                    <div v-if="activeTab === 'overview'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Project Name</label>
                                <input
                                    type="text"
                                    :value="prebidding.project_name"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Project Code</label>
                                <input
                                    type="text"
                                    :value="prebidding.project_code"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Project Type</label>
                                <input
                                    type="text"
                                    :value="prebidding.project_type"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Client</label>
                                <input
                                    type="text"
                                    :value="prebidding.client_name"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Site Address</label>
                                <textarea
                                    :value="prebidding.address"
                                    rows="3"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm resize-none"
                                ></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Project Notes</label>
                                <textarea
                                    :value="prebidding.notes"
                                    rows="3"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm resize-none"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bid Details Tab -->
                    <div v-if="activeTab === 'bid_details'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Submission Deadline</label>
                                <input
                                    type="text"
                                    :value="formatDate(prebidding.bid_details.deadline)"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Opening Date</label>
                                <input
                                    type="text"
                                    :value="formatDate(prebidding.bid_details.opening)"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Type</label>
                                <input
                                    type="text"
                                    :value="prebidding.bid_details.type"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bond Type</label>
                                <input
                                    type="text"
                                    :value="prebidding.bid_details.bondType"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Security Amount</label>
                                <input
                                    type="text"
                                    :value="formatCurrency(prebidding.bid_details.amount)"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Bidding Activities</h3>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Activity Name</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(activity, index) in prebidding.bidding_activities" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ activity.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(activity.date) }}</td>
                                        </tr>
                                        <tr v-if="!prebidding.bidding_activities.length">
                                            <td colspan="2" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No bidding activities recorded.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'" class="space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Document Checklist</h3>
                                <button
                                    @click="showDocumentModal = true"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-[#0a7bc7] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Document
                                </button>
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Document Name</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Status</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(doc, index) in documentChecklist" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ doc.name }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold rounded-full"
                                                    :class="doc.completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                                >
                                                    {{ doc.completed ? 'Completed' : 'Pending' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <button
                                                    @click="removeDocumentChecklist(index)"
                                                    class="text-red-600 hover:text-red-800 font-medium text-sm"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!documentChecklist.length">
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No documents in checklist.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Upload Documents</h3>
                            <div class="max-w-lg mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Choose Document to Upload</label>
                                <select
                                    v-model="selectedDocument"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select document</option>
                                    <option
                                        v-for="doc in documentChecklist.filter(d => !d.completed)"
                                        :key="doc.name"
                                        :value="doc.name"
                                    >
                                        {{ doc.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    accept=".pdf"
                                    class="hidden"
                                    id="fileUploadView"
                                    :disabled="!selectedDocument"
                                />
                                <label
                                    for="fileUploadView"
                                    @drop.prevent="handleFileDrop"
                                    @dragover.prevent
                                    class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 p-12 flex flex-col items-center justify-center transition-colors hover:border-blue-500 hover:bg-blue-50 cursor-pointer"
                                    :class="!selectedDocument ? 'opacity-60 cursor-not-allowed pointer-events-none' : ''"
                                >
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-700 font-medium mb-1">Click to upload or drag and drop</p>
                                    <p class="text-gray-500 text-sm">PDF files only</p>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Uploaded Documents</h3>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Document Name</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Upload Date</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(doc, index) in documentUploaded" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ doc.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ doc.uploadedAt }}</td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <button
                                                        @click="viewDocument(doc)"
                                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                                    >
                                                        View
                                                    </button>
                                                    <span class="text-gray-300">|</span>
                                                    <button
                                                        @click="removeUploadedDocument(index)"
                                                        class="text-red-600 hover:text-red-800 font-medium"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!documentUploaded.length">
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No documents uploaded yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Team & Resources Tab -->
                    <div v-if="activeTab === 'team_resources'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Supervisor</label>
                                <input
                                    type="text"
                                    :value="prebidding.team.supervisor"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foreman</label>
                                <input
                                    type="text"
                                    :value="prebidding.team.foreman"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Materials Tab -->
                    <div v-if="activeTab === 'materials'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Materials Planning</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Material</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Brand</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Source</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(material, index) in prebidding.materials" :key="index" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ material.name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ material.brand || 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ material.quantity }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ material.unit }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ material.source }}</td>
                                    </tr>
                                    <tr v-if="!prebidding.materials.length">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            No materials recorded.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Equipment Tab -->
                    <div v-if="activeTab === 'equipment'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Equipment Planning</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Equipment</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Brand</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Condition</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(equipment, index) in prebidding.equipment" :key="index" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ equipment.name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.brand || 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.quantity }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.type }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.condition || 'N/A' }}</td>
                                    </tr>
                                    <tr v-if="!prebidding.equipment.length">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            No equipment recorded.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Subcontractors Tab -->
                    <div v-if="activeTab === 'subcontractors'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Subcontractor Planning</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Subcontractor</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Scope</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(sub, index) in prebidding.subcontractors" :key="index" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ sub.name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ sub.scope }}</td>
                                    </tr>
                                    <tr v-if="!prebidding.subcontractors.length">
                                        <td colspan="2" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            No subcontractors recorded.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Financial Review Tab -->
                    <div v-if="activeTab === 'financial'" class="space-y-6">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="text-gray-600 text-xs font-semibold uppercase">Total Budget</div>
                                <div class="text-lg font-bold text-gray-900 mt-2">₱ {{ formatCurrency(prebidding.financial.total_budget) }}</div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="text-gray-600 text-xs font-semibold uppercase">Contract Amount</div>
                                <div class="text-lg font-bold text-gray-900 mt-2">₱ {{ formatCurrency(prebidding.financial.contract_amount) }}</div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="text-gray-600 text-xs font-semibold uppercase">Billing Type</div>
                                <div class="text-lg font-bold text-gray-900 mt-2">{{ prebidding.financial.billing_type }}</div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="text-gray-600 text-xs font-semibold uppercase">Retention (%)</div>
                                <div class="text-lg font-bold text-gray-900 mt-2">{{ prebidding.financial.retention }}</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold text-gray-800 text-base">Budget Utilization</h4>
                                <span class="text-sm font-bold text-blue-600">
                                    {{ budgetUtilizationPercentage.toFixed(1) }}%
                                </span>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>₱ {{ prebidding.financial.total_budget_utilized.toLocaleString() }}</span>
                                    <span>₱ {{ prebidding.financial.total_budget_set.toLocaleString() }}</span>
                                </div>
                                <div class="relative w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-linear-to-r from-blue-500 to-blue-600"
                                        :style="{ width: `${Math.min(budgetUtilizationPercentage, 100)}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scope Changes Tab -->
                    <div v-if="activeTab === 'scope_changes'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Scope Changes</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Change ID</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Impact</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(change, index) in scopeChanges" :key="index" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ change.code }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(change.date) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ change.description }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">₱ {{ formatCurrency(change.impact_cost) }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="scopeStatusStyles[change.status]">
                                                {{ change.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!scopeChanges.length">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            No scope changes recorded.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Document Modal -->
            <div v-if="showDocumentModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="background-color: rgba(0, 0, 0, 0.5);">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Add Document to Checklist</h3>
                        <button @click="closeDocumentModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Document Name *</label>
                                <input
                                    v-model="newDocumentForm.name"
                                    type="text"
                                    placeholder="e.g., Safety Plan, Insurance Certificate"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex gap-3">
                        <button
                            @click="closeDocumentModal"
                            class="flex-1 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="addDocumentToChecklist"
                            :disabled="!newDocumentForm.name.trim()"
                            class="flex-1 px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-[#0a7bc7] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Add Document
                        </button>
                    </div>
                </div>
            </div>

            <!-- Set as Construction Project Drawer -->
            <div v-if="showConstructionDrawer" class="fixed inset-0 z-50 overflow-hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 transition-opacity" @click="showConstructionDrawer = false"></div>
                
                <!-- Drawer -->
                <div class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-xl flex flex-col">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Set as Construction Project</h3>
                            <button @click="showConstructionDrawer = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="space-y-6">
                            <!-- Project Information -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm font-semibold text-gray-700 mb-1">Project</p>
                                <p class="text-base font-bold text-gray-900">{{ prebidding.project_name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ prebidding.project_code }}</p>
                            </div>

                            <!-- Notice of Award Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Notice of Award
                                    <span class="text-red-500">*</span>
                                </label>
                                
                                <!-- File Upload Area -->
                                <div
                                    @drop.prevent="handleNoticeOfAwardDrop"
                                    @dragover.prevent
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0c8ce9] transition-colors cursor-pointer"
                                    @click="$refs.noticeFileInput.click()"
                                >
                                    <div v-if="!noticeOfAwardFile">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">
                                            Click to upload or drag and drop
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">PDF files only</p>
                                    </div>
                                    <div v-else class="flex items-center justify-center gap-3">
                                        <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-left">
                                            <p class="text-sm font-semibold text-gray-900">{{ noticeOfAwardFile.name }}</p>
                                            <p class="text-xs text-gray-500">{{ (noticeOfAwardFile.size / 1024).toFixed(2) }} KB</p>
                                        </div>
                                        <button
                                            @click.stop="noticeOfAwardFile = null"
                                            class="ml-auto text-red-500 hover:text-red-700"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <input
                                    ref="noticeFileInput"
                                    type="file"
                                    accept=".pdf,application/pdf"
                                    class="hidden"
                                    @change="handleNoticeOfAwardUpload"
                                />
                            </div>

                            <!-- Info Message -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-yellow-800 mb-1">Important</p>
                                        <p class="text-xs text-yellow-700">
                                            By sending this to Operations, you are confirming that this pre-bidding project has been awarded and should be converted to an active construction project.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex gap-3">
                            <button
                                @click="showConstructionDrawer = false"
                                class="flex-1 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="sendToOperations"
                                :disabled="!noticeOfAwardFile || sendingToOperations"
                                class="flex-1 px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-[#0a7bc7] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="!sendingToOperations">Send to Operations</span>
                                <span v-else class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div class="fixed bottom-6 right-6 z-50" v-if="loading">
                <div class="bg-white rounded-full shadow-lg p-3">
                    <svg class="animate-spin h-6 w-6 text-[#0c8ce9]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const activeTab = ref('overview')
const loading = ref(false)
const selectedDocument = ref('')
const showConstructionDrawer = ref(false)
const showDocumentModal = ref(false)
const noticeOfAwardFile = ref(null)
const sendingToOperations = ref(false)
const savingChanges = ref(false)

const newDocumentForm = ref({
    name: ''
})

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'bid_details', label: 'Bid Details' },
    { key: 'documents', label: 'Documents' },
    { key: 'team_resources', label: 'Team & Resources' },
    { key: 'materials', label: 'Materials' },
    { key: 'equipment', label: 'Equipment' },
    { key: 'subcontractors', label: 'Subcontractors' },
    { key: 'financial', label: 'Financial Review' },
    { key: 'scope_changes', label: 'Scope Changes' }
]

const statusStyles = {
    active: 'bg-green-100 text-green-800 border border-green-300',
    draft: 'bg-gray-100 text-gray-800 border border-gray-300',
    pending: 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    rejected: 'bg-red-100 text-red-800 border border-red-300',
    bidding_won: 'bg-blue-100 text-blue-800 border border-blue-300'
}

const scopeStatusStyles = {
    Approved: 'bg-green-100 text-green-800',
    Pending: 'bg-yellow-100 text-yellow-800',
    Rejected: 'bg-red-100 text-red-800'
}

const prebidding = ref({})

const documentChecklist = ref([
    { name: 'Company Profile', completed: true },
    { name: 'Tax Clearance', completed: true },
    { name: 'Bid Bond', completed: false },
    { name: 'Technical Proposal', completed: false }
])

const originalDocumentChecklist = ref([
    { name: 'Company Profile', completed: true },
    { name: 'Tax Clearance', completed: true },
    { name: 'Bid Bond', completed: false },
    { name: 'Technical Proposal', completed: false }
])

const documentUploaded = ref([
    { name: 'Company Profile', uploadedAt: 'Feb 01, 2026', fileUrl: '', filePath: null },
    { name: 'Tax Clearance', uploadedAt: 'Feb 02, 2026', fileUrl: '', filePath: null }
])

const originalDocumentUploaded = ref([
    { name: 'Company Profile', uploadedAt: 'Feb 01, 2026', fileUrl: '', filePath: null },
    { name: 'Tax Clearance', uploadedAt: 'Feb 02, 2026', fileUrl: '', filePath: null }
])

const scopeChanges = ref([
    {
        code: 'SC-001',
        date: '2026-02-01',
        description: 'Additional electrical outlets per floor',
        impact_cost: 50000,
        status: 'Approved'
    },
    {
        code: 'SC-002',
        date: '2026-02-03',
        description: 'Change of flooring material to premium tiles',
        impact_cost: 120000,
        status: 'Pending'
    }
])

const budgetUtilizationPercentage = computed(() => {
    if (!prebidding.value.financial.total_budget_set) return 0
    return (prebidding.value.financial.total_budget_utilized / prebidding.value.financial.total_budget_set) * 100
})

const hasUnsavedChanges = computed(() => {
    // Deep comparison of both arrays
    const checklistChanged = JSON.stringify(documentChecklist.value) !== JSON.stringify(originalDocumentChecklist.value)
    const uploadedChanged = JSON.stringify(documentUploaded.value) !== JSON.stringify(originalDocumentUploaded.value)
    return checklistChanged || uploadedChanged
})

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatStatus = (status) => {
    const map = {
        active: 'Active',
        draft: 'Draft',
        pending: 'Pending',
        rejected: 'Rejected',
        bidding_won: 'Bidding Won'
    }
    return map[status] || status
}

const showToast = ({ icon, title }) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    })
}

const closeDocumentModal = () => {
    showDocumentModal.value = false
    newDocumentForm.value.name = ''
}

const addDocumentToChecklist = () => {
    if (!newDocumentForm.value.name.trim()) return
    
    // Check if document already exists
    if (documentChecklist.value.some(doc => doc.name === newDocumentForm.value.name)) {
        Swal.fire({
            icon: 'warning',
            title: 'Duplicate',
            text: 'This document already exists in the checklist.',
            confirmButtonColor: '#0c8ce9'
        })
        return
    }

    documentChecklist.value.push({
        name: newDocumentForm.value.name,
        completed: false
    })

    closeDocumentModal()
}

const syncChecklistWithUploads = () => {
    const uploadedNames = new Set(documentUploaded.value.map(doc => doc.name))

    documentChecklist.value = documentChecklist.value.map(doc => ({
        ...doc,
        completed: uploadedNames.has(doc.name) ? true : doc.completed
    }))

    uploadedNames.forEach((name) => {
        if (!documentChecklist.value.find(doc => doc.name === name)) {
            documentChecklist.value.push({ name, completed: true })
        }
    })
}

const removeDocumentChecklist = (index) => {
    const docName = documentChecklist.value[index]?.name
    Swal.fire({
        title: 'Remove Document?',
        text: `Are you sure you want to remove "${docName}" from the checklist?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {
        if (result.isConfirmed) {
            documentChecklist.value.splice(index, 1)
            if (docName) {
                documentUploaded.value = documentUploaded.value.filter(doc => doc.name !== docName)
            }
            showToast({ icon: 'success', title: 'Checklist document removed.' })
        }
    })
}

const removeUploadedDocument = (index) => {
    const docName = documentUploaded.value[index]?.name
    Swal.fire({
        title: 'Remove Document?',
        text: `Are you sure you want to remove "${docName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {
        if (result.isConfirmed) {
            documentUploaded.value.splice(index, 1)
            if (docName) {
                const checklistDoc = documentChecklist.value.find(doc => doc.name === docName)
                if (checklistDoc) {
                    checklistDoc.completed = false
                }
            }
            showToast({ icon: 'success', title: 'Uploaded document removed.' })
        }
    })
}

const cancelDocumentChanges = () => {
    Swal.fire({
        title: 'Revert Changes?',
        text: 'All unsaved document changes will be discarded.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, revert',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            documentChecklist.value = JSON.parse(JSON.stringify(originalDocumentChecklist.value))
            documentUploaded.value = JSON.parse(JSON.stringify(originalDocumentUploaded.value))
            syncChecklistWithUploads()
            showToast({ icon: 'info', title: 'Changes reverted.' })
        }
    })
}

const handleFileUpload = (event) => {
    if (!selectedDocument.value) {
        showToast({ icon: 'warning', title: 'Select a document first.' })
        return
    }
    const file = event.target.files[0]
    if (!file) return

    if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
        showToast({ icon: 'error', title: 'Only PDF files are allowed.' })
        event.target.value = ''
        return
    }

    const documentIndex = documentChecklist.value.findIndex(doc => doc.name === selectedDocument.value)
    if (documentIndex !== -1) {
        documentChecklist.value[documentIndex].completed = true
    } else {
        documentChecklist.value.push({ name: selectedDocument.value, completed: true })
    }

    documentUploaded.value.push({
        name: selectedDocument.value,
        uploadedAt: new Date().toLocaleDateString('en-PH'),
        fileUrl: URL.createObjectURL(file),
        filePath: null,
        file
    })

    syncChecklistWithUploads()

    showToast({ icon: 'success', title: 'Document uploaded.' })

    selectedDocument.value = ''
    event.target.value = ''
}

const handleFileDrop = (event) => {
    if (!selectedDocument.value) {
        showToast({ icon: 'warning', title: 'Select a document first.' })
        return
    }
    const file = event.dataTransfer.files[0]
    if (!file) return

    if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
        showToast({ icon: 'error', title: 'Only PDF files are allowed.' })
        return
    }

    const documentIndex = documentChecklist.value.findIndex(doc => doc.name === selectedDocument.value)
    if (documentIndex !== -1) {
        documentChecklist.value[documentIndex].completed = true
    } else {
        documentChecklist.value.push({ name: selectedDocument.value, completed: true })
    }

    documentUploaded.value.push({
        name: selectedDocument.value,
        uploadedAt: new Date().toLocaleDateString('en-PH'),
        fileUrl: URL.createObjectURL(file),
        filePath: null,
        file
    })

    syncChecklistWithUploads()

    showToast({ icon: 'success', title: 'Document uploaded.' })

    selectedDocument.value = ''
}

const viewDocument = (doc) => {
    if (doc.fileUrl) {
        window.open(doc.fileUrl, '_blank')
    }
}

const saveDocumentChanges = async () => {
    try {
        savingChanges.value = true

        const payload = {
            checklist: documentChecklist.value,
            uploaded: documentUploaded.value.map(doc => ({
                name: doc.name,
                uploadedAt: doc.uploadedAt,
                filePath: doc.filePath
            }))
        }

        const formData = new FormData()
        formData.append('checklist', JSON.stringify(payload.checklist))
        formData.append('uploaded', JSON.stringify(payload.uploaded))
        formData.append('_method', 'PUT')

        documentUploaded.value.forEach((doc, index) => {
            if (doc.file) {
                formData.append(`documents[${index}]`, doc.file)
            }
        })

        // Call API to update documents
        const id = route.params.id
        const response = await axios.post(`/api/prebiddings/${id}/documents`, formData, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
                'Content-Type': 'multipart/form-data'
            }
        })

        // Update original state to current state
        originalDocumentChecklist.value = JSON.parse(JSON.stringify(documentChecklist.value))
        originalDocumentUploaded.value = JSON.parse(JSON.stringify(documentUploaded.value))

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Document changes have been saved successfully.',
            confirmButtonColor: '#0c8ce9'
        })

    } catch (error) {
        console.error('Failed to save document changes:', error)
        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: error.response?.data?.message || 'Failed to save changes. Please try again.',
            confirmButtonColor: '#0c8ce9'
        })
    } finally {
        savingChanges.value = false
    }
}

const handleNoticeOfAwardUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return

    if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'Please upload a PDF file only.',
            confirmButtonColor: '#0c8ce9'
        })
        event.target.value = ''
        return
    }

    noticeOfAwardFile.value = file
    event.target.value = ''
}

const handleNoticeOfAwardDrop = (event) => {
    const file = event.dataTransfer.files[0]
    if (!file) return

    if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'Please upload a PDF file only.',
            confirmButtonColor: '#0c8ce9'
        })
        return
    }

    noticeOfAwardFile.value = file
}

const sendToOperations = async () => {
    if (!noticeOfAwardFile.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Document',
            text: 'Please upload the Notice of Award document.',
            confirmButtonColor: '#0c8ce9'
        })
        return
    }

    try {
        sendingToOperations.value = true

        const formData = new FormData()
        formData.append('notice_of_award', noticeOfAwardFile.value)

        // Call API to send to operations
        const id = route.params.id
        const response = await axios.post(`/api/prebiddings/${id}/send-to-operations`, formData, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
                'Content-Type': 'multipart/form-data'
            }
        })

        showConstructionDrawer.value = false
        noticeOfAwardFile.value = null

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Pre-bidding project has been sent to Operations for construction project conversion.',
            confirmButtonColor: '#0c8ce9'
        }).then(() => {
            // Refresh prebidding data to show updated status
            fetchPrebidding()
            router.push('/prebiddings') // Redirect back to prebiddings list after sending to operations
        })

    } catch (error) {
        console.error('Failed to send to operations:', error)
        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: error.response?.data?.message || 'Failed to send to Operations. Please try again.',
            confirmButtonColor: '#0c8ce9'
        })
    } finally {
        sendingToOperations.value = false
    }
}

const fetchPrebidding = async () => {
    try {
        loading.value = true
        const id = route.params.id
        const response = await axios.get(`/api/prebiddings/${id}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        
        prebidding.value = response.data.prebidding
        documentChecklist.value = response.data.documents?.checklist || []
        documentUploaded.value = response.data.documents?.uploaded || []
        syncChecklistWithUploads()
        originalDocumentChecklist.value = JSON.parse(JSON.stringify(documentChecklist.value))
        originalDocumentUploaded.value = JSON.parse(JSON.stringify(documentUploaded.value))
        scopeChanges.value = response.data.scope_changes || []
    } catch (error) {
        console.error('Failed to fetch pre-bidding:', error)
        Swal.fire({
            icon: 'error',
            title: 'Failed to Load',
            text: 'Could not load prebidding details. Please try again.',
            confirmButtonColor: '#0c8ce9'
        })
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchPrebidding()

    auth.pageTitle = 'Prebiddings'
})
</script>
