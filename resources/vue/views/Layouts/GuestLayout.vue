<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import logo from '@/assets/EOSHMA.png'
    import logoImage from '@/assets/EOSHMAlogin.png'
    const isOpen = ref(false);
    const isScrolled = ref(false);
    
    const toggleMenu = () => { isOpen.value = !isOpen.value; };
    
    const handleScroll = () => {
        isScrolled.value = window.scrollY > 10;
    };
    
    onMounted(() => {
        window.addEventListener('scroll', handleScroll);
    });
    
    onUnmounted(() => {
        window.removeEventListener('scroll', handleScroll);
    });
</script>

<template>
    <nav :class="['w-full fixed shadow-[inset_0_30px_30px_-6px_rgba(0,0,0,0.1)] transition-colors duration-300', isScrolled ? 'bg-white' : 'bg-transparent']">
        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <img :src="logo" alt="Logo" class="h-7 w-auto"/>
                <div class="hidden md:flex space-x-10 items-center">
                    <router-link to="/" class="text-black border-b-2 border-transparent hover:border-black transition-all duration-200 pb-1">Home</router-link>
                    <router-link to="/about" class="text-black border-b-2 border-transparent hover:border-black transition-all duration-200 pb-1">About</router-link>
                    <router-link to="/services" class="text-black border-b-2 border-transparent hover:border-black transition-all duration-200 pb-1">Services</router-link>
                    <router-link to="/project" class="text-black border-b-2 border-transparent hover:border-black transition-all duration-200 pb-1">Projects</router-link>
                    <router-link to="/contact" class="text-black border-b-2 border-transparent hover:border-black transition-all duration-200 pb-1">Contact</router-link>
                </div>
                <button @click="toggleMenu" class="md:hidden text-gray-700 focus:outline-none">
                    <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div v-show="isOpen" class="md:hidden bg-white shadow-md">
            <div class="flex flex-col space-y-2 py-4 px-6">
                <router-link to="/" class="text-gray-700 hover:text-blue-500">Home</router-link>
                <router-link to="/about" class="text-gray-700 hover:text-blue-500">About</router-link>
                <router-link to="/services" class="text-gray-700 hover:text-blue-500">Services</router-link>
                <router-link to="/project" class="text-gray-700 hover:text-blue-500">Projects</router-link>
                <router-link to="/contact" class="text-gray-700 hover:text-blue-500">Contact</router-link>
            </div>
        </div>
    </nav>
    <slot></slot>
    <footer class="bg-[#04162d] text-gray-400 py-10 pt-15 px-6 sm:px-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr] gap-12 mb-15">
                <div class="space-y-6">
                    <img :src="logoImage" class="w-auto h-8">
                    <p class="text-sm leading-relaxed max-w-xs">
                        Dependable construction services and quality building materials through professionalism, integrity, and a commitment to customer satisfaction.
                    </p>
                </div>
                <div class="space-y-4">
                    <h3 class="text-white font-bold">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><router-link to="/" class="hover:text-white transition">Home</router-link></li>
                        <li><router-link to="/about" class="hover:text-white transition">About</router-link></li>
                        <li><router-link to="/services" class="hover:text-white transition">Services</router-link></li>
                        <li><router-link to="/project" class="hover:text-white transition">Projects</router-link></li>
                        <li><router-link to="/contact" class="hover:text-white transition">Contact</router-link></li>
                        <li><router-link to="/login" class="hover:text-white transition">Portal</router-link></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h3 class="text-white font-bold">Contact Information</h3>
                    <div class="text-sm space-y-4">
                        <div>
                            <p class="text-gray-500 mb-1">Address:</p>
                            <p>123 Main Street, Cityville, Country</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Phone:</p>
                            <p>+63 912 345 6789</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Email:</p>
                            <p>eoshma@example.com</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <h3 class="text-white font-bold">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">License</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 text-center text-sm">
                <p>&copy; 2025 EOSHMA. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>
