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

                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create New Client Record</h1>
                        <p class="text-sm text-gray-600 mt-1">Step {{ currentStep }} of 3 — {{ stepTitles[currentStep - 1] }}</p>
                    </div>
                    <button 
                        @click="submitStep"
                        :disabled="isSubmitting"
                        class="px-6 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-all disabled:opacity-50"
                    >
                        <span v-if="!isSubmitting && currentStep < 3">Next</span>
                        <span v-else-if="!isSubmitting && currentStep === 3">Create Client Record</span>
                        <span v-else>Processing...</span>
                    </button>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div v-for="step in 3" :key="step" class="flex items-center flex-1">
                        <div 
                            :class="[
                                'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all',
                                step < currentStep ? 'bg-green-500 text-white' : 
                                step === currentStep ? 'bg-[#0c8ce9] text-white' : 
                                'bg-gray-300 text-gray-600'
                            ]"
                        >
                            <span v-if="step < currentStep">✓</span>
                            <span v-else>{{ step }}</span>
                        </div>
                        <span :class="['text-sm font-semibold ml-3', step <= currentStep ? 'text-gray-900' : 'text-gray-500']">
                            {{ stepTitles[step - 1] }}
                        </span>
                        <div v-if="step < 3" :class="['flex-1 h-1 mx-4 rounded', step < currentStep ? 'bg-green-500' : 'bg-gray-300']"></div>
                    </div>
                </div>
            </div>

            <!-- Form Steps -->
            <form @submit.prevent="submitStep" class="space-y-6">
                <!-- Step 1: Legal Identity and Registration -->
                <div v-show="currentStep === 1" class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 animate-in">
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Legal Identity & Registration</h2>
                        <p class="text-sm text-gray-600 mt-2">Provide the complete legal information for this client</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Legal Company Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Legal Company Name <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.legal_name"
                                type="text"
                                placeholder="Enter full legal company name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                                @blur="validateUniqueLegalName"
                            />
                            <span v-if="errors.legal_name" class="text-red-600 text-xs mt-1">{{ errors.legal_name }}</span>
                        </div>

                        <!-- Trade Name (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Trade Name</label>
                            <input 
                                v-model="formData.trade_name"
                                type="text"
                                placeholder="Enter trade name (optional)"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                                @blur="validateUniqueTradeName"
                            />
                            <span v-if="errors.trade_name" class="text-red-600 text-xs mt-1">{{ errors.trade_name }}</span>
                        </div>

                        <!-- Client Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Client Type <span class="text-red-600">*</span>
                            </label>
                            <select 
                                v-model="formData.client_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            >
                                <option value="">Select a type...</option>
                                <option value="Private">Private</option>
                                <option value="Corporate">Corporate</option>
                                <option value="Government">Government</option>
                            </select>
                            <span v-if="errors.client_type" class="text-red-600 text-xs mt-1">{{ errors.client_type }}</span>
                        </div>

                        <!-- Tax Identification Number -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Tax Identification Number (TIN) <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.tin"
                                type="text"
                                placeholder="e.g., 001-234-567-890"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                                @blur="validateUniqueTIN"
                            />
                            <span v-if="errors.tin" class="text-red-600 text-xs mt-1">{{ errors.tin }}</span>
                        </div>

                        <!-- Business Registration Number -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Business Registration Number <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.registration_number"
                                type="text"
                                placeholder="Enter registration number"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.registration_number" class="text-red-600 text-xs mt-1">{{ errors.registration_number }}</span>
                        </div>

                        <!-- Jurisdiction -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Jurisdiction <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.jurisdiction"
                                type="text"
                                placeholder="e.g., Republic of the Philippines"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.jurisdiction" class="text-red-600 text-xs mt-1">{{ errors.jurisdiction }}</span>
                        </div>

                        <!-- Registered Address (Full Width) -->
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Registered Address <span class="text-red-600">*</span>
                            </label>
                            <textarea 
                                v-model="formData.registered_address"
                                placeholder="Enter complete registered address"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            ></textarea>
                            <span v-if="errors.registered_address" class="text-red-600 text-xs mt-1">{{ errors.registered_address }}</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Business Profile -->
                <div v-show="currentStep === 2" class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 animate-in">
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Business Profile</h2>
                        <p class="text-sm text-gray-600 mt-2">Define the business characteristics and category</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Industry -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Industry <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.industry"
                                type="text"
                                placeholder="e.g., Construction, Manufacturing, Services"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.industry" class="text-red-600 text-xs mt-1">{{ errors.industry }}</span>
                        </div>

                        <!-- Client Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Client Category <span class="text-red-600">*</span>
                            </label>
                            <select 
                                v-model="formData.client_category"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            >
                                <option value="">Select a category...</option>
                                <option value="One-time">One-time Project</option>
                                <option value="Repeat">Repeat Client</option>
                                <option value="Strategic">Strategic Partner</option>
                            </select>
                            <span v-if="errors.client_category" class="text-red-600 text-xs mt-1">{{ errors.client_category }}</span>
                        </div>

                        <!-- Internal Notes (Full Width) -->
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Internal Notes</label>
                            <textarea 
                                v-model="formData.internal_notes"
                                placeholder="Add any internal notes or context about this client (optional)"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Primary Contact Person -->
                <div v-show="currentStep === 3" class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 animate-in">
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Primary Contact Person</h2>
                        <p class="text-sm text-gray-600 mt-2">At least one primary contact is required to create a client record</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Contact Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Contact Name <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.contact_name"
                                type="text"
                                placeholder="Full name of primary contact"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.contact_name" class="text-red-600 text-xs mt-1">{{ errors.contact_name }}</span>
                        </div>

                        <!-- Contact Role -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Contact Role <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.contact_role"
                                type="text"
                                placeholder="e.g., Project Manager, Owner, Director"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.contact_role" class="text-red-600 text-xs mt-1">{{ errors.contact_role }}</span>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Phone Number <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.contact_phone"
                                type="tel"
                                placeholder="e.g., +63 2 1234 5678"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.contact_phone" class="text-red-600 text-xs mt-1">{{ errors.contact_phone }}</span>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Email Address <span class="text-red-600">*</span>
                            </label>
                            <input 
                                v-model="formData.contact_email"
                                type="email"
                                placeholder="e.g., contact@company.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                            <span v-if="errors.contact_email" class="text-red-600 text-xs mt-1">{{ errors.contact_email }}</span>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Review Client Information</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1 uppercase">Company</p>
                                <p class="text-sm font-semibold text-gray-900">{{ formData.legal_name || 'Not provided' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1 uppercase">Contact</p>
                                <p class="text-sm font-semibold text-gray-900">{{ formData.contact_name || 'Not provided' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1 uppercase">Email</p>
                                <p class="text-sm font-semibold text-gray-900">{{ formData.contact_email || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between gap-4 pt-8">
                    <button 
                        v-if="currentStep > 1"
                        type="button"
                        @click="previousStep"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all"
                    >
                        Previous
                    </button>
                    <div v-else></div>
                    
                    <router-link 
                        to="/clients"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all"
                    >
                        Cancel
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Step Configuration
const currentStep = ref(1)
const stepTitles = [
    'Legal Identity & Registration',
    'Business Profile',
    'Primary Contact Person'
]

// Form Data
const formData = ref({
    // Legal Identity
    legal_name: '',
    trade_name: '',
    client_type: '',
    tin: '',
    registration_number: '',
    registered_address: '',
    jurisdiction: '',
    
    // Business Profile
    industry: '',
    client_category: '',
    internal_notes: '',
    
    // Primary Contact
    contact_name: '',
    contact_role: '',
    contact_phone: '',
    contact_email: ''
})

// Mock existing clients for uniqueness validation
const existingClients = ref([
    { legal_name: 'Acme Corporation', trade_name: 'Acme Corp', tin: '001-234-567-890' },
    { legal_name: 'BuildTech Solutions', trade_name: 'BuildTech', tin: '002-345-678-901' },
    { legal_name: 'City Infrastructure Development', trade_name: 'CID', tin: '003-456-789-012' },
])

const errors = ref({})
const isSubmitting = ref(false)

// Validation Methods
const validateUniqueLegalName = () => {
    if (!formData.value.legal_name) {
        errors.value.legal_name = 'Legal company name is required'
        return false
    }
    
    const isDuplicate = existingClients.value.some(
        client => client.legal_name.toLowerCase() === formData.value.legal_name.toLowerCase()
    )
    
    if (isDuplicate) {
        errors.value.legal_name = 'This legal company name already exists in the system'
        return false
    }
    
    delete errors.value.legal_name
    return true
}

const validateUniqueTradeName = () => {
    if (!formData.value.trade_name) return true // Optional field
    
    const isDuplicate = existingClients.value.some(
        client => client.trade_name && client.trade_name.toLowerCase() === formData.value.trade_name.toLowerCase()
    )
    
    if (isDuplicate) {
        errors.value.trade_name = 'This trade name already exists in the system'
        return false
    }
    
    delete errors.value.trade_name
    return true
}

const validateUniqueTIN = () => {
    if (!formData.value.tin) {
        errors.value.tin = 'TIN is required'
        return false
    }
    
    const isDuplicate = existingClients.value.some(
        client => client.tin === formData.value.tin
    )
    
    if (isDuplicate) {
        errors.value.tin = 'This TIN already exists in the system'
        return false
    }
    
    delete errors.value.tin
    return true
}

const validateStep1 = () => {
    errors.value = {}
    let isValid = true
    
    if (!validateUniqueLegalName()) isValid = false
    if (formData.value.trade_name && !validateUniqueTradeName()) isValid = false
    if (!formData.value.client_type) {
        errors.value.client_type = 'Client type is required'
        isValid = false
    }
    if (!validateUniqueTIN()) isValid = false
    if (!formData.value.registration_number) {
        errors.value.registration_number = 'Business registration number is required'
        isValid = false
    }
    if (!formData.value.registered_address) {
        errors.value.registered_address = 'Registered address is required'
        isValid = false
    }
    if (!formData.value.jurisdiction) {
        errors.value.jurisdiction = 'Jurisdiction is required'
        isValid = false
    }
    
    return isValid
}

const validateStep2 = () => {
    errors.value = {}
    let isValid = true
    
    if (!formData.value.industry) {
        errors.value.industry = 'Industry is required'
        isValid = false
    }
    if (!formData.value.client_category) {
        errors.value.client_category = 'Client category is required'
        isValid = false
    }
    
    return isValid
}

const validateStep3 = () => {
    errors.value = {}
    let isValid = true
    
    if (!formData.value.contact_name) {
        errors.value.contact_name = 'Contact name is required'
        isValid = false
    }
    if (!formData.value.contact_role) {
        errors.value.contact_role = 'Contact role is required'
        isValid = false
    }
    if (!formData.value.contact_phone) {
        errors.value.contact_phone = 'Phone number is required'
        isValid = false
    }
    if (!formData.value.contact_email) {
        errors.value.contact_email = 'Email address is required'
        isValid = false
    } else if (!isValidEmail(formData.value.contact_email)) {
        errors.value.contact_email = 'Please enter a valid email address'
        isValid = false
    }
    
    return isValid
}

const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
}

const submitStep = async () => {
    // Validate current step
    if (currentStep.value === 1 && !validateStep1()) return
    if (currentStep.value === 2 && !validateStep2()) return
    if (currentStep.value === 3 && !validateStep3()) return
    
    // Move to next step or submit
    if (currentStep.value < 3) {
        currentStep.value++
    } else {
        await submitForm()
    }
}

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
        errors.value = {}
    }
}

const submitForm = async () => {
    isSubmitting.value = true
    
    try {
        // Simulate API call - Replace with actual API endpoint
        console.log('Submitting client creation form:', formData.value)
        
        // Mock API call with delay
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // On success, navigate back to clients list
        // In production, this would be: const response = await axios.post('/api/clients', formData.value)
        // Then handle response and redirect
        
        alert('Client record created successfully! Client ID will be assigned by the system.')
        await router.push('/clients')
    } catch (error) {
        console.error('Error creating client:', error)
        errors.value.submit = 'Failed to create client record. Please try again.'
    } finally {
        isSubmitting.value = false
    }
}
</script>

<style scoped>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: slideIn 0.3s ease-out;
}
</style>
