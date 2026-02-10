<template>
    <Teleport to="body">
        <transition name="modal">
            <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 border-b border-gray-200 shrink-0">
                        <h2 class="text-2xl font-bold text-gray-900">Applicants</h2>
                        <button 
                            @click="closeModal"
                            class="text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="overflow-y-auto flex-1 p-6">
                        <div v-if="applicants.length === 0" class="text-center py-12">
                            <p class="text-gray-600">No applicants yet</p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div 
                                v-for="applicant in applicants" 
                                :key="applicant.id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
                            >
                                <div class="mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ applicant.name }}</h3>
                                    <p class="text-sm text-gray-600">{{ applicant.email }}</p>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4 text-sm">
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase">Applied Date</p>
                                        <p class="text-gray-900 font-medium">{{ formatDate(applicant.appliedDate) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase">Phone</p>
                                        <p class="text-gray-900 font-medium">{{ applicant.phone }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase">Experience</p>
                                        <p class="text-gray-900 font-medium">{{ applicant.experience }} years</p>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-700">{{ applicant.summary }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-3 p-6 border-t border-gray-200 shrink-0 bg-gray-50">
                        <button 
                            @click="closeModal"
                            class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors font-semibold"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
    import { defineProps, defineEmits } from 'vue'
    import Swal from 'sweetalert2'

    const props = defineProps({
        isOpen: {
            type: Boolean,
            default: false
        },
        position: {
            type: String,
            default: 'Senior Engineer'
        }
    })

    const emit = defineEmits(['close', 'update-status', 'reject'])

    const applicants = [
        {
            id: 'APP-001',
            name: 'Maria Santos',
            email: 'maria.santos@email.com',
            phone: '+63 917 123 4567',
            status: 'New',
            appliedDate: '2026-02-01',
            experience: 5,
            rating: 4.5,
            summary: 'Experienced engineer with strong background in full-stack development and cloud architecture. Seeking new challenges.'
        },
        {
            id: 'APP-002',
            name: 'Juan Cruz',
            email: 'juan.cruz@email.com',
            phone: '+63 917 234 5678',
            status: 'Under Review',
            appliedDate: '2026-01-28',
            experience: 7,
            rating: 4.8,
            summary: 'Senior engineer with 7 years of experience in backend systems and microservices architecture. Team lead experience.'
        },
        {
            id: 'APP-003',
            name: 'Angela Reyes',
            email: 'angela.reyes@email.com',
            phone: '+63 917 345 6789',
            status: 'Shortlisted',
            appliedDate: '2026-01-25',
            experience: 6,
            rating: 4.7,
            summary: 'Full-stack engineer with expertise in React, Node.js, and AWS. Proven track record of delivering scalable solutions.'
        },
        {
            id: 'APP-004',
            name: 'Roberto Diaz',
            email: 'roberto.diaz@email.com',
            phone: '+63 917 456 7890',
            status: 'Interview',
            appliedDate: '2026-01-22',
            experience: 8,
            rating: 4.9,
            summary: 'Lead engineer with 8 years of experience managing large teams and driving technical innovations. Strong in DevOps.'
        },
        {
            id: 'APP-005',
            name: 'Carmen Lopez',
            email: 'carmen.lopez@email.com',
            phone: '+63 917 567 8901',
            status: 'Under Review',
            appliedDate: '2026-01-30',
            experience: 4,
            rating: 4.2,
            summary: 'Growing engineer with solid fundamentals in web development. Eager to learn and contribute to challenging projects.'
        }
    ]

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }

    const closeModal = () => {
        emit('close')
    }
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
