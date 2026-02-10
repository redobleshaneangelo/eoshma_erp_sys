<template>
    <div class="space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-900">
                <span class="font-semibold">Total Records:</span> {{ history.length }} entries
                | <span class="font-semibold">Last Change:</span> {{ history[0]?.timestamp || 'No changes' }}
            </p>
        </div>

        <!-- Timeline -->
        <div class="space-y-4">
            <div v-for="(entry, index) in history" :key="entry.id" class="flex gap-4">
                <!-- Timeline Marker -->
                <div class="flex flex-col items-center pt-2">
                    <div 
                        :class="getEventTypeColor(entry.type)"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
                    >
                        {{ getEventIcon(entry.type) }}
                    </div>
                    <div v-if="index < history.length - 1" class="w-0.5 h-16 bg-gray-200 my-2"></div>
                </div>

                <!-- Event Details -->
                <div class="flex-1 pb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ entry.description }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ entry.timestamp }}</p>
                            </div>
                            <span :class="eventBadgeStyles[entry.type]" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold">
                                {{ entry.type }}
                            </span>
                        </div>

                        <!-- Change Details -->
                        <div v-if="entry.changes && entry.changes.length > 0" class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Changes:</p>
                            <div class="space-y-1">
                                <div v-for="change in entry.changes" :key="change.field" class="text-xs text-gray-600 font-mono bg-gray-50 rounded px-2 py-1">
                                    <span class="font-semibold">{{ change.field }}:</span>
                                    <span class="line-through text-red-600">{{ change.old_value }}</span>
                                    <span class="text-green-600">→ {{ change.new_value }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="mt-3 flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ entry.user }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="history.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600">No history records found.</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    clientId: {
        type: [String, Number],
        required: true
    }
})

const history = [
    {
        id: 1,
        type: 'Created',
        description: 'Client record created',
        timestamp: 'Feb 4, 2026 10:30 AM',
        user: 'John Doe',
        changes: []
    },
    {
        id: 2,
        type: 'Updated',
        description: 'Client status changed',
        timestamp: 'Feb 3, 2026 2:15 PM',
        user: 'Jane Smith',
        changes: [
            { field: 'Status', old_value: 'Prospect', new_value: 'Active' }
        ]
    },
    {
        id: 3,
        type: 'Updated',
        description: 'CRM owner assigned',
        timestamp: 'Feb 1, 2026 9:45 AM',
        user: 'Admin User',
        changes: [
            { field: 'CRM Owner', old_value: 'Unassigned', new_value: 'John Doe' }
        ]
    },
    {
        id: 4,
        type: 'Activity',
        description: 'Contact logged - Phone call regarding Project Alpha',
        timestamp: 'Jan 30, 2026 4:20 PM',
        user: 'John Doe',
        changes: []
    },
    {
        id: 5,
        type: 'Updated',
        description: 'Credit limit updated',
        timestamp: 'Jan 28, 2026 11:00 AM',
        user: 'Finance Team',
        changes: [
            { field: 'Credit Limit', old_value: '₱1,000,000', new_value: '₱2,000,000' }
        ]
    },
    {
        id: 6,
        type: 'Activity',
        description: 'Document uploaded - Business Permit 2026',
        timestamp: 'Jan 15, 2026 3:30 PM',
        user: 'Jane Smith',
        changes: []
    }
]

const eventBadgeStyles = {
    'Created': 'bg-green-100 text-green-800',
    'Updated': 'bg-blue-100 text-blue-800',
    'Activity': 'bg-purple-100 text-purple-800',
    'Deleted': 'bg-red-100 text-red-800'
}

const getEventTypeColor = (type) => {
    const colors = {
        'Created': 'bg-green-500',
        'Updated': 'bg-blue-500',
        'Activity': 'bg-purple-500',
        'Deleted': 'bg-red-500'
    }
    return colors[type] || 'bg-gray-500'
}

const getEventIcon = (type) => {
    const icons = {
        'Created': '✓',
        'Updated': '✎',
        'Activity': '◉',
        'Deleted': '✕'
    }
    return icons[type] || '•'
}
</script>
