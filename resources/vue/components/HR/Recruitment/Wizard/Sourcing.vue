<template>
    <div class="space-y-6">
        <!-- TOP SECTION: CV Upload & Candidate Entry (Full Width) -->
        <div class="flex flex-col gap-6">
            <!-- Left Panel: CV Upload & Manual Entry -->
            <div class="space-y-4">
                <!-- Manual Candidate Entry Form -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 text-sm">Add Candidate Manually</h4>
                    <div class="space-y-2">
                        <input 
                            v-model="manualCandidate.name"
                            type="text" 
                            placeholder="Full Name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                        <input 
                            v-model="manualCandidate.email"
                            type="email" 
                            placeholder="Email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                        <select 
                            v-model="manualCandidate.source"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select Source</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="JobBoard">Job Board</option>
                            <option value="Referral">Referral</option>
                            <option value="TalentPool">Talent Pool</option>
                        </select>
                        <button 
                            @click="addManualCandidate"
                            class="w-full px-3 py-2 bg-[#0c8ce9] text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors"
                        >
                            Add Candidate
                        </button>
                    </div>
                </div>

                
                <!-- Drag & Drop Upload -->
                <div
                    @drop.prevent="handleDrop"
                    @dragover.prevent
                    @dragenter.prevent
                    :class="[
                        'border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-all',
                        dragOver ? 'border-[#0c8ce9] bg-blue-50' : 'border-gray-300 hover:border-gray-400'
                    ]"
                    @mouseenter="dragOver = true"
                    @mouseleave="dragOver = false"
                >
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm font-semibold text-gray-700">Drag & drop CVs here</p>
                    <p class="text-xs text-gray-500 mt-1">or click to browse</p>
                    <input 
                        type="file" 
                        multiple 
                        accept=".pdf,.doc,.docx"
                        @change="handleFileSelect"
                        class="hidden"
                        ref="fileInput"
                    />
                    <button 
                        @click="$refs.fileInput?.click()"
                        class="mt-3 px-4 py-1 bg-[#0c8ce9] text-white text-xs font-semibold rounded hover:bg-blue-700 transition-colors"
                    >
                        Browse
                    </button>
                </div>
            </div>

            <!-- Right Panel: Candidates Table -->
            <div class="">
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="px-6 py-3 bg-gray-100 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900">Candidates</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">
                                        <input 
                                            type="checkbox" 
                                            @change="selectAllCandidates"
                                            :checked="candidates.length > 0 && candidates.every(c => c.selected)"
                                            class="rounded"
                                        />
                                    </th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Name</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Position</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Source</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Date Added</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Status</th>
                                    <th class="px-6 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="candidate in candidates" :key="candidate.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <input 
                                            type="checkbox" 
                                            v-model="candidate.selected"
                                            class="rounded"
                                        />
                                    </td>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ candidate.name }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ candidate.position }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ candidate.source }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ formatDate(candidate.dateAdded) }}</td>
                                    <td class="px-6 py-3 text-sm">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold',
                                            candidate.status === 'For Approval' ? 'bg-yellow-100 text-yellow-800' :
                                            candidate.status === 'Approved' ? 'bg-green-100 text-green-800' :
                                            'bg-red-100 text-red-800'
                                        ]">
                                            {{ candidate.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <button 
                                            @click="viewCandidate(candidate.id)"
                                            class="text-[#0c8ce9] hover:text-blue-700 text-sm font-semibold"
                                        >
                                            View CV
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="candidates.length === 0" class="text-center py-8 text-gray-500">
                            <p>No candidates added yet</p>
                        </div>
                    </div>
                </div>

                <!-- Send for Approval Button -->
                <div v-if="candidates.length > 0" class="mt-4">
                    <button 
                        @click="sendForApproval"
                        :disabled="!candidates.some(c => c.selected)"
                        :class="[
                            'px-6 py-2 rounded-lg font-semibold transition-colors flex items-center gap-2',
                            candidates.some(c => c.selected)
                                ? 'bg-[#0c8ce9] text-white hover:bg-blue-700'
                                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                        ]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Send {{ selectedCount }} for Approval
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import Swal from 'sweetalert2'

    const props = defineProps({
        requestId: {
            type: String,
            required: false,
            default: ''
        }
    })

    const emit = defineEmits(['candidates-ready'])

    const dragOver = ref(false)
    const showTalentPoolImport = ref(false)
    const fileInput = ref(null)
    const candidates = ref([
        {
            id: 1,
            name: 'John Smith',
            position: 'Senior Engineer',
            source: 'LinkedIn',
            dateAdded: '2026-01-25',
            status: 'For Approval',
            selected: false
        },
        {
            id: 2,
            name: 'Jane Doe',
            position: 'Senior Engineer',
            source: 'Referral',
            dateAdded: '2026-01-24',
            status: 'For Approval',
            selected: false
        },
        {
            id: 3,
            name: 'Bob Johnson',
            position: 'Senior Engineer',
            source: 'JobBoard',
            dateAdded: '2026-01-23',
            status: 'Approved',
            selected: false
        }
    ])

    const manualCandidate = ref({
        name: '',
        email: '',
        source: ''
    })

    const selectedCount = computed(() => candidates.value.filter(c => c.selected).length)

    const handleDrop = (e) => {
        dragOver.value = false
        const files = e.dataTransfer.files
        processFiles(files)
    }

    const handleFileSelect = (e) => {
        const files = e.target.files
        processFiles(files)
    }

    const processFiles = (files) => {
        for (let file of files) {
            const candidate = {
                id: Date.now() + Math.random(),
                name: file.name.replace(/\.[^/.]+$/, ''),
                position: 'Senior Engineer', // Would be determined from context
                source: 'Upload',
                dateAdded: new Date().toISOString().split('T')[0],
                status: 'For Approval',
                selected: false
            }
            candidates.value.push(candidate)
        }
    }

    const addManualCandidate = () => {
        if (!manualCandidate.value.name || !manualCandidate.value.email || !manualCandidate.value.source) {
            Swal.fire('Error', 'Please fill in all fields', 'error')
            return
        }

        candidates.value.push({
            id: Date.now(),
            name: manualCandidate.value.name,
            position: 'Senior Engineer',
            source: manualCandidate.value.source,
            dateAdded: new Date().toISOString().split('T')[0],
            status: 'For Approval',
            selected: false
        })

        manualCandidate.value = { name: '', email: '', source: '' }
        Swal.fire('Success', 'Candidate added successfully', 'success')
    }

    const selectAllCandidates = (e) => {
        candidates.value.forEach(c => c.selected = e.target.checked)
    }

    const viewCandidate = (candidateId) => {
        Swal.fire({
            title: 'Candidate CV',
            text: 'View CV content here',
            icon: 'info'
        })
    }

    const sendForApproval = async () => {
        const selectedCandidates = candidates.value.filter(c => c.selected)
        
        selectedCandidates.forEach(c => {
            c.status = 'Pending Approval'
        })

        // Simulate approval process
        setTimeout(() => {
            selectedCandidates.forEach(c => {
                c.status = 'Approved'
            })
            emit('candidates-ready', candidates.value)
            Swal.fire('Success', 'Candidates sent for approval and approved', 'success')
        }, 1000)
    }

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        })
    }
</script>
