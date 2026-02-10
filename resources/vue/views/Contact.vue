<template>
    <guest-layout>
        <div
            class="w-full h-180 bg-cover bg-no-repeat bg-center"
            :style="{ backgroundImage: `url(${bg1})` }"
        >
            <div class="flex flex-col items-center justify-center h-full">
                <div class="mb-10">
                    <h1 class="text-[#F2F2F2] text-[57px] font-bold text-center">
                        Contact
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
        <div class="bg-[#f7f7f7] py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-20 items-center">
                <div class="space-y-6 mb-auto">
                    <h2 class="text-4xl font-bold text-gray-900 leading-tight">
                        Contact Us
                    </h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Have a project in mind or need reliable construction services? Get in touch with Eoshma General Construction 
                        and Supplies today. Our team is ready to answer your inquiries, discuss your requirements, 
                        and help bring your project to life with quality and efficiency.
                    </p>
                </div>
                <div class="w-full mx-auto">
                    <form class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">First Name</label>
                                <input type="text" class="w-full px-4 py-2 bg-white text-sm border border-gray-300 rounded-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Last Name</label>
                                <input type="text" class="w-full px-4 py-2 bg-white text-sm border border-gray-300 rounded-sm">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Email</label>
                            <input type="email" class="w-full px-4 py-2 bg-white text-sm border border-gray-300 rounded-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Phone Number</label>
                            <input type="tel" class="w-full px-4 py-2 bg-white text-sm border border-gray-300 rounded-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Message</label>
                            <textarea rows="6" class="w-full px-4 py-2 bg-white text-sm border border-gray-300 rounded-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-[#337ab7] text-white font-semibold rounded-sm text-sm">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </guest-layout>
</template>

<script setup>
    import bg1 from '@/assets/landingPageBg1.png'

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
