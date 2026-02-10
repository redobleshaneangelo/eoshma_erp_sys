<template>
    <guest-layout>
        <div
            class="w-full h-180 bg-cover bg-no-repeat bg-center"
            :style="{ backgroundImage: `url(${bg1})` }"
        >
            <div class="flex flex-col items-center justify-center h-full">
                <div class="mb-10">
                    <h1 class="text-[#F2F2F2] text-[57px] font-bold text-center">
                        Building Strong Foundations for Every Project
                    </h1>
                    <p class="text-[#F2F2F2] text-xl mt-3 text-center px-40 font-light">
                        EOSHMA General Construction and Supplies delivers reliable construction services and quality materials for residential, 
                        commercial, and industrial projects. We are committed to excellence, safety, and timely project completion.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-4">
                    <router-link
                        to="/contact"
                        class="px-4 py-2 bg-[#337ab7] text-white rounded-sm shadow-md"
                    >
                        Contact Us
                    </router-link>
                    <router-link
                        to="/"
                        class="px-4 py-2 text-white rounded-sm shadow-md border-white border"
                    >
                        Learn More
                    </router-link>
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
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold text-gray-900 leading-tight">
                        Built on Trust and Experience
                    </h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        EOSHMA General Construction and Supplies delivers reliable construction services 
                        and quality materials for residential, commercial, and industrial projects. 
                        We are committed to excellence, safety, and timely project completion.
                    </p>
                    <div>
                        <router-link
                            to="/about"
                            class="inline-block px-6 py-3 bg-[#337ab7] text-white font-medium rounded-sm shadow-sm"
                        >
                            Read More
                        </router-link>
                    </div>
                </div>
                <div 
                    class="w-full h-100 bg-cover bg-no-repeat bg-center overflow-hidden"
                    :style="{ backgroundImage: `url(${handshake})` }"
                >
                </div>
            </div>
        </div>
        <div 
            class="w-full bg-cover bg-no-repeat bg-center py-30"
            :style="{ backgroundImage: `url(${bg2})` }"
        >
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
                <div class="grid grid-cols-2 gap-5">
                    <img :src="service1" class="w-auto h-auto">
                    <img :src="service2" class="w-auto h-auto">
                    <img :src="service3" class="w-auto h-auto">
                    <img :src="service4" class="w-auto h-auto">
                    <img :src="service5" class="w-auto h-auto">
                    <img :src="service6" class="w-auto h-auto">
                    <img :src="service7" class="w-auto h-auto">
                    <img :src="service8" class="w-auto h-auto">
                </div>
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold text-white leading-tight text-end">
                        What We Offer
                    </h2>
                    <p class="text-[#b8c1c8] text-lg leading-relaxed text-end">
                        EOSHMA General Construction and Supplies delivers reliable construction services 
                        and quality materials for residential, commercial, and industrial projects. 
                        We are committed to excellence, safety, and timely project completion.
                    </p>
                    <div class="flex item-center justify-end">
                        <router-link
                            to="/services"
                            class="inline-block px-6 py-3 border border-white text-white font-medium rounded-sm shadow-sm"
                        >
                            See Services
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-[#f8f9fa] py-20 px-4 w-full">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold text-center mb-16 text-black">Why Choose Us</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 text-center">
                    <div class="flex flex-col items-center">
                        <h3 class="text-lg font-bold mb-4">10 Years of Experience</h3>
                        <span class="text-6xl font-bold text-[#004a99] mb-4">10</span>
                        <p class="text-gray-600 leading-relaxed max-w-62.5">
                            Proven expertise in delivering reliable construction and supply solutions across various project types.
                        </p>
                    </div>
                    <div class="flex flex-col items-center">
                        <h3 class="text-lg font-bold mb-4">63 Projects Completed</h3>
                        <span class="text-6xl font-bold text-[#004a99] mb-4">63</span>
                        <p class="text-gray-600 leading-relaxed max-w-62.5">
                            Successfully completed residential, commercial, and infrastructure projects with consistent quality and performance.
                        </p>
                    </div>
                    <div class="flex flex-col items-center">
                        <h3 class="text-lg font-bold mb-4">95% On-Time Completion Rate</h3>
                        <span class="text-6xl font-bold text-[#004a99] mb-4">95%</span>
                        <p class="text-gray-600 leading-relaxed max-w-62.5">
                            Strong project coordination and planning ensure projects are delivered within schedule and scope.
                        </p>
                    </div>
                    <div class="flex flex-col items-center">
                        <h3 class="text-lg font-bold mb-4">Trusted by Multiple Clients</h3>
                        <span class="text-6xl font-bold text-[#004a99] mb-4">12+</span>
                        <p class="text-gray-600 leading-relaxed max-w-62.5">
                            Built long-term partnerships through quality workmanship, dependable materials, and client-focused service.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-[#004D8F] py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between space-x-50">
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold text-white leading-tight">
                        Get in Contact
                    </h2>
                    <p class="text-[#b2cadd] text-lg leading-relaxed">
                        Get in touch with Eoshma General Construction and Supplies to discuss your project needs or request a quotation. 
                        Our team is ready to assist you with reliable construction services and quality supply solutions.
                    </p>
                    <div>
                        <router-link
                            to="/contact"
                            class="inline-block px-6 py-3 border border-white text-white font-medium rounded-sm shadow-sm"
                        >
                            Contact Us
                        </router-link>
                    </div>
                </div>
                <img :src="eoshmaCircle" class="w-auto h-80">
            </div>
        </div>
        <div class="bg-[#f7f7f7] py-30 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-16">
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold text-gray-900">Our Vision</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        To be the builder clients, employees, stakeholders and the community can trust and value 
                        while continuously developing to be a recognized competitive industry player.
                    </p>
                </div>
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold text-gray-900">Our Mission</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        To provide quality workmanship and satisfactory services to our clients while maintaining the highest degree 
                        of professionalism, competence, and integrity. To develop innovative technological solutions through the products 
                        we introduce to the market in order to promote public safety and convenience. To continuously grow with 
                        and for our clients, personnel, suppliers, subcontractors and professional associates and work together 
                        not just in building structures but in building the nation.
                    </p>
                </div>
            </div>
        </div>
    </guest-layout>
</template>

<script setup>
    import bg1 from '@/assets/landingPageBg1.png'
    import bg2 from '@/assets/landingPageBg2.png'
    import handshake from '@/assets/handshake.png'
    import service1 from '@/assets/Service1.png'
    import service2 from '@/assets/Service2.png'
    import service3 from '@/assets/Service3.png'
    import service4 from '@/assets/Service4.png'
    import service5 from '@/assets/Service5.png'
    import service6 from '@/assets/Service6.png'
    import service7 from '@/assets/Service7.png'
    import service8 from '@/assets/Service8.png'
    import eoshmaCircle from '@/assets/eoshmaCircle.png'

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
