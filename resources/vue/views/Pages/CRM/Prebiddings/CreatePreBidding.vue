<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="flex flex-col h-full">
                <router-link
                    to="/prebiddings"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Pre-Biddings
                </router-link>

                <!-- Modern Stepper -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="relative">
                        <!-- Progress Bar -->
                        <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 rounded-full" style="z-index: 0;">
                            <div 
                                class="h-full bg-linear-to-r from-[#0C8CE9] to-[#0EA5E9] rounded-full transition-all duration-500 ease-out"
                                :style="{ width: `${(activeStep / (steps.length - 1)) * 100}%` }"
                            ></div>
                        </div>
                        
                        <!-- Steps -->
                        <div class="relative flex justify-between" style="z-index: 1;">
                            <div 
                                v-for="(step, index) in steps" 
                                :key="index"
                                class="flex flex-col items-center cursor-pointer group"
                                @click="goToStep(index)"
                                :style="{ width: `${100 / steps.length}%` }"
                            >
                                <!-- Step Circle -->
                                <div 
                                    class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 mb-3"
                                    :class="getStepCircleClass(index)"
                                >
                                    <!-- Checkmark for completed -->
                                    <svg v-if="index < activeStep" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <!-- Icon for current/upcoming -->
                                    <component v-else :is="step.icon" class="w-5 h-5" />
                                </div>
                                
                                <!-- Step Label -->
                                <div class="text-center">
                                    <p 
                                        class="text-xs font-medium transition-colors duration-300"
                                        :class="index === activeStep ? 'text-[#0C8CE9] font-semibold' : index < activeStep ? 'text-gray-700' : 'text-gray-400'"
                                    >
                                        {{ step.label }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mb-6">
                    <button 
                        v-if="activeStep > 0"
                        @click="activeStep--"
                        class="flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </button>
                    <div v-else></div>
                    
                    <div class="flex items-center gap-3">
                        <button 
                            v-if="activeStep < steps.length - 1"
                            @click="nextStep"
                            class="flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-linear-to-r from-[#0C8CE9] to-[#0EA5E9] rounded-lg hover:from-[#0B7DC9] hover:to-[#0D94D9] transition-all duration-200 shadow-sm"
                        >
                            Continue
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button
                            v-else
                            @click="handleSubmit"
                            :disabled="loading"
                            class="flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-linear-to-r from-green-500 to-green-600 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Pre-Bidding
                        </button>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="bg-white rounded-xl shadow-sm p-8 min-h-150">
                    <!-- Budget Utilization (Visible after budget is set) -->
                    <div v-if="totalBudgetSet > 0" class="bg-gray-50 rounded-lg p-6 mb-6 border border-gray-200 sticky top-0">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-semibold text-gray-800 text-base">Budget Utilization</h4>
                            <span class="text-sm font-bold" :class="budgetUtilizationPercentage > 100 ? 'text-red-600' : 'text-blue-600'">
                                {{ budgetUtilizationPercentage.toFixed(1) }}%
                            </span>
                        </div>
                        <div class="mb-2">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>₱ {{ totalBudgetUtilized.toLocaleString() }}</span>
                                <span>₱ {{ totalBudgetSet.toLocaleString() }}</span>
                            </div>
                            <div class="relative w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                <div 
                                    class="h-full transition-all duration-500"
                                    :class="budgetUtilizationPercentage > 100 ? 'bg-linear-to-r from-red-500 to-red-600' : 'bg-linear-to-r from-blue-500 to-blue-600'"
                                    :style="{ width: `${Math.min(budgetUtilizationPercentage, 100)}%` }"
                                ></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <span v-if="budgetUtilizationPercentage > 100" class="text-red-600 font-semibold">⚠️ Over budget!</span>
                            <span v-else>Budget tracking helps ensure financial control</span>
                        </p>
                    </div>

                    <!-- Step 0: Project Information -->
                    <div v-if="activeStep === 0" class="space-y-6">
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Project Information</h2>
                            <p class="text-gray-600 text-sm mt-1">Enter basic details about your project</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Project Name<span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        v-model="form.step1.name"
                                        placeholder="Enter project name"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Project Code<span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        v-model="form.step1.code"
                                        placeholder="e.g., PRJ-2026-001"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Project Type<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step1.type"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    >
                                        <option value="">Select project type</option>
                                        <option value="Residential">Residential</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Industrial">Industrial</option>
                                        <option value="Infrastructure">Infrastructure</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Client<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step1.client"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    >
                                        <option value="">Select client</option>
                                        <option v-for="client in clients" :key="client.id" :value="client.id">
                                            {{ formatFullName(client) }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Address<span class="text-red-500">*</span></label>
                                    <textarea 
                                        v-model="form.step1.address"
                                        rows="4"
                                        placeholder="Enter complete site address"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Project Notes</label>
                                    <textarea 
                                        v-model="form.step1.notes"
                                        rows="4"
                                        placeholder="Any additional information..."
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Project Image</label>
                                    <input 
                                        type="file" 
                                        ref="fileInput"
                                        @change="handleImageUpload"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                    <div 
                                        @click="triggerFileInput"
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition"
                                    >
                                        <div v-if="!previewUrl" class="text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm">Click to upload image</p>
                                        </div>
                                        <img v-else :src="previewUrl" class="max-h-32 mx-auto rounded" alt="Preview" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Bid & Budget (Merged Step 1 & 2) -->
                    <div v-if="activeStep === 1" class="space-y-6">
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Bid Details & Budget Setup</h2>
                            <p class="text-gray-600 text-sm mt-1">Configure bidding information and set your budget</p>
                        </div>

                        <!-- Budget Setup Section -->
                        <div class="bg-linear-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
                            <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Budget Setup
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Budget<span class="text-red-500">*</span></label>
                                    <div class="flex">
                                        <input
                                            type="text" 
                                            v-model="form.step5.budget"
                                            @input="(e) => form.step5.budget = formatNumberInput(e.target.value)"
                                            placeholder="Enter total budget"
                                            class="flex-1 px-4 py-2.5 bg-white border border-gray-300 rounded-l-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        <button 
                                            @click="setBudget"
                                            class="px-4 py-2.5 text-sm font-medium bg-[#0C8CE9] text-white border border-transparent rounded-r-lg hover:bg-[#0B7DC9] transition-colors"
                                        >
                                            Set Budget
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bid Details -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="font-bold text-gray-800 text-lg mb-4">Bidding Information</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Submission Deadline<span class="text-red-500">*</span></label>
                                    <input 
                                        type="date" 
                                        v-model="form.step2.deadline"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Opening Date<span class="text-red-500">*</span></label>
                                    <input 
                                        type="date" 
                                        v-model="form.step2.opening"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Type<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step2.type"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select bid type</option>
                                        <option value="Open">Open</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Negotiated">Negotiated</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Required Bond Type<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step2.bondType"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select bond type</option>
                                        <option value="Bid Bond">Bid Bond</option>
                                        <option value="Performance Bond">Performance Bond</option>
                                        <option value="Payment Bond">Payment Bond</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bid Security Amount (%)<span class="text-red-500">*</span></label>
                                    <input 
                                        type="number" 
                                        v-model="form.step2.amount"
                                        placeholder="e.g., 10"
                                        min="1"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Bidding Activities -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Bidding Activities<span class="text-red-500">*</span></h3>
                                <button
                                    @click="showModalBtn('biddingActivities')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-[#0C8CE9] text-white rounded-lg hover:bg-[#0B7DC9] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Activity
                                </button> 
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Activity Name</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(activity, index) in biddingActivities" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ activity.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(activity.date) }}</td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <button 
                                                    @click="biddingActivities.splice(index, 1)"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!biddingActivities.length">
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No activities added yet. Click "Add Activity" to get started.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Documents (Step 3) -->
                    <div v-if="activeStep === 2" class="space-y-6">
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Document Management</h2>
                            <p class="text-gray-600 text-sm mt-1">Manage your document checklist and uploads</p>
                        </div>

                        <!-- Document Checklist -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Document Checklist<span class="text-red-500">*</span></h3>
                                <button
                                    @click="showModalBtn('documentChecklist')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-[#0C8CE9] text-white rounded-lg hover:bg-[#0B7DC9] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Document
                                </button> 
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
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
                                            <td class="px-6 py-4 text-sm text-center">
                                                <button 
                                                    @click="removeDocumentFromChecklist(index)"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!documentChecklist.length">
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No documents in checklist. Click "Add Document" to get started.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Document Upload -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="font-bold text-gray-800 text-lg mb-4">Upload Documents</h3>
                            <div class="max-w-lg mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Choose Document to Upload</label>
                                <select 
                                    v-model="form.step3.document"
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
                                    id="fileUpload"
                                    :disabled="!form.step3.document"
                                />
                                <label 
                                    for="fileUpload"
                                    @drop.prevent="form.step3.document ? handleFileDrop($event) : null"
                                    @dragover.prevent
                                    :class="[
                                        'border-2 border-dashed rounded-lg p-12 flex flex-col items-center justify-center transition-colors',
                                        form.step3.document 
                                            ? 'border-gray-300 bg-gray-50 hover:border-blue-500 hover:bg-blue-50 cursor-pointer' 
                                            : 'border-gray-200 bg-gray-100 cursor-not-allowed opacity-60'
                                    ]"
                                >
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-700 font-medium mb-1">Click to upload or drag and drop</p>
                                    <p class="text-gray-500 text-sm">PDF files only</p>
                                </label>
                            </div>
                        </div>

                        <!-- Uploaded Documents -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="font-bold text-gray-800 text-lg mb-4">Uploaded Documents</h3>
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
                                                <button 
                                                    @click="viewDocument(doc)"
                                                    class="text-blue-600 hover:text-blue-800 font-medium mr-3"
                                                >
                                                    View
                                                </button>
                                                <button 
                                                    @click="removeUploadedDocument(index)"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!documentUploaded.length">
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No documents uploaded yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Team & Resources (Steps 4, 5, 6 merged) -->
                    <div v-if="activeStep === 3" class="space-y-6">
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Team & Resources</h2>
                            <p class="text-gray-600 text-sm mt-1">Assign team members and plan your resources</p>
                        </div>

                        <!-- Team Assignment -->
                        <div class="bg-linear-to-r from-purple-50 to-pink-50 rounded-lg p-6">
                            <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Team Assignment
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Supervisor<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step4.supervisor"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select supervisor</option>
                                        <option v-for="supervisor in supervisors" :key="supervisor.id" :value="supervisor.id">
                                            {{ supervisor.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foreman<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step4.foreman"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select foreman</option>
                                        <option v-for="foreman in foremans" :key="foreman.id" :value="foreman.id">
                                            {{ foreman.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Material Planning -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Materials Planning<span class="text-red-500">*</span></h3>
                                <button 
                                    @click="showModalBtn('materialPlanning')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-[#0C8CE9] text-white rounded-lg hover:bg-[#0B7DC9] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Material
                                </button> 
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Material</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Brand</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Source</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Status</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(material, index) in materialPlanning" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ material.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ material.brand }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ material.quantity }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ material.unit }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ material.source }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span 
                                                    class="px-3 py-1 text-xs font-semibold rounded-full border"
                                                    :class="material.quantity <= material.available ? materialStatusStyles['Sufficient'] : material.available > 0 ? materialStatusStyles['Short'] : materialStatusStyles['Not Available']"
                                                >
                                                    {{ material.quantity <= material.available ? 'Sufficient' : material.available > 0 ? 'Short' : 'Not Available' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <button 
                                                    @click="removeMaterial(index)"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!materialPlanning.length">
                                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No materials added yet. Click "Add Material" to get started.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Equipment Planning -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Equipment Planning<span class="text-red-500">*</span></h3>
                                <button 
                                    @click="showModalBtn('equipmentPlanning')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-[#0C8CE9] text-white rounded-lg hover:bg-[#0B7DC9] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Equipment
                                </button> 
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Equipment</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Brand</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Condition</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Status</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(equipment, index) in equipmentPlanning" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ equipment.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.brand }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.quantity }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.type }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.condition }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span 
                                                    class="px-3 py-1 text-xs font-semibold rounded-full border"
                                                    :class="equipment.quantity <= equipment.available ? equipmentStatusStyles['Available'] : equipmentStatusStyles['Not Available']"
                                                >
                                                    {{ equipment.quantity <= equipment.available ? 'Available' : 'Not Available' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <button 
                                                    @click="removeEquipment(index)"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!equipmentPlanning.length">
                                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No equipment added yet. Click "Add Equipment" to get started.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Financial Review (Step 7) -->
                    <div v-if="activeStep === 4" class="space-y-6">
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Financial Review & Summary</h2>
                            <p class="text-gray-600 text-sm mt-1">Review all financial details and cost plans</p>
                        </div>

                        <!-- Contract Information -->
                        <div class="bg-linear-to-r from-green-50 to-emerald-50 rounded-lg p-6">
                            <h3 class="font-bold text-gray-800 text-lg mb-4">Contract Information</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Amount<span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        v-model="form.step5.contractAmount"
                                        @input="(e) => form.step5.contractAmount = formatNumberInput(e.target.value)"
                                        placeholder="Enter contract amount"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Billing Type<span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.step5.billingType"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select billing type</option>
                                        <option value="Progress">Progress Billing</option>
                                        <option value="Milestone">Milestone Billing</option>
                                        <option value="Fixed">Fixed Price</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Retention (%)<span class="text-red-500">*</span></label>
                                    <input 
                                        type="number" 
                                        v-model="form.step5.retention"
                                        placeholder="e.g., 10"
                                        min="1"
                                        class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Category Breakdown -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Category Breakdown</h3>
                                <button 
                                    @click="showModalBtn('categoryBreakdown')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-[#0C8CE9] text-white rounded-lg hover:bg-[#0B7DC9] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Category
                                </button> 
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(category, index) in categoryBreakdown" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ category.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ category.description }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold">₱ {{ category.amount.toLocaleString() }}</td>
                                        </tr>
                                        <tr class="bg-gray-100 font-bold">
                                            <td colspan="2" class="px-6 py-4 text-sm text-gray-900">Total Budget Utilized</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 text-right">₱ {{ totalBudgetUtilized.toLocaleString() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Cost Plans Grid -->
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Material Cost Plan -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-bold text-gray-800 mb-3">Material Costs</h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <div v-for="(item, index) in materialCostPlan" :key="index" class="flex justify-between text-sm py-2 border-b border-gray-100">
                                        <span class="text-gray-700">
                                            {{ item.brand ? `${item.name} (${item.brand})` : item.name }}
                                        </span>
                                        <span class="font-semibold text-gray-900">₱ {{ item.total.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-bold pt-2">
                                        <span>Total</span>
                                        <span class="text-blue-600">₱ {{ materialCostPlan.reduce((sum, item) => sum + Number(item.total), 0).toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Labor Cost Plan -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-bold text-gray-800 mb-3">Labor Costs</h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <div v-for="(item, index) in laborCostPlan" :key="index" class="flex justify-between text-sm py-2 border-b border-gray-100">
                                        <span class="text-gray-700">{{ item.name }}</span>
                                        <span class="font-semibold text-gray-900">₱ {{ item.total.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-bold pt-2">
                                        <span>Total</span>
                                        <span class="text-blue-600">₱ {{ laborCostPlan.reduce((sum, item) => sum + Number(item.total), 0).toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Equipment Cost Plan -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-bold text-gray-800 mb-3">Equipment Costs</h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <div v-for="(item, index) in equipmentCostPlan" :key="index" class="flex justify-between text-sm py-2 border-b border-gray-100">
                                        <span class="text-gray-700">
                                            {{ item.brand ? `${item.name} (${item.brand})` : item.name }}
                                        </span>
                                        <span class="font-semibold text-gray-900">₱ {{ item.total.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-bold pt-2">
                                        <span>Total</span>
                                        <span class="text-blue-600">₱ {{ equipmentCostPlan.reduce((sum, item) => sum + Number(item.total), 0).toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Subcontractor Cost Plan -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-bold text-gray-800">Subcontractor Costs</h4>
                                    <button 
                                        @click="showModalBtn('subcontractors')"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        + Add
                                    </button>
                                </div>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    <div v-for="(item, index) in subcontractorCostPlan" :key="index" class="flex justify-between text-sm py-2 border-b border-gray-100">
                                        <span class="text-gray-700">{{ item.name }}</span>
                                        <span class="font-semibold text-gray-900">₱ {{ item.cost.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-bold pt-2">
                                        <span>Total</span>
                                        <span class="text-blue-600">₱ {{ subcontractorCostPlan.reduce((sum, item) => sum + Number(item.cost), 0).toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Miscellaneous -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-800 text-lg">Miscellaneous Costs</h3>
                                <button 
                                    @click="showModalBtn('miscellaneous')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-[#0C8CE9] text-white rounded-lg hover:bg-[#0B7DC9] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Item
                                </button> 
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-right">Cost</th>
                                            <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(item, index) in miscellaneousCostPlan" :key="index" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ item.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold">₱ {{ item.cost.toLocaleString() }}</td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <button 
                                                    @click="miscellaneousCostPlan.splice(index, 1)"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!miscellaneousCostPlan.length">
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                No miscellaneous costs added yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Modal -->
    <div
        v-if="showModal"
        class="fixed inset-0 flex items-center justify-center z-50 px-4"
        style="background-color: rgba(0, 0, 0, 0.5);"
    >
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900 text-lg">
                    <span v-if="modal === 'biddingActivities'">Add Bidding Activity</span>
                    <span v-if="modal === 'documentChecklist'">Add Document</span>
                    <span v-if="modal === 'materialPlanning'">Add Material</span>
                    <span v-if="modal === 'equipmentPlanning'">Add Equipment</span>
                    <span v-if="modal === 'categoryBreakdown'">Add Category</span>
                    <span v-if="modal === 'subcontractors'">Add Subcontractor</span>
                    <span v-if="modal === 'miscellaneous'">Add Miscellaneous</span>
                </h3>
                <button
                    @click="closeModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="space-y-4">
                <!-- Bidding Activities Modal -->
                <div v-if="modal === 'biddingActivities'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Activity Name</label>
                        <input 
                            type="text" 
                            v-model="modalForms.biddingActivities.name"
                            placeholder="Enter activity name"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                        <input 
                            type="date" 
                            v-model="modalForms.biddingActivities.date"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>

                <!-- Document Checklist Modal -->
                <div v-if="modal === 'documentChecklist'">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Document Name</label>
                    <input 
                        type="text" 
                        v-model="modalForms.documentChecklist.name"
                        placeholder="Enter document name"
                        class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>

                <!-- Material Planning Modal -->
                <div v-if="modal === 'materialPlanning'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Item</label>
                        <select 
                            v-model="modalForms.materialPlanning.name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select material</option>
                            <option v-for="material in materials" :key="material.id" :value="material.id">
                                {{ material.name }}
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                            <input 
                                type="number" 
                                v-model="modalForms.materialPlanning.quantity"
                                placeholder="0"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                            <input 
                                type="text" 
                                :value="selectedMaterial?.unit || ''"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Source</label>
                            <select 
                                v-model="modalForms.materialPlanning.source"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Select</option>
                                <option value="BOQ">BOQ</option>
                                <option value="Estimate">Estimate</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="selectedMaterial">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Brand</label>
                        <select
                            v-model="modalForms.materialPlanning.brand"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select brand</option>
                            <option v-for="brand in selectedMaterialBrands" :key="brand.name" :value="brand.name">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="selectedMaterialBrand" class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Cost</label>
                            <input
                                type="text"
                                :value="`₱ ${selectedMaterialBrand.cost.toLocaleString()}`"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Availability</label>
                            <input
                                type="text"
                                :value="selectedMaterialBrand.available"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Equipment Planning Modal -->
                <div v-if="modal === 'equipmentPlanning'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Equipment</label>
                        <select 
                            v-model="modalForms.equipmentPlanning.name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select equipment</option>
                            <option v-for="equipment in equipments" :key="equipment.id" :value="equipment.id">
                                {{ equipment.name }}
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                            <input 
                                type="number" 
                                v-model="modalForms.equipmentPlanning.quantity"
                                placeholder="0"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                            <input 
                                type="text" 
                                :value="selectedEquipment?.type || ''"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                    </div>
                    <div v-if="selectedEquipment">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Brand</label>
                        <select
                            v-model="modalForms.equipmentPlanning.brand"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select brand</option>
                            <option v-for="brand in selectedEquipmentBrands" :key="brand.name" :value="brand.name">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="selectedEquipmentBrand" class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Cost</label>
                            <input
                                type="text"
                                :value="`₱ ${selectedEquipmentBrand.cost.toLocaleString()}`"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Availability</label>
                            <input
                                type="text"
                                :value="selectedEquipmentBrand.available"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Condition</label>
                            <input
                                type="text"
                                :value="selectedEquipmentBrand.condition"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown Modal -->
                <div v-if="modal === 'categoryBreakdown'" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                            <input 
                                type="text" 
                                v-model="modalForms.categoryBreakdown.name"
                                placeholder="Category name"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Budget</label>
                            <input 
                                type="text" 
                                v-model="modalForms.categoryBreakdown.budget"
                                @input="(e) => modalForms.categoryBreakdown.budget = formatNumberInput(e.target.value)"
                                placeholder="0"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea 
                            v-model="modalForms.categoryBreakdown.description"
                            rows="4"
                            placeholder="Enter description..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        ></textarea>
                    </div>
                </div>

                <!-- Subcontractors Modal -->
                <div v-if="modal === 'subcontractors'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Subcontractor</label>
                        <select 
                            v-model="modalForms.subcontractors.name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select subcontractor</option>
                            <option v-for="subcontractor in subcontractors" :key="subcontractor.id" :value="subcontractor.id">
                                {{ subcontractor.name }}
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Scope</label>
                            <input 
                                type="text" 
                                :value="selectedSubcontractor?.scope || ''"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cost Code</label>
                            <input 
                                type="text" 
                                :value="selectedSubcontractor?.code || ''"
                                disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg outline-none text-sm"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cost</label>
                        <input 
                            type="text" 
                            v-model="modalForms.subcontractors.cost"
                            @input="(e) => modalForms.subcontractors.cost = formatNumberInput(e.target.value)"
                            placeholder="Enter cost"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>

                <!-- Miscellaneous Modal -->
                <div v-if="modal === 'miscellaneous'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Item Name</label>
                        <input 
                            type="text" 
                            v-model="modalForms.miscellaneous.name"
                            placeholder="Enter item name"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cost</label>
                        <input 
                            type="text" 
                            v-model="modalForms.miscellaneous.cost"
                            @input="(e) => modalForms.miscellaneous.cost = formatNumberInput(e.target.value)"
                            placeholder="Enter cost"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <button 
                    @click="closeModal()" 
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button> 
                <button 
                    @click="submitModal(modal)" 
                    class="px-5 py-2.5 text-sm font-medium bg-linear-to-r from-[#0C8CE9] to-[#0EA5E9] text-white rounded-lg hover:from-[#0B7DC9] hover:to-[#0D94D9] transition-all"
                >
                    Confirm
                </button>  
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div class="fixed bottom-6 right-6 z-50" v-if="loading">
        <img :src="agnes" class="h-16 w-16 rounded-full shadow-lg" />
    </div>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, reactive, onMounted, watch, computed, h } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    /* =====================================================
    Stores & Layouts
    ===================================================== */
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()

    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    } 

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    Assets
    ===================================================== */
    import agnes from '@/assets/agnes.gif'
    import circleFill from '@/assets/circleFill.png'
    import circleCheckFill from '@/assets/circleCheckFill.png'

    /* =====================================================
    Global UI State
    ===================================================== */
    const loading = ref(false)
    const showModal = ref(false)
    const modal = ref(null)

    /* =====================================================
    File Upload State
    ===================================================== */
    const previewUrl = ref(null)
    const fileInput = ref(null)
    const selectedFile = ref(null)

    /* =====================================================
    Form State
    ===================================================== */
    const form = ref({
        step1: {
            name: '',
            code: '',
            type: '',
            address: '',
            client: '',
            notes: '',
            image: null
        },
        step2: {
            deadline: '',
            opening: '',
            type: '',
            bondType: '',
            amount: '',
        },
        step3: {
            document: null,
        },
        step4: {
            supervisor: '',
            foreman: '',
        },
        step5: {
            budget: '',
            contractAmount: '',
            billingType: '',
            retention: null,
        }
    })

    const modalForms = ref({
        biddingActivities: {
            name: '',
            date: '',
        },
        documentChecklist: {
            name: '',
        },
        materialPlanning: {
            name: '',
            quantity: null,
            brand: '',
            source: '',
        },
        equipmentPlanning: {
            name: '',
            quantity: null,
            brand: '',
        },
        categoryBreakdown: {
            name: '',
            description: '',
            budget: '',
        },
        subcontractors: {
            name: '',
            scope: '',
            cost: '',
        },
        miscellaneous: {
            name: '',
            cost: '',
        },
    })

    const clients = ref([])

    const supervisors = ref([
        {
            id: 1,
            name: 'Carlos Santos',
            role: 'Project Supervisor',
            total: 1000000,
        },
        {
            id: 2,
            name: 'Ana Reyes',
            role: 'Project Supervisor',
            total: 850000,
        },
    ])

    const foremans = ref([
        {
            id: 1,
            name: 'Luis Gomez',
            role: 'Project Foreman',
            total: 600000,
        },
        {
            id: 2,
            name: 'Sofia Martinez',
            role: 'Project Foreman',
            total: 750000,
        },
    ])

    const materials = ref([
        {
            id: 1,
            name: 'Cement',
            unit: 'Bags',
            code: 'MAT-001',
        },
        {
            id: 2,
            name: 'Sand',
            unit: 'm³',
            code: 'MAT-002',
        },
    ])

    const materialBrandMap = {
        Cement: [
            { name: 'Holcim', cost: 52, available: 400 },
            { name: 'Republic Cement', cost: 50, available: 350 },
            { name: 'Eagle', cost: 48, available: 300 },
            { name: 'Cemex', cost: 55, available: 250 }
        ],
        Sand: [
            { name: 'Riverbend', cost: 78, available: 200 },
            { name: 'QuarryPro', cost: 82, available: 180 },
            { name: 'StoneLine', cost: 75, available: 220 }
        ]
    }

    const equipments = ref([
        {
            id: 1,
            name: 'Excavator',
            type: 'Heavy',
        },
        {
            id: 2,
            name: 'Concrete Mixer',
            type: 'Light',
        },
    ])

    const equipmentBrandMap = {
        Excavator: [
            { name: 'Caterpillar', cost: 5200, available: 2, condition: 'Good' },
            { name: 'Komatsu', cost: 5100, available: 1, condition: 'Fair' },
            { name: 'Hitachi', cost: 5000, available: 1, condition: 'Good' },
            { name: 'Volvo', cost: 5400, available: 1, condition: 'Excellent' }
        ],
        'Concrete Mixer': [
            { name: 'Honda', cost: 2100, available: 4, condition: 'Good' },
            { name: 'Yanmar', cost: 2000, available: 3, condition: 'Fair' },
            { name: 'Belle', cost: 1900, available: 2, condition: 'Good' }
        ]
    }

    const subcontractors = ref([
        {
            id: 1,
            name: 'Excavator Contractors Ltd.',
            scope: 'Excavation Works',
            code: 'SUB-001',
        },
    ])

    const biddingActivities = ref([]);
    const documentChecklist = ref([]);
    const documentUploaded = ref([]);
    const materialPlanning = ref([]);
    const equipmentPlanning = ref([]);
    const totalBudgetSet = ref(0);
    const categoryBreakdownList = ref([]);
    const materialCostPlan = ref([]);
    const laborCostPlan = ref([]);
    const equipmentCostPlan = ref([]);
    const subcontractorCostPlan = ref([]);
    const miscellaneousCostPlan = ref([]);

    const materialStatusStyles = {
        'Sufficient': 'bg-[#dbfce7] text-[#00a63e] border-[#bbf8d1]',
        'Short': 'bg-[#ffffcf] text-[#651a1a] border-[#ffe990]',
        'Not Available': 'bg-[#ffe2e2] text-[#651A1A] border-[#ffcccc]'
    }

    const equipmentStatusStyles = {
        'Available': 'bg-[#dbfce7] text-[#00a63e] border-[#bbf8d1]',
        'Not Available': 'bg-[#ffe2e2] text-[#651A1A] border-[#ffcccc]'
    }

    const showModalBtn = (modalName) => {
        modal.value = modalName
        showModal.value = true
        return
    }

    const closeModal = () => {
        showModal.value = false
        modal.value = null
        
        modalForms.value.biddingActivities = { name: '', date: '' }
        modalForms.value.documentChecklist = { name: '' }
        modalForms.value.materialPlanning = { name: '', quantity: null, brand: '', source: '' }
        modalForms.value.equipmentPlanning = { name: '', quantity: null, brand: '' }
        modalForms.value.categoryBreakdown = { name: '', description: '', budget: '' }
        modalForms.value.subcontractors = { name: '', scope: '', cost: '' }
        modalForms.value.miscellaneous = { name: '', cost: '' }
        
        return
    }
    
    const formatNumberInput = (value) => {
        if (value === '' || value === null) return ''
        
        let numValue = value.toString().replace(/[^\d.]/g, '')
        
        const parts = numValue.split('.')
        if (parts.length > 2) {
            numValue = parts[0] + '.' + parts.slice(1).join('')
        }
        
        const [intPart, decPart] = numValue.split('.')
        
        const formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        
        return decPart !== undefined ? formattedInt + '.' + decPart : formattedInt
    }
    
    const submitModal = (modal) => {
        if (modal === 'biddingActivities') {
            if (!modalForms.value.biddingActivities.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter <b>Activity Name</b>' })
                return
            }
            if (!modalForms.value.biddingActivities.date?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please select a <b>Date</b>' })
                return
            }
            biddingActivities.value.push({
                name: modalForms.value.biddingActivities.name,
                date: modalForms.value.biddingActivities.date,
            })
        } else if (modal === 'documentChecklist') {
            if (!modalForms.value.documentChecklist.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter <b>Document Name</b>' })
                return
            }
            
            // Check for duplicate document names
            if (documentChecklist.value.some(doc => doc.name === modalForms.value.documentChecklist.name)) {
                showToast({ icon: 'warning', html: 'A document with this name already exists' })
                return
            }
            
            documentChecklist.value.push({
                name: modalForms.value.documentChecklist.name,
                completed: false,
            })
        } else if (modal === 'materialPlanning') {
            if (!modalForms.value.materialPlanning.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please select an <b>Item</b>' })
                return
            }
            if (!modalForms.value.materialPlanning.quantity) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Quantity</b>' })
                return
            }
            if (!modalForms.value.materialPlanning.brand?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Brand</b>' })
                return
            }
            if (!modalForms.value.materialPlanning.source) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Source</b>' })
                return
            }
            const selected = materials.value.find(
                material => material.id == modalForms.value.materialPlanning.name
            )
            if (selected) {
                const quantity = Number(modalForms.value.materialPlanning.quantity)
                const unitCost = selectedMaterialBrand.value?.cost ?? selected.cost
                const total = quantity * unitCost
                const brand = modalForms.value.materialPlanning.brand?.toString().trim()
                const brandAvailability = selectedMaterialBrand.value?.available ?? selected.available
                
                const existingCostPlan = materialCostPlan.value.find(item =>
                    item.name === selected.name &&
                    item.brand?.toString().trim().toLowerCase() === brand.toLowerCase()
                )
                if (existingCostPlan) {
                    existingCostPlan.quantity += quantity
                    existingCostPlan.total = existingCostPlan.quantity * existingCostPlan.cost
                } else {
                    materialCostPlan.value.push({
                        name: selected.name,
                        brand: brand,
                        quantity: quantity,
                        cost: unitCost,
                        total: total,
                        code: selected.code,
                    })
                }
                
                const existingMaterial = materialPlanning.value.find(item =>
                    item.name === selected.name &&
                    item.brand?.toString().trim().toLowerCase() === brand.toLowerCase()
                )
                if (existingMaterial) {
                    existingMaterial.quantity = Number(existingMaterial.quantity) + quantity
                } else {
                    materialPlanning.value.push({
                        id: selected.id,
                        name: selected.name,
                        brand: brand,
                        unit: selected.unit,
                        quantity: quantity,
                        available: brandAvailability,
                        source: modalForms.value.materialPlanning.source,
                        cost: unitCost,
                        code: selected.code,
                    })
                }

                showToast({
                    icon: 'success',
                    html: `<b>${selected.name}</b> (${brand}) added to materials` 
                })
            }
        } else if (modal === 'equipmentPlanning') {
            if (!modalForms.value.equipmentPlanning.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please select an <b>Equipment</b>' })
                return
            }
            if (!modalForms.value.equipmentPlanning.quantity) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Quantity</b>' })
                return
            }
            if (!modalForms.value.equipmentPlanning.brand?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Brand</b>' })
                return
            }
            const selected = equipments.value.find(
                equipment => equipment.id == modalForms.value.equipmentPlanning.name
            )
            if (selected) {
                const quantity = Number(modalForms.value.equipmentPlanning.quantity)
                const unitCost = selectedEquipmentBrand.value?.cost ?? selected.cost
                const total = quantity * unitCost
                const brand = modalForms.value.equipmentPlanning.brand?.toString().trim()
                const brandAvailability = selectedEquipmentBrand.value?.available ?? selected.available
                const brandCondition = selectedEquipmentBrand.value?.condition || 'Good'
                
                const existingCostPlan = equipmentCostPlan.value.find(item =>
                    item.name === selected.name &&
                    item.brand?.toString().trim().toLowerCase() === brand.toLowerCase()
                )
                if (existingCostPlan) {
                    existingCostPlan.quantity += quantity
                    existingCostPlan.total = existingCostPlan.quantity * existingCostPlan.rate
                } else {
                    equipmentCostPlan.value.push({
                        name: selected.name,
                        brand: brand,
                        quantity: quantity,
                        rate: unitCost,
                        total: total,
                    })
                }
                
                const existingEquipment = equipmentPlanning.value.find(item =>
                    item.name === selected.name &&
                    item.brand?.toString().trim().toLowerCase() === brand.toLowerCase()
                )
                if (existingEquipment) {
                    existingEquipment.quantity = Number(existingEquipment.quantity) + quantity
                } else {
                    equipmentPlanning.value.push({
                        id: selected.id,
                        name: selected.name,
                        brand: brand,
                        type: selected.type,
                        quantity: quantity,
                        available: brandAvailability,
                        condition: brandCondition,
                        cost: unitCost,
                    })
                }

                showToast({
                    icon: 'success',
                    html: `<b>${selected.name}</b> (${brand}) added to equipment` 
                })
            }
        } else if (modal === 'categoryBreakdown') {
            if (!modalForms.value.categoryBreakdown.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Category Name</b>' })
                return
            }
            if (!modalForms.value.categoryBreakdown.budget?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Budget</b>' })
                return
            }
            const budgetAmount = Number(modalForms.value.categoryBreakdown.budget.toString().replace(/,/g, ''))
            categoryBreakdownList.value.push({
                name: modalForms.value.categoryBreakdown.name,
                description: modalForms.value.categoryBreakdown.description,
                amount: budgetAmount,
            })
        } else if (modal === 'subcontractors') {
            if (!modalForms.value.subcontractors.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please select a <b>Subcontractor</b>' })
                return
            }
            if (!modalForms.value.subcontractors.cost?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Cost</b>' })
                return
            }
            const selected = subcontractors.value.find(
                subcontractor => subcontractor.id == modalForms.value.subcontractors.name
            )
            if (selected) {
                const cost = Number(modalForms.value.subcontractors.cost.toString().replace(/,/g, ''))
                subcontractorCostPlan.value.push({
                    id: selected.id,
                    name: selected.name,
                    scope: selected.scope,
                    cost: cost,
                    code: selected.code,
                })
            }
        } else if (modal === 'miscellaneous') {
            if (!modalForms.value.miscellaneous.name?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter an <b>Item Name</b>' })
                return
            }
            if (!modalForms.value.miscellaneous.cost?.toString().trim()) {
                showToast({ icon: 'warning', html: 'Please enter a <b>Cost</b>' })
                return
            }
            const cost = Number(modalForms.value.miscellaneous.cost.toString().replace(/,/g, ''))
            miscellaneousCostPlan.value.push({
                name: modalForms.value.miscellaneous.name,
                cost: cost,
            })
        }

        closeModal()
    }

    /* =====================================================
    Computed
    ===================================================== */
    const selectedMaterial = computed(() => {
        if (!modalForms.value.materialPlanning.name) return null
        return materials.value.find(
            material => material.id == modalForms.value.materialPlanning.name
        ) || null
    })

    const selectedMaterialBrands = computed(() => {
        return materialBrandMap[selectedMaterial.value?.name] || []
    })

    const selectedMaterialBrand = computed(() => {
        return selectedMaterialBrands.value.find(
            brand => brand.name === modalForms.value.materialPlanning.brand
        ) || null
    })

    const selectedEquipment = computed(() => {
        if (!modalForms.value.equipmentPlanning.name) return null
        return equipments.value.find(
            equipment => equipment.id == modalForms.value.equipmentPlanning.name
        ) || null
    })

    const selectedEquipmentBrands = computed(() => {
        return equipmentBrandMap[selectedEquipment.value?.name] || []
    })

    const selectedEquipmentBrand = computed(() => {
        return selectedEquipmentBrands.value.find(
            brand => brand.name === modalForms.value.equipmentPlanning.brand
        ) || null
    })

    const selectedSubcontractor = computed(() => {
        if (!modalForms.value.subcontractors.name) return null
        return subcontractors.value.find(
            subcontractor => subcontractor.id == modalForms.value.subcontractors.name
        ) || null
    })

    const categoryBreakdown = computed(() => {
        const breakdown = [...categoryBreakdownList.value]
        
        if (materialCostPlan.value.length > 0) {
            const total = materialCostPlan.value.reduce((sum, item) => sum + Number(item.total), 0)
            breakdown.push({
                name: 'Materials',
                description: 'Material costs',
                amount: total
            })
        }
        
        if (laborCostPlan.value.length > 0) {
            const total = laborCostPlan.value.reduce((sum, item) => sum + Number(item.total), 0)
            breakdown.push({
                name: 'Labor',
                description: 'Labor costs',
                amount: total
            })
        }
        
        if (equipmentCostPlan.value.length > 0) {
            const total = equipmentCostPlan.value.reduce((sum, item) => sum + Number(item.total), 0)
            breakdown.push({
                name: 'Equipment',
                description: 'Equipment costs',
                amount: total
            })
        }
        
        if (subcontractorCostPlan.value.length > 0) {
            const total = subcontractorCostPlan.value.reduce((sum, item) => sum + Number(item.cost), 0)
            breakdown.push({
                name: 'Subcontractors',
                description: 'Subcontractor costs',
                amount: total
            })
        }
        
        if (miscellaneousCostPlan.value.length > 0) {
            const total = miscellaneousCostPlan.value.reduce((sum, item) => sum + Number(item.cost), 0)
            breakdown.push({
                name: 'Miscellaneous',
                description: 'Miscellaneous costs',
                amount: total
            })
        }
        
        return breakdown
    })

    const totalBudgetUtilized = computed(() => {
        return categoryBreakdown.value.reduce((sum, item) => sum + Number(item.amount), 0)
    })

    const budgetUtilizationPercentage = computed(() => {
        if (totalBudgetSet.value === 0) return 0
        return (totalBudgetUtilized.value / totalBudgetSet.value) * 100
    })

    const setBudget = () => {
        if (!form.value.step5.budget?.toString().trim()) {
            showToast({ icon: 'warning', html: 'Please enter a <b>Total Budget</b>' })
            return
        }
        
        const budgetValue = Number(form.value.step5.budget.toString().replace(/,/g, ''))
        totalBudgetSet.value = budgetValue
        
        showToast({ 
            icon: 'success', 
            html: `Budget set to <b>₱ ${budgetValue.toLocaleString()}</b>` 
        })
    }

    const updateLaborCostPlan = () => {
        laborCostPlan.value = []
        
        if (form.value.step4.supervisor) {
            const selectedSupervisor = supervisors.value.find(
                supervisor => supervisor.id == form.value.step4.supervisor
            )
            if (selectedSupervisor) {
                laborCostPlan.value.push({
                    id: selectedSupervisor.id,
                    name: selectedSupervisor.name,
                    role: selectedSupervisor.role,
                    quantity: 1,
                    headcount: 1,
                    rate: selectedSupervisor.total,
                    total: selectedSupervisor.total,
                })
            }
        }
        
        if (form.value.step4.foreman) {
            const selectedForeman = foremans.value.find(
                foreman => foreman.id == form.value.step4.foreman
            )
            if (selectedForeman) {
                laborCostPlan.value.push({
                    id: selectedForeman.id,
                    name: selectedForeman.name,
                    role: selectedForeman.role,
                    quantity: 1,
                    headcount: 1,
                    rate: selectedForeman.total,
                    total: selectedForeman.total,
                })
            }
        }
    }

    /* =====================================================
    Toast Helper
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
            timer
        })
    }

    const formatDate = (date) => {
        const d = new Date(date)
        const months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ]
        return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`
    }

    /* =====================================================
    Input & Upload Handlers
    ===================================================== */
    const handleImageUpload = (event) => {
        const file = event.target.files[0]
        if (!file) return

        selectedFile.value = file
        previewUrl.value = URL.createObjectURL(file)
    }

    const triggerFileInput = () => {
        fileInput.value.click()
    }

    const handleFileUpload = (event) => {
        if (!form.value.step3.document?.toString().trim()) {
            showToast({ icon: 'warning', html: 'Please select a <b>document</b> first' })
            return
        }

        const file = event.target.files[0]
        if (!file) return

        if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
            showToast({ icon: 'error', html: 'Only <b>PDF files</b> are allowed' })
            event.target.value = ''
            return
        }

        const documentIndex = documentChecklist.value.findIndex(doc => doc.name === form.value.step3.document)
        if (documentIndex !== -1) {
            documentChecklist.value[documentIndex].completed = true
        }

        documentUploaded.value.push({
            name: form.value.step3.document,
            file: file,
            uploadedAt: new Date().toLocaleString(),
            fileUrl: URL.createObjectURL(file)
        })

        showToast({
            icon: 'success',
            html: `<b>${form.value.step3.document}</b> uploaded successfully`
        })

        form.value.step3.document = ''
        event.target.value = ''
    }

    const handleFileDrop = (event) => {
        if (!form.value.step3.document?.toString().trim()) {
            showToast({ icon: 'warning', html: 'Please select a <b>document</b> first' })
            return
        }

        const file = event.dataTransfer.files[0]
        if (!file) return

        if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
            showToast({ icon: 'error', html: 'Only <b>PDF files</b> are allowed' })
            return
        }

        const documentIndex = documentChecklist.value.findIndex(doc => doc.name === form.value.step3.document)
        if (documentIndex !== -1) {
            documentChecklist.value[documentIndex].completed = true
        }

        documentUploaded.value.push({
            name: form.value.step3.document,
            file: file,
            uploadedAt: new Date().toLocaleString(),
            fileUrl: URL.createObjectURL(file)
        })

        showToast({
            icon: 'success',
            html: `<b>${form.value.step3.document}</b> uploaded successfully`
        })

        form.value.step3.document = ''
    }

    const viewDocument = (doc) => {
        if (doc.fileUrl) {
            window.open(doc.fileUrl, '_blank')
        } else {
            showToast({ icon: 'info', html: 'Document file not available' })
        }
    }

    const removeUploadedDocument = (index) => {
        const doc = documentUploaded.value[index]
        if (doc) {
            // Reset the document status in checklist to pending
            const documentIndex = documentChecklist.value.findIndex(item => item.name === doc.name)
            if (documentIndex !== -1) {
                documentChecklist.value[documentIndex].completed = false
            }
            
            // Remove the document from uploaded list
            documentUploaded.value.splice(index, 1)
            
            showToast({
                icon: 'info',
                html: `<b>${doc.name}</b> removed from uploaded documents`
            })
        }
    }

    const removeDocumentFromChecklist = (index) => {
        const doc = documentChecklist.value[index]
        if (doc) {
            // Find and remove the document from uploaded documents
            const uploadedIndex = documentUploaded.value.findIndex(item => item.name === doc.name)
            if (uploadedIndex !== -1) {
                documentUploaded.value.splice(uploadedIndex, 1)
            }
            
            // Remove the document from checklist
            documentChecklist.value.splice(index, 1)
            
            showToast({
                icon: 'info',
                html: `<b>${doc.name}</b> removed from document checklist`
            })
        }
    }

    const removeMaterial = (index) => {
        const material = materialPlanning.value[index]
        if (material) {
            // Remove from cost plan
            const costPlanIndex = materialCostPlan.value.findIndex(item =>
                item.name === material.name && item.brand === material.brand
            )
            if (costPlanIndex !== -1) {
                materialCostPlan.value.splice(costPlanIndex, 1)
            }
            
            // Remove from material planning
            materialPlanning.value.splice(index, 1)
            
            showToast({
                icon: 'info',
                html: `<b>${material.name}</b> removed from materials`
            })
        }
    }

    const removeEquipment = (index) => {
        const equipment = equipmentPlanning.value[index]
        if (equipment) {
            // Remove from cost plan
            const costPlanIndex = equipmentCostPlan.value.findIndex(item =>
                item.name === equipment.name && item.brand === equipment.brand
            )
            if (costPlanIndex !== -1) {
                equipmentCostPlan.value.splice(costPlanIndex, 1)
            }
            
            // Remove from equipment planning
            equipmentPlanning.value.splice(index, 1)
            
            showToast({
                icon: 'info',
                html: `<b>${equipment.name}</b> removed from equipment`
            })
        }
    }

    /* =====================================================
    Validation Helpers
    ===================================================== */
    const validateForm = (form, requiredFields) => {
        for (const [key, label] of Object.entries(requiredFields)) {
            if (!form[key]?.toString().trim()) {
                showToast({
                    icon: 'warning',
                    html: `Please enter the <b>${label}</b>`
                })
                return true
            }
        }
        return false
    }

    const checkRequiredFields = () => {
        const formConfigs = [
            {
                form: form.value.step1,
                fields: {
                    name: 'Project Name',
                    code: 'Project Code',
                    type: 'Project Type',
                    address: 'Site Address',
                    client: 'Client Name',
                }
            },
            {
                form: form.value.step2,
                fields: {
                    deadline: 'Bid Submission Deadline',
                    opening: 'Bid Opening Date',
                    type: 'Bid Type',
                    bondType: 'Required Bond Type',
                    amount: 'Bid Security Amount',
                }
            },
            {
                form: null,
                fields: {}
            },
            {
                form: form.value.step4,
                fields: {
                    supervisor: 'Supervisor',
                    foreman: 'Foreman',
                }
            },
        ]

        const stepMap = [0, 1, 2, 3, null]
        const configIndex = stepMap[activeStep.value]
        
        if (configIndex === null) return false
        const config = formConfigs[configIndex]
        if (!config) return false
        
        const hasFieldError = validateForm(config.form, config.fields)
        if (hasFieldError) return true

        if (activeStep.value === 1) {
            if (!form.value.step5.budget?.toString().trim() || totalBudgetSet.value <= 0) {
                showToast({
                    icon: 'warning',
                    html: 'Please set the <b>Total Budget</b>'
                })
                return true
            }
            if (!biddingActivities.value.length) {
                showToast({
                    icon: 'warning',
                    html: 'Please add at least one <b>Bidding Activity</b>'
                })
                return true
            }
        }

        if (activeStep.value === 2) {
            if (!documentChecklist.value.length) {
                showToast({
                    icon: 'warning',
                    html: 'Please add at least one <b>Document</b>'
                })
                return true
            }
        }

        if (activeStep.value === 3) {
            if (!materialPlanning.value.length) {
                showToast({
                    icon: 'warning',
                    html: 'Please add at least one <b>Material Planning</b> item'
                })
                return true
            }
            if (!equipmentPlanning.value.length) {
                showToast({
                    icon: 'warning',
                    html: 'Please add at least one <b>Equipment Planning</b> item'
                })
                return true
            }
        }

        return false
    }

    /* =====================================================
    Submit Logic
    ===================================================== */
    const handleSubmit = async () => {
        const requiredFields = {
            budget: 'Total Budget',
            contractAmount: 'Contract Amount',
            billingType: 'Billing Type',
            retention: 'Retention',
        }
        for (const [key, label] of Object.entries(requiredFields)) {
            if (!form.value.step5[key]?.toString().trim()) {
                showToast({
                    icon: 'warning',
                    html: `Please enter the <b>${label}</b>`
                })
                return
            }
        }

        if (budgetUtilizationPercentage.value > 100) {
            showToast({
                icon: 'warning',
                html: 'Cannot proceed. The project is <b>over budget</b>.'
            })
            return
        }

        if (!categoryBreakdown.value.length) {
            showToast({
                icon: 'warning',
                html: 'Please add at least one <b>Category Breakdown</b> item'
            })
            return
        }
        if (!materialCostPlan.value.length) {
            showToast({
                icon: 'warning',
                html: 'Please add at least one <b>Material Cost Plan</b> item'
            })
            return
        }
        if (!laborCostPlan.value.length) {
            showToast({
                icon: 'warning',
                html: 'Please add at least one <b>Labor Cost Plan</b> item'
            })
            return
        }
        if (!equipmentCostPlan.value.length) {
            showToast({
                icon: 'warning',
                html: 'Please add at least one <b>Equipment Cost Plan</b> item'
            })
            return
        }

        loading.value = true

        try {
            const formData = new FormData()

            if (selectedFile.value) {
                formData.append('image', selectedFile.value)
            }

            documentUploaded.value.forEach((doc, index) => {
                if (doc.file) {
                    formData.append(`documents[${index}]`, doc.file)
                }
            })

            const payload = {
                projectInfo: {
                    name: form.value.step1.name,
                    code: form.value.step1.code,
                    type: form.value.step1.type,
                    address: form.value.step1.address,
                    client_id: form.value.step1.client,
                    notes: form.value.step1.notes,
                },
                bidDetails: {
                    deadline: form.value.step2.deadline,
                    opening: form.value.step2.opening,
                    type: form.value.step2.type,
                    bondType: form.value.step2.bondType,
                    amount: Number(form.value.step2.amount.toString().replace(/,/g, '')),
                },
                documents: {
                    checklist: documentChecklist.value,
                    uploaded: documentUploaded.value.map(doc => ({
                        name: doc.name,
                        uploadedAt: doc.uploadedAt,
                    })),
                },
                projectSetup: {
                    supervisor: supervisors.value.find(s => s.id == form.value.step4.supervisor) || null,
                    foreman: foremans.value.find(f => f.id == form.value.step4.foreman) || null,
                },
                financial: {
                    totalBudget: Number(form.value.step5.budget.toString().replace(/,/g, '')),
                    contractAmount: Number(form.value.step5.contractAmount.toString().replace(/,/g, '')),
                    billingType: form.value.step5.billingType,
                    retention: Number(form.value.step5.retention),
                    totalBudgetSet: totalBudgetSet.value,
                    totalBudgetUtilized: totalBudgetUtilized.value,
                },
                biddingActivities: biddingActivities.value,
                materialPlanning: materialPlanning.value,
                equipmentPlanning: equipmentPlanning.value,
                costPlans: {
                    materials: materialCostPlan.value,
                    labor: laborCostPlan.value,
                    equipment: equipmentCostPlan.value,
                    subcontractors: subcontractorCostPlan.value,
                    miscellaneous: miscellaneousCostPlan.value,
                },
                categoryBreakdown: categoryBreakdown.value,
                categoryBreakdownCustom: categoryBreakdownList.value,
            }

            formData.append('data', JSON.stringify(payload))

            const response = await axios.post('/api/prebiddings', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${auth.token}`
                }
            })

            showToast({
                icon: 'success',
                html: 'Pre-bidding document created successfully.'
            })

            setTimeout(() => {
                router.push('/prebiddings')
            }, 500)

        } catch (err) {
            if (err.response?.status === 422) {
                const errors = err.response.data.errors
                let errorList = '<ul style="text-align:left;font-size:0.85rem;">'

                Object.values(errors).forEach(messages => {
                    messages.forEach(msg => {
                        errorList += `<li>${msg}</li>`
                    })
                })

                errorList += '</ul>'

                showToast({
                    icon: 'error',
                    title: 'Validation Failed',
                    html: errorList,
                    timer: 6000
                })
            } else {
                showToast({
                    icon: 'error',
                    title: 'Error',
                    html: err.response?.data?.message || 'An unexpected server error occurred.'
                })
                console.error(err)
            }
        } finally {
            loading.value = false
        }
    }

    const fetchClients = async () => {
        try {
            const res = await axios.get('/api/clients')
            clients.value = res.data.clients
        } catch (err) {
            console.error('Fetch error:', err)
            showToast({
                icon: 'error',
                title: 'Error',
                html: 'Failed to load clients'
            })
        }
    }

    const formatFullName = (client) => {
        const last = client.last_name || ''
        const first = client.first_name || ''
        const mi = client.middle_initial ? ` ${client.middle_initial}` : ''
        return `${last}, ${first}${mi}`
    }

    /* =====================================================
    Stepper Configuration - Merged to 5 Steps
    ===================================================== */
    const ProjectIcon = {
        render() {
            return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
            ])
        }
    }
    const BidIcon = {
        render() {
            return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
            ])
        }
    }
    const DocumentIcon = {
        render() {
            return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
            ])
        }
    }
    const TeamIcon = {
        render() {
            return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
            ])
        }
    }
    const FinancialIcon = {
        render() {
            return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z' })
            ])
        }
    }

    const steps = [
        {
            label: 'Project Info',
            icon: ProjectIcon
        },
        {
            label: 'Bid & Budget',
            icon: BidIcon
        },
        {
            label: 'Documents',
            icon: DocumentIcon
        },
        {
            label: 'Team & Resources',
            icon: TeamIcon
        },
        {
            label: 'Financial Review',
            icon: FinancialIcon
        },
    ]

    const activeStep = ref(0)
    const maxStepReached = ref(0)

    const goToStep = (index) => {
        if (index < activeStep.value) {
            activeStep.value = index
            return
        }

        if (index === activeStep.value) return

        if (checkRequiredFields()) return

        if (index <= maxStepReached.value) {
            activeStep.value = index
        }
    }

    const nextStep = () => {
        if (checkRequiredFields()) return

        if (activeStep.value < steps.length - 1) {
            activeStep.value++
            maxStepReached.value = Math.max(maxStepReached.value, activeStep.value)
        }
    }

    const getStepStatus = (index) => {
        if (index < maxStepReached.value) return 'completed'
        if (index === activeStep.value) return 'current'
        return 'upcoming'
    }

    const getStepIcon = (step, index) => {
        return getStepStatus(index) === 'completed'
            ? step.icons.check
            : step.icons.circle
    }

    const getIconClass = (index) => {
        return getStepStatus(index) === 'upcoming'
            ? 'opacity-50'
            : 'opacity-100'
    }

    const getStepCircleClass = (index) => {
        if (index < activeStep.value) {
            return 'bg-gradient-to-r from-[#0C8CE9] to-[#0EA5E9] text-white shadow-md scale-105'
        } else if (index === activeStep.value) {
            return 'bg-white border-4 border-[#0C8CE9] text-[#0C8CE9] shadow-lg scale-110'
        } else {
            return 'bg-gray-200 text-gray-400'
        }
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        auth.pageTitle = 'Create Prebidding'
        fetchClients()
    })

    watch(() => form.value.step4.supervisor, () => {
        updateLaborCostPlan()
    })

    watch(() => form.value.step4.foreman, () => {
        updateLaborCostPlan()
    })

    watch(() => modalForms.value.materialPlanning.name, () => {
        modalForms.value.materialPlanning.brand = ''
    })

    watch(() => modalForms.value.equipmentPlanning.name, () => {
        modalForms.value.equipmentPlanning.brand = ''
    })
</script>

<style>
    /* Smooth transitions for all interactive elements */
    * {
        transition-property: color, background-color, border-color, transform, opacity;
        transition-duration: 200ms;
        transition-timing-function: ease-in-out;
    }
</style>
