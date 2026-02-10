<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                <!-- Back Button & Navigation -->
                <div class="mb-6">
                    <router-link 
                        to="/recruitment"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-[#0c8ce9] hover:bg-blue-50 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Recruitment
                    </router-link>
                </div>

                <!-- Recruitment Wizard Component -->
                <RecruitmentWizard 
                    :requestId="requestId"
                    @close="goBack"
                    @complete="onWizardComplete"
                />
            </div>

            <div class="fixed bottom-0 right-0 p-2 z-50">
                <img :src="agnes" class="h-auto w-auto" v-if="loading" />
            </div>
        </div>
    
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import RecruitmentWizard from '@/components/HR/Recruitment/Wizard/RecruitmentWizard.vue'
    import agnes from '@/assets/agnes.gif'

    const router = useRouter()
    const route = useRoute()
    const auth = useAuthStore()

    const loading = ref(false)
    const requestId = ref(null)

    const goBack = () => {
        router.push('/recruitment')
    }

    const onWizardComplete = () => {
        Swal.fire({
            icon: 'success',
            title: 'Recruitment Complete',
            text: 'Candidate has been processed successfully'
        })
        router.push('/recruitment')
    }

    onMounted(() => {
        requestId.value = route.params.requestId
        auth.pageTitle = `Recruitment Wizard - ${requestId.value}`
    })
</script>
