<template>
    <!-- <guest-layout> -->
        <div class="min-h-screen bg-gray-100 flex items-start justify-center">
            <div class="w-full max-w-md mt-8">
                <div class="mb-6 text-center">
                    <h1 class="text-blue-500 text-2xl font-bold">Registration Page</h1>
                </div>

                <div class="bg-white rounded-md shadow-md border border-gray-200">
                    <div class="p-6">
                        <h2 class="text-center text-lg font-medium text-gray-800 mb-4">Create an account</h2>

                        <form class="space-y-4" @submit.prevent="handleRegister" novalidate>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input
                                    type="text"
                                    v-model="userData.name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    autocomplete="name"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input
                                    type="email"
                                    v-model="userData.email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    autocomplete="email"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input
                                    type="password"
                                    v-model="userData.password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    autocomplete="new-password"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input
                                    type="password"
                                    v-model="userData.password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    autocomplete="new-password"
                                />
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="w-full flex items-center justify-center gap-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-60"
                                >
                                    <span>{{ loading ? 'Registering...' : 'Register' }}</span>
                                </button>
                            </div>

                            <div class="text-center text-xs text-blue-500">
                                <router-link to="/login" class="hover:underline">Back to Login</router-link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <!-- </guest-layout> -->
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'
import { useAuthStore } from "@/stores/auth"

const router = useRouter()
const auth = useAuthStore()

const userData = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const loading = ref(false)

const resetForm = () => {
    userData.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    }
}

const handleRegister = async () => {
    // basic client-side validation
    if (!userData.value.name || !userData.value.email || !userData.value.password || !userData.value.password_confirmation) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing fields',
            text: 'Please fill out all fields.'
        })
        return
    }

    if (userData.value.password !== userData.value.password_confirmation) {
        Swal.fire({
            icon: 'error',
            title: 'Password mismatch',
            text: 'Password and Confirm Password do not match.'
        })
        return
    }

    loading.value = true
    try {
        const response = await axios.post('/api/register', userData.value)
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Registration successful',
            timer: 1400,
            showConfirmButton: false
        })
        resetForm()
        // redirect to login after a small delay so user sees the toast
        setTimeout(() => router.push('/login'), 700)
    } catch (error) {
        const msg = 'Registration failed'
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg
        })
    } finally {
        loading.value = false
    }
}
if (auth.user) {
    router.push('/dashboard')
}
</script>
