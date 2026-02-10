<template>
    <guest-layout>
        <div
            class="w-full h-180 bg-cover bg-no-repeat bg-center"
            :style="{ backgroundImage: `url(${bg1})` }"
        >
            <div class="flex flex-col items-center justify-center h-full">
                <div class="mb-10">
                    <h1 class="text-[#F2F2F2] text-[57px] font-bold text-center">
                        About
                    </h1>
                </div>
                <div class="absolute bottom-0 flex flex-col items-center justify-center text-white">
                    <p class="text-xl font-light mb-2">Scroll for More</p>
                    <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        class="h-8 w-8 animate-bounce" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-[#f7f7f7] py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16 mx-auto">
                    <h2 class="text-4xl font-bold mb-8 text-black text-center">EOSHMA General Construction and Supplies</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        EOSHMA General Construction and Supplies is a trusted leader in delivering high-quality construction solutions 
                        and building materials. With years of experience in residential, commercial, and infrastructure projects, 
                        we combine expertise, reliability, and innovation to bring every project to life efficiently and safely. 
                        Committed to excellence and customer satisfaction, Eoshma provides comprehensive services and quality supplies 
                        that meet the highest industry standards, making us the go-to partner for all your construction needs.
                    </p>
                </div>
                <div class="w-full bg-gray-200 mb-20 overflow-hidden">
                    <img :src="aboutImage" class="w-full h-auto">
                </div>
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-black">Branches</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <img :src="manila" class="w-auto h-auto">
                    <img :src="sorsogon" class="w-auto h-auto">
                    <img :src="laguna" class="w-auto h-auto">
                </div>
            </div>
        </div>
    </guest-layout>
</template>

<script setup>
    import bg1 from '@/assets/landingPageBg1.png'
    import aboutImage from '@/assets/aboutImage.png'
    import manila from '@/assets/branchManila.png'
    import sorsogon from '@/assets/branchSorsogon.png'
    import laguna from '@/assets/branchLaguna.png'

    import { useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'

    const router = useRouter()
    const auth = useAuthStore()

    if (auth.user) {
        router.push('/dashboard')
    }
</script>


<style>
    /* no background-image here — it's set inline so bundler resolves it */
</style>
