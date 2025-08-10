<template>
  <DoctorLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
              Lab Results
            </h1>
            <div class="flex items-center space-x-4">
              <button
                @click="refreshResults"
                :disabled="loading"
                class="inline-flex items-center rounded-lg bg-first-accent px-4 py-2 text-sm font-medium text-white hover:bg-second-accent disabled:opacity-50"
              >
                <svg
                  class="mr-2 h-4 w-4"
                  :class="{ 'animate-spin': loading }"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                {{ loading ? 'Refreshing...' : 'Refresh' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Search
              </label>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by patient name, test type..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Status
              </label>
              <select
                v-model="statusFilter"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Date Range
              </label>
              <select
                v-model="dateFilter"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              >
                <option value="">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Lab Results List -->
        <div class="rounded-lg bg-white shadow">
          <!-- Loading State -->
          <div v-if="loading" class="p-12 text-center">
            <div
              class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"
            ></div>
            <p class="mt-4 text-gray-600">Loading lab results...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="p-12 text-center">
            <div class="mx-auto h-16 w-16 text-red-500">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <p class="mt-4 text-lg font-medium text-gray-900">
              Error loading lab results
            </p>
            <p class="mt-2 text-gray-600">{{ error }}</p>
            <button
              @click="refreshResults"
              class="mt-4 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
              Try Again
            </button>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredResults.length === 0" class="p-12 text-center">
            <div class="mx-auto h-16 w-16 text-gray-400">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
            </div>
            <p class="mt-4 text-lg font-medium text-gray-900">No lab results found</p>
            <p class="mt-2 text-gray-600">
              {{ searchQuery || statusFilter || dateFilter 
                  ? 'Try adjusting your filters' 
                  : 'Lab results will appear here once available' }}
            </p>
          </div>

          <!-- Results Table -->
          <div v-else class="overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                      Patient
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                      Test Type
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                      Laboratory
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                      Status
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                      Date
                    </th>
                    <th
                      class="relative px-6 py-3"
                    >
                      <span class="sr-only">Actions</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr
                    v-for="result in filteredResults"
                    :key="result.id"
                    class="hover:bg-gray-50"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div
                          class="h-10 w-10 flex-shrink-0 rounded-full bg-blue-600 flex items-center justify-center"
                        >
                          <span class="text-sm font-medium text-white">
                            {{ getInitials(result.patient?.name || 'Patient') }}
                          </span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ result.patient?.name || 'Unknown Patient' }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ result.patient?.email || '' }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ result.lab_request?.test_type || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ result.laboratory?.name || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getStatusClass(result.status)"
                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                      >
                        {{ result.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(result.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center justify-end space-x-2">
                        <button
                          @click="viewResult(result)"
                          class="text-blue-600 hover:text-blue-900"
                          title="View Details"
                        >
                          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            />
                          </svg>
                        </button>
                        <button
                          v-if="result.attachment"
                          @click="downloadAttachment(result)"
                          class="text-green-600 hover:text-green-900"
                          title="Download Attachment"
                        >
                          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Result Detail Modal -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-800">Lab Result Details</h2>
          <button
            @click="closeDetailModal"
            class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <div v-if="selectedResult" class="space-y-6">
          <!-- Patient Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-3">Patient Information</h3>
            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Name:</span>
                <span class="font-medium">{{ selectedResult.patient?.name || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium">{{ selectedResult.patient?.email || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Test Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-3">Test Information</h3>
            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Test Type:</span>
                <span class="font-medium">{{ selectedResult.lab_request?.test_type || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Laboratory:</span>
                <span class="font-medium">{{ selectedResult.laboratory?.name || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span :class="getStatusClass(selectedResult.status)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  {{ selectedResult.status }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Date:</span>
                <span class="font-medium">{{ formatDate(selectedResult.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Result Details -->
          <div v-if="selectedResult.result_details">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Result Details</h3>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-gray-800 whitespace-pre-wrap">{{ selectedResult.result_details }}</p>
            </div>
          </div>

          <!-- Attachment -->
          <div v-if="selectedResult.attachment">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Attachment</h3>
            <div class="bg-gray-50 rounded-lg p-4">
              <button
                @click="downloadAttachment(selectedResult)"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
              >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                Download Attachment
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLabResultStore } from '@/stores/labResultStore'
import DoctorLayout from '@/layout/DoctorLayout.vue'

// Store
const labResultStore = useLabResultStore()

// State
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const showDetailModal = ref(false)
const selectedResult = ref(null)
const loading = ref(false)
const error = ref(null)
const labResults = ref([])

// Computed
const filteredResults = computed(() => {
  let results = labResults.value || []

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    results = results.filter(result => 
      result.patient?.name?.toLowerCase().includes(query) ||
      result.lab_request?.test_type?.toLowerCase().includes(query) ||
      result.laboratory?.name?.toLowerCase().includes(query)
    )
  }

  // Status filter
  if (statusFilter.value) {
    results = results.filter(result => result.status === statusFilter.value)
  }

  // Date filter
  if (dateFilter.value) {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    
    results = results.filter(result => {
      const resultDate = new Date(result.created_at)
      
      switch (dateFilter.value) {
        case 'today':
          return resultDate >= today
        case 'week':
          const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)
          return resultDate >= weekAgo
        case 'month':
          const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate())
          return resultDate >= monthAgo
        default:
          return true
      }
    })
  }

  return results
})

// Methods
const refreshResults = async () => {
  loading.value = true
  error.value = null
  
  try {
    const result = await labResultStore.getLabResults()
    if (result && result.success !== false) {
      labResults.value = Array.isArray(result) ? result : (result.data || [])
    } else {
      throw new Error(result?.message || 'Failed to fetch lab results')
    }
  } catch (err) {
    console.error('Error fetching lab results:', err)
    error.value = err.message || 'Failed to load lab results'
    labResults.value = []
  } finally {
    loading.value = false
  }
}

const viewResult = (result) => {
  selectedResult.value = result
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedResult.value = null
}

const downloadAttachment = (result) => {
  if (result.attachment) {
    // Create a link to download the attachment
    const link = document.createElement('a')
    link.href = result.attachment
    link.download = `lab_result_${result.id}_attachment`
    link.target = '_blank'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Lifecycle
onMounted(() => {
  refreshResults()
})
</script>
