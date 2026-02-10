<template>
    <guest-layout>
        <div
            class="w-full h-180 bg-cover bg-no-repeat bg-center"
            :style="{ backgroundImage: `url(${bg1})` }"
        >
            <div class="flex flex-col items-center justify-center h-full">
                <div class="mb-10">
                    <h1 class="text-[#F2F2F2] text-[57px] font-bold text-center">
                        Projects
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
                    <h2 class="text-4xl font-bold mb-8 text-black text-center">List of Projects</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        With a strong foundation in quality workmanship and reliable service, our completed and ongoing projects 
                        showcase how we turn plans into results. Below is a selection of projects that reflect our expertise, 
                        attention to detail, and commitment to delivering lasting value.
                    </p>
                </div>
                <div class="mb-12">
                    <h2 class="text-4xl font-bold text-black">Building A</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-20">
                    <img :src="sampleProject" class="w-auto h-auto">
                    <p class="text-gray-600 text-lg leading-relaxed flex items-center">
                        With a strong foundation in quality workmanship and reliable service, our completed and ongoing projects 
                        showcase how we turn plans into results. Below is a selection of projects that reflect our expertise, 
                        attention to detail, and commitment to delivering lasting value.
                    </p>
                </div>
                <div class="mb-12 text-end">
                    <h2 class="text-4xl font-bold text-black">Building A</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-20">
                    <p class="text-gray-600 text-lg leading-relaxed flex items-center text-end">
                        With a strong foundation in quality workmanship and reliable service, our completed and ongoing projects 
                        showcase how we turn plans into results. Below is a selection of projects that reflect our expertise, 
                        attention to detail, and commitment to delivering lasting value.
                    </p>
                    <img :src="sampleProject" class="w-auto h-auto">
                </div>
                <div class="mb-12">
                    <h2 class="text-4xl font-bold text-black">Building A</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-15">
                    <img :src="sampleProject" class="w-auto h-auto">
                    <p class="text-gray-600 text-lg leading-relaxed flex items-center">
                        With a strong foundation in quality workmanship and reliable service, our completed and ongoing projects 
                        showcase how we turn plans into results. Below is a selection of projects that reflect our expertise, 
                        attention to detail, and commitment to delivering lasting value.
                    </p>
                </div>
            </div>
        </div>
    </guest-layout>
</template>

<script setup>
    import bg1 from '@/assets/landingPageBg1.png'
    import sampleProject from '@/assets/sampleProject.png'

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
