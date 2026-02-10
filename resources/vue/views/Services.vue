<template>
    <guest-layout>
        <div
            class="w-full h-180 bg-cover bg-no-repeat bg-center"
            :style="{ backgroundImage: `url(${bg1})` }"
        >
            <div class="flex flex-col items-center justify-center h-full">
                <div class="mb-10">
                    <h1 class="text-[#F2F2F2] text-[57px] font-bold text-center">
                        Services
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
                    <h2 class="text-4xl font-bold mb-8 text-black text-center">What we offer</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        At Eoshma General Construction and Supplies, we provide a full range of construction services 
                        to meet your needs. From building and renovating residential or commercial projects to road works 
                        and infrastructure, we combine expertise and quality materials to deliver reliable results. 
                        Our team also handles project management, site preparation, and material supply, 
                        ensuring every project runs smoothly from start to finish.
                    </p>
                </div>
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-black">Services</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <img :src="service1" class="w-auto h-auto">
                    <img :src="service2" class="w-auto h-auto">
                    <img :src="service3" class="w-auto h-auto">
                    <img :src="service4" class="w-auto h-auto">
                    <img :src="service5" class="w-auto h-auto">
                    <img :src="service6" class="w-auto h-auto">
                    <img :src="service7" class="w-auto h-auto">
                    <img :src="service8" class="w-auto h-auto">
                    <img :src="service1" class="w-auto h-auto">
                </div>
            </div>
        </div>
    </guest-layout>
</template>

<script setup>
    import bg1 from '@/assets/landingPageBg1.png'
    import service1 from '@/assets/Service1.png'
    import service2 from '@/assets/Service2.png'
    import service3 from '@/assets/Service3.png'
    import service4 from '@/assets/Service4.png'
    import service5 from '@/assets/Service5.png'
    import service6 from '@/assets/Service6.png'
    import service7 from '@/assets/Service7.png'
    import service8 from '@/assets/Service8.png'


    import { useRouter } from "vue-router"
    import { useAuthStore } from "@/stores/auth"

    const router = useRouter()
    const auth = useAuthStore()

    if (auth.user) {
        router.push('/dashboard')
    }
</script>

<style>
    /* no background-image here — it's set inline so bundler resolves it */
</style>
