<template>
    <div class="space-y-6">
        <!-- Add Contact Button -->
        <div class="flex justify-end">
            <button 
                @click="showAddContact = true"
                class="flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Contact
            </button>
        </div>

        <!-- Contacts Table -->
        <div v-if="contacts.length > 0" class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Decision Maker</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Phone</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Preferred Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Last Contacted</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-blue-50 transition-all">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ contact.name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                {{ contact.role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span v-if="contact.is_decision_maker" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                Yes
                            </span>
                            <span v-else class="text-gray-500">No</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ contact.phone }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <a :href="`mailto:${contact.email}`" class="text-[#0c8ce9] hover:underline">{{ contact.email }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ contact.preferred_contact }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ contact.last_contacted }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="callContact(contact.id)" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded" title="Call">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </button>
                                <button @click="emailContact(contact.id)" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded" title="Email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button @click="editContact(contact.id)" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-[#0c8ce9] hover:bg-blue-100 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-600">No contacts added yet. Click "Add Contact" to get started.</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    clientId: {
        type: [String, Number],
        required: true
    }
})

const showAddContact = ref(false)
const contacts = ref([
    {
        id: 1,
        name: 'Mr. Juan Dela Cruz',
        role: 'Project Manager',
        is_decision_maker: true,
        phone: '+63 2 1234 5678',
        email: 'juan.delacruz@acme.com',
        preferred_contact: 'Email',
        last_contacted: '2 days ago'
    },
    {
        id: 2,
        name: 'Ms. Maria Santos',
        role: 'Accountant',
        is_decision_maker: false,
        phone: '+63 917 123 4567',
        email: 'maria.santos@acme.com',
        preferred_contact: 'Phone',
        last_contacted: '5 days ago'
    }
])

const callContact = (contactId) => {
    alert(`Calling contact ${contactId}`)
}

const emailContact = (contactId) => {
    alert(`Opening email for contact ${contactId}`)
}

const editContact = (contactId) => {
    alert(`Editing contact ${contactId}`)
}
</script>
