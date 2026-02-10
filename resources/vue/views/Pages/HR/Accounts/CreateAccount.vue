<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">
                <nav class="flex items-center gap-3 mb-8">
                            <template v-for="(step, index) in steps" :key="index">
                                <button
                                    @click="goToStep(index)"
                                    class="flex items-center gap-2 text-base font-bold border-b-2 transition"
                                    :class="index === activeStep ? 'text-[#111827] border-[#1F2937]' : 'text-[#B9BABC] border-transparent'"
                                >
                                    <img
                                        :src="getStepIcon(step, index)"
                                        :class="['h-4 w-4 transition-opacity', getIconClass(index)]"
                                        alt=""
                                    />
                                    {{ step.label }}
                                </button>
                                <svg
                                    v-if="index < steps.length - 1"
                                    height="2"
                                    width="10"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <line x1="0" y1="1" x2="40" y2="1" stroke="#B9BABC" stroke-width="2" />
                                </svg>
                            </template>
                        </nav>  
                        <div class="flex items-center justify-between gap-4 ms-auto"> 
                            <router-link 
                                to="/accounts"
                                class="flex items-center gap-2 px-5 py-1 text-sm hover:bg-[#0C8CE9] hover:text-white border border-[#0C8CE9] text-[#0C8CE9] rounded-sm cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Accounts
                            </router-link>
                            <button 
                                class="flex items-center gap-2 px-5 py-1 text-sm bg-[#0C8CE9] text-white rounded-sm cursor-pointer"
                                @click="nextStep"
                                :hidden="activeStep === 1"
                            >
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>  
                            <button
                                v-if="activeStep === 1"
                                @click="handleSubmit"
                                :disabled="loading"
                                class="flex items-center gap-2 px-5 py-1 text-sm bg-[#0C8CE9] text-white rounded-sm cursor-pointer"
                            >
                                Create Account
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="px-11 min-h-screen mt-5">
                        <div class="flex" v-if="activeStep === 0">
                            <div class="grid grid-cols-2 gap-x-20 gap-y-5 w-full pb-5 mb-5">
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-[#333333] mb-2">User Picture</label>
                                        <input 
                                            type="file" 
                                            ref="fileInput" 
                                            class="hidden" 
                                            accept="image/*" 
                                            @change="handleImageUpload"
                                        />
                                        <div 
                                            @click="$refs.fileInput.click()"
                                            class="relative border border-gray-300 rounded-sm h-50 flex flex-col items-center justify-center bg-white cursor-pointer overflow-hidden w-60"
                                        >
                                            <template v-if="previewUrl">
                                                <img :src="previewUrl" class="absolute inset-0 w-full h-full object-cover" />
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <span class="text-white text-sm font-bold">Change Image</span>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-[#B9BABC] font-medium">Upload file</span>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-[#333333] mb-2">Account Name</label>
                                        <div class="flex gap-x-2">
                                            <input 
                                                type="text" 
                                                v-model="form1.last_name"
                                                placeholder="Last Name"
                                                class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                            />
                                            <input 
                                                type="text" 
                                                v-model="form1.first_name"
                                                placeholder="First Name"
                                                class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                            />
                                            <input 
                                                type="text" 
                                                v-model="form1.middle_initial"
                                                maxlength="1"
                                                placeholder="Middle Initial"
                                                class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-[#333333] mb-2">Phone Number</label>
                                        <input 
                                            type="text" 
                                            v-model="form1.phone"
                                            @input="handlePhoneInput" 
                                            placeholder="e.g. 09123456789"
                                            class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-[#333333] mb-2">Email Address</label>
                                        <input 
                                            type="email" 
                                            v-model="form1.email"
                                            placeholder="e.g. user@example.com"
                                            class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col" v-if="activeStep === 1">
                            <div class="grid grid-cols-2 gap-x-20 gap-y-5 w-full mb-5">
                                <div>
                                    <label class="block text-sm font-semibold text-[#333333] mb-2">Deparment</label>
                                    <select 
                                        v-model="form2.department"
                                        class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                    >
                                        <option value="" disabled hidden></option>
                                        <option value="" disabled>Select a department</option>
                                        <option value="hrm">Human Resource</option>
                                        <option value="crm">Client Relationship</option>
                                        <option value="frm">Finance</option>
                                        <option value="scm">Supply Chain</option>
                                        <option value="mrp">Procurement</option>
                                        <option value="om">Operations</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-[#333333] mb-2">Role</label>
                                    <select 
                                        v-model="form2.role"
                                        class="w-full px-4 py-2 border border-gray-300 bg-white rounded-sm outline-none text-sm"
                                    >   
                                        <option value="" disabled hidden></option>
                                        <option value="" disabled>
                                            {{ form2.department ? 'Select a role' : 'Select a department first' }}
                                        </option>
                                        <option 
                                            v-for="role in roles[form2.department]" 
                                            :key="role.value"
                                            :value="role.value"
                                        >
                                            {{ role.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="font-semibold text-[#333333] mb-3">Account Permissions</div>
                            <div class="max-w-md mb-5 pb-5">
                                <div class="grid grid-cols-12 mb-2 font-semibold text-[#333333] text-sm">
                                    <div class="col-span-8"></div>
                                    <div class="col-span-2 text-center">View</div>
                                    <div class="col-span-2 text-center">Manage</div>
                                </div>
                                <div v-for="(dept, index) in departments" :key="index" class="mb-5">
                                    <div class="grid grid-cols-12 items-center py-2 border-b border-gray-300">
                                        <div class="col-span-8 font-bold text-[#333333]">
                                            {{ dept.name }}
                                        </div>
                                        <div class="col-span-2 flex justify-center">
                                            <input 
                                                type="checkbox" 
                                                :checked="isAllSelected(index, 'view')"
                                                :disabled="isDeptLocked(index, 'view')"
                                                @change="toggleAll(index, 'view', $event.target.checked)"
                                                class="w-5 h-5 rounded border-gray-300"
                                            />
                                        </div>
                                        <div class="col-span-2 flex justify-center">
                                            <input 
                                                type="checkbox" 
                                                :checked="isAllSelected(index, 'manage')"
                                                :disabled="isDeptLocked(index, 'manage')"
                                                @change="toggleAll(index, 'manage', $event.target.checked)"
                                                class="w-5 h-5 rounded border-gray-300"
                                            />
                                        </div>
                                    </div>
                                    <div 
                                        v-for="page in dept.pages" 
                                        :key="page.name" 
                                        class="grid grid-cols-12 items-center py-2"
                                    >
                                        <div class="col-span-8 ml-8 text-[#333333] text-sm">
                                            {{ page.name }}
                                        </div>
                                        <div class="col-span-2 flex justify-center">
                                            <input 
                                                type="checkbox" 
                                                v-model="page.view"
                                                :disabled="page.lockView"
                                                class="w-5 h-5 rounded border-gray-300"
                                            />
                                        </div>
                                        <div class="col-span-2 flex justify-center">
                                            <input 
                                                type="checkbox" 
                                                :disabled="page.lockManage"
                                                @change="syncView(page)"
                                                v-model="page.manage"
                                                class="w-5 h-5 rounded border-gray-300"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, onMounted, watch } from 'vue'
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

    /* =====================================================
    File Upload State
    ===================================================== */
    const previewUrl = ref(null)
    const selectedFile = ref(null)

    /* =====================================================
    Form State
    ===================================================== */
    const form1 = ref({
        last_name: '',
        first_name: '',
        middle_initial: '',
        phone: '',
        email: '',
    })

    const form2 = ref({
        department: '',
        role: '',
    })

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
            timer,
            customClass: { popup: 'no-wrap-toast' }
        })
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

    const handlePhoneInput = (e) => {
        form1.value.phone = e.target.value.replace(/\D/g, '')
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

    /* =====================================================
    Validation Helpers
    ===================================================== */
    const checkRequiredFields = () => {
        const requiredFields = {
            last_name: 'Last Name',
            first_name: 'First Name',
            phone: 'Phone Number',
            email: 'Email',
        }

        for (const [key, label] of Object.entries(requiredFields)) {
            if (!form1.value[key]?.toString().trim()) {
                showToast({
                    icon: 'warning',
                    html: `Please enter the <b>${label}</b>`
                })
                return true
            }
        }

        if (!/^09\d{9}$/.test(form1.value.phone)) {
            showToast({
                icon: 'warning',
                html: 'Please enter a valid phone number'
            })
            return true
        }

        if (!emailRegex.test(form1.value.email)) {
            showToast({
                icon: 'warning',
                html: 'Please enter a valid email address'
            })
            return true
        }

        return false
    }

    /* =====================================================
    Submit Logic
    ===================================================== */
    const handleSubmit = async () => {
        const requiredFields = {
            department: 'Department',
            role: 'Role',
        }

        for (const [key, label] of Object.entries(requiredFields)) {
            if (!form2.value[key]?.toString().trim()) {
                showToast({
                    icon: 'warning',
                    html: `Please enter the <b>${label}</b>`
                })
                return
            }
        }

        loading.value = true

        const formData = new FormData()

        if (selectedFile.value) {
            formData.append('image', selectedFile.value)
        }

        Object.entries(form1.value).forEach(([key, value]) => {
            formData.append(key, value || '')
        })

        Object.entries(form2.value).forEach(([key, value]) => {
            formData.append(key, value || '')
        })

        const selectedPermissions = []

        departments.value.forEach(dept => {
            dept.pages.forEach(page => {
                if (page.view) {
                    selectedPermissions.push({ page: page.name, type: 'view' })
                }
                if (page.manage) {
                    selectedPermissions.push({ page: page.name, type: 'manage' })
                }
            })
        })

        formData.append('permissions', JSON.stringify(selectedPermissions))

        try {
            await axios.post('/api/users', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })

            showToast({
                icon: 'success',
                html: 'User created successfully.'
            })

            router.push('/accounts')

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
                    html: 'An unexpected server error occurred.'
                })
                console.error(err)
            }
        } finally {
            loading.value = false
        }
    }

    /* =====================================================
    Roles Configuration
    ===================================================== */
    const roles = {
        hrm: [
            { value: "hr_manager", label: "HR Manager" },
            // { value: "recruitment_officer", label: "Recruitment Officer" },
            // { value: "training_officer", label: "Training & Development Officer" },
            { value: "payroll", label: "Payroll Officer" },
            { value: "employee_relations", label: "Employee Relations" },
            // { value: "safety_officer", label: "Health & Safety Officer" }
        ],
        crm: [
            { value: "cr_manager", label: "Client Relationship Manager" },
            // { value: "sales_manager", label: "Sales Manager" },
            // { value: "business_development_officer", label: "Business Development Officer" },
            // { value: "customer_support_officer", label: "Customer Support Officer" },
            // { value: "contract_administrator", label: "Contract Administrator" },
            // { value: "project_liaison_officer", label: "Project Liaison Officer" }
        ],
        frm: [
            { value: "finance_manager", label: "Finance Manager" },
            { value: "accountant", label: "Accountant" },
            // { value: "project_accountant", label: "Project Accountant" },
            // { value: "cost_controller", label: "Cost Controller" },
            // { value: "budget_analyst", label: "Budget Analyst" },
            // { value: "finance_officer", label: "Finance Officer" }
        ],
        scm: [
            { value: "supply_chain_manager", label: "Supply Chain Manager" },
            // { value: "logistics_manager", label: "Logistics Manager" },
            { value: "inventory_manager", label: "Inventory Manager" },
            // { value: "warehouse_supervisor", label: "Warehouse Supervisor" },
            // { value: "transport_coordinator", label: "Transport Coordinator" },
            { value: "supplier_coordinator", label: "Supplier Coordinator" }
        ],
        mrp: [
            { value: "procurement_manager", label: "Procurement Manager" },
            // { value: "purchasing_officer", label: "Purchasing Officer" },
            // { value: "buyer", label: "Buyer" },
            // { value: "material_planner", label: "Material Planner" },
            // { value: "contract_officer", label: "Contract Officer" },
            // { value: "quality_control_officer", label: "Quality Control Officer" }
        ],
        om: [
            { value: "operations_manager", label: "Operations Manager" },
            { value: "project_manager", label: "Project Manager" },
            // { value: "site_engineer", label: "Site Engineer" },
            { value: "site_supervisor", label: "Site Supervisor" },
            { value: "foreman", label: "Foreman" },
            // { value: "planning_engineer", label: "Planning Engineer" },
            // { value: "qa_qc_engineer", label: "QA/QC Engineer" },
            // { value: "equipment_manager", label: "Equipment Manager" }
        ]
    };

    /* =====================================================
    Stepper Configuration
    ===================================================== */
    const steps = [
        {
            label: 'Employee Details',
            icons: { circle: circleFill, check: circleCheckFill }
        },
        {
            label: 'Account Information',
            icons: { circle: circleFill, check: circleCheckFill }
        }
    ]

    const activeStep = ref(0)
    const maxStepReached = ref(0)

    const goToStep = (index) => {
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

    /* =====================================================
    Department & Permission Management
    ===================================================== */
    const departments = ref([
        {
            name: 'Human Resources Management',
            pages: [
                { name: 'Accounts', view: false, manage: false, lockView: false, lockManage: false }
            ]
        },
        {
            name: 'Client Relationship Management',
            pages: [
                { name: 'Clients', view: false, manage: false, lockView: false, lockManage: false },
                { name: 'Prebiddings', view: false, manage: false, lockView: false, lockManage: false }
            ]
        },
        {
            name: 'Finance Resources Management',
            pages: [
                { name: 'Accounting', view: false, manage: false, lockView: false, lockManage: false },
                { name: 'Project Financials', view: false, manage: false, lockView: false, lockManage: false }
            ]
        },
        {
            name: 'Supply Chain Management',
            pages: [
                { name: 'Inventory', view: false, manage: false, lockView: false, lockManage: false },
                { name: 'Suppliers', view: false, manage: false, lockView: false, lockManage: false }
            ]
        },
        {
            name: 'Material Request and Procurement',
            pages: [
                { name: 'Procurement', view: false, manage: false, lockView: false, lockManage: false }
            ]
        },
        {
            name: 'Operations',
            pages: [
                { name: 'Projects', view: false, manage: false, lockView: false, lockManage: false }
            ]
        },
    ])

    const isAllSelected = (deptIndex, key) => {
        return departments.value[deptIndex].pages.every(page => page[key])
    }

    const toggleAll = (deptIndex, key, checked) => {
        departments.value[deptIndex].pages.forEach(page => {
            page[key] = checked
            if (key === 'manage' && checked) page.view = true
        })
    }

    const syncView = (page) => {
        if (page.manage) page.view = true
    }

    /* =====================================================
    Roles & Permission Management
    ===================================================== */
    const rolePermissionRules = {
        hr_manager: {
            dept: 'Human Resources Management',
            pages: 'ALL'
        },
        cr_manager: {
            dept: 'Client Relationship Management',
            pages: 'ALL'
        },
        finance_manager: {
            dept: 'Finance Resources Management',
            pages: 'ALL'
        },
        accountant: {
            dept: 'Finance Resources Management',
            pages: ['Accounting', 'Project Financials']
        },
        supply_chain_manager: {
            dept: 'Supply Chain Management',
            pages: 'ALL'
        },
        inventory_manager: {
            dept: 'Supply Chain Management',
            pages: ['Inventory']
        },
        supplier_coordinator: {
            dept: 'Supply Chain Management',
            pages: ['Suppliers']
        },
        procurement_manager: {
            dept: 'Material Request and Procurement',
            pages: 'ALL'
        },
        operations_manager: {
            dept: 'Operations',
            pages: 'ALL'
        },
        project_manager: {
            dept: 'Operations',
            pages: ['Projects'],
            manage: true
        },
        site_supervisor: {
            dept: 'Operations',
            pages: ['Projects'],
            manage: false
        },
        foreman: {
            dept: 'Operations',
            pages: ['Projects'],
            manage: false
        }
    }

    /* =====================================================
    Watchers
    ===================================================== */
    watch(() => form2.value.role, () => {
        // Reset everything first
        departments.value.forEach(dept => {
            dept.pages.forEach(page => {
                page.view = false
                page.manage = false
                page.lockView = false
                page.lockManage = false
            })
        })

        const rule = rolePermissionRules[form2.value.role]
        if (!rule) return

        const dept = departments.value.find(d => d.name === rule.dept)
        if (!dept) return

        dept.pages.forEach(page => {
            const applies =
                rule.pages === 'ALL' || rule.pages.includes(page.name)

            if (applies) {
                page.view = true
                page.lockView = true

                if (rule.manage !== false) {
                    page.manage = true
                    page.lockManage = true
                }
            }
        })
    })

    const isDeptLocked = (deptIndex, type) => {
        return departments.value[deptIndex].pages.some(page =>
            type === 'view' ? page.lockView : page.lockManage
        )
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // init logic if needed
    })
</script>

<style>
    .no-wrap-toast {
        white-space: nowrap !important;
        overflow: hidden;
    }
</style>