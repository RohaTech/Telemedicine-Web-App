<template>
  <DoctorLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
              My Patients
            </h1>
            <div class="flex items-center space-x-4">
              <button
                @click="refreshPatients"
                :disabled="loading"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
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
                Search Patients
              </label>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name, email, phone..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Gender
              </label>
              <select
                v-model="genderFilter"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              >
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Last Consultation
              </label>
              <select
                v-model="consultationFilter"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              >
                <option value="">Any Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="3months">Last 3 Months</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Patients List -->
        <div class="rounded-lg bg-white shadow">
          <!-- Loading State -->
          <div v-if="loading" class="p-12 text-center">
            <div
              class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"
            ></div>
            <p class="mt-4 text-gray-600">Loading patients...</p>
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
              Error loading patients
            </p>
            <p class="mt-2 text-gray-600">{{ error }}</p>
            <button
              @click="refreshPatients"
              class="mt-4 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
              Try Again
            </button>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredPatients.length === 0" class="p-12 text-center">
            <div class="mx-auto h-16 w-16 text-gray-400">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <p class="mt-4 text-lg font-medium text-gray-900">No patients found</p>
            <p class="mt-2 text-gray-600">
              {{ searchQuery || genderFilter || consultationFilter 
                  ? 'Try adjusting your filters' 
                  : 'Patients you have consulted with will appear here' }}
            </p>
          </div>

          <!-- Patients Grid -->
          <div v-else class="p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="patient in filteredPatients"
                :key="patient.id"
                class="relative rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                @click="viewPatientDetails(patient)"
              >
                <!-- Patient Avatar -->
                <div class="flex items-center space-x-4">
                  <div
                    class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-blue-600"
                  >
                    <img
                      v-if="patient.profile_picture"
                      :src="patient.profile_picture"
                      :alt="patient.name"
                      class="h-full w-full rounded-full object-cover"
                    />
                    <span
                      v-else
                      class="text-xl font-medium text-white"
                    >
                      {{ getInitials(patient.name) }}
                    </span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ patient.name }}
                    </h3>
                    <p class="text-sm text-gray-600">
                      {{ patient.age || 'N/A' }} years • {{ patient.gender || 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ patient.email }}
                    </p>
                  </div>
                </div>

                <!-- Patient Stats -->
                <div class="mt-4 grid grid-cols-2 gap-4">
                  <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">
                      {{ patient.consultation_count || 0 }}
                    </p>
                    <p class="text-xs text-gray-500">Consultations</p>
                  </div>
                  <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">
                      {{ patient.lab_request_count || 0 }}
                    </p>
                    <p class="text-xs text-gray-500">Lab Requests</p>
                  </div>
                </div>

                <!-- Last Consultation -->
                <div class="mt-4 border-t border-gray-200 pt-4">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Last consultation:</span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ formatDate(patient.last_consultation_date) }}
                    </span>
                  </div>
                </div>

                <!-- Contact Info -->
                <div class="mt-3">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Phone:</span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ patient.phone || 'N/A' }}
                    </span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex space-x-2">
                  <!-- <button
                    @click.stop="viewConsultationHistory(patient)"
                    class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                  >
                    View History
                  </button> -->
                  <button
                    @click.stop="contactPatient(patient)"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Contact
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Patient Details Modal -->
    <div
      v-if="showPatientModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-800">Patient Details</h2>
          <button
            @click="closePatientModal"
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

        <div v-if="selectedPatient" class="space-y-6">
          <!-- Patient Basic Info -->
          <div class="flex items-center space-x-4">
            <div
              class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-full bg-blue-600"
            >
              <img
                v-if="selectedPatient.profile_picture"
                :src="selectedPatient.profile_picture"
                :alt="selectedPatient.name"
                class="h-full w-full rounded-full object-cover"
              />
              <span
                v-else
                class="text-2xl font-medium text-white"
              >
                {{ getInitials(selectedPatient.name) }}
              </span>
            </div>
            <div>
              <h3 class="text-2xl font-bold text-gray-900">{{ selectedPatient.name }}</h3>
              <p class="text-gray-600">
                {{ selectedPatient.age || 'N/A' }} years old • {{ selectedPatient.gender || 'N/A' }}
              </p>
              <p class="text-gray-500">{{ selectedPatient.email }}</p>
            </div>
          </div>

          <!-- Contact Information -->
          <div>
            <h4 class="text-lg font-medium text-gray-900 mb-3">Contact Information</h4>
            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium">{{ selectedPatient.email || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Phone:</span>
                <span class="font-medium">{{ selectedPatient.phone || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Address:</span>
                <span class="font-medium">{{ selectedPatient.address || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Medical Summary -->
          <div>
            <h4 class="text-lg font-medium text-gray-900 mb-3">Medical Summary</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                  <p class="text-3xl font-bold text-blue-600">
                    {{ selectedPatient.consultation_count || 0 }}
                  </p>
                  <p class="text-sm text-gray-500">Total Consultations</p>
                </div>
                <div class="text-center">
                  <p class="text-3xl font-bold text-green-600">
                    {{ selectedPatient.lab_request_count || 0 }}
                  </p>
                  <p class="text-sm text-gray-500">Lab Requests</p>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between">
                  <span class="text-gray-600">First Consultation:</span>
                  <span class="font-medium">{{ formatDate(selectedPatient.first_consultation_date) }}</span>
                </div>
                <div class="flex justify-between mt-2">
                  <span class="text-gray-600">Last Consultation:</span>
                  <span class="font-medium">{{ formatDate(selectedPatient.last_consultation_date) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 pt-4">
            <!-- <button
              @click="viewConsultationHistory(selectedPatient)"
              class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
              View Consultation History
            </button> -->
            <button
              @click="contactPatient(selectedPatient)"
              class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Contact Patient
            </button>
          </div>
        </div>
      </div>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import DoctorLayout from '@/layout/DoctorLayout.vue'

// Stores
const authStore = useAuthStore()
const router = useRouter()

// State
const searchQuery = ref('')
const genderFilter = ref('')
const consultationFilter = ref('')
const showPatientModal = ref(false)
const selectedPatient = ref(null)
const loading = ref(false)
const error = ref(null)
const patients = ref([])

// Computed
const filteredPatients = computed(() => {
  let result = patients.value || []

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(patient => 
      patient.name?.toLowerCase().includes(query) ||
      patient.email?.toLowerCase().includes(query) ||
      patient.phone?.includes(query)
    )
  }

  // Gender filter
  if (genderFilter.value) {
    result = result.filter(patient => patient.gender === genderFilter.value)
  }

  // Consultation date filter
  if (consultationFilter.value) {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    
    result = result.filter(patient => {
      if (!patient.last_consultation_date) return false
      
      const consultationDate = new Date(patient.last_consultation_date)
      
      switch (consultationFilter.value) {
        case 'today':
          return consultationDate >= today
        case 'week':
          const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)
          return consultationDate >= weekAgo
        case 'month':
          const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate())
          return consultationDate >= monthAgo
        case '3months':
          const threeMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 3, today.getDate())
          return consultationDate >= threeMonthsAgo
        default:
          return true
      }
    })
  }

  return result
})

// Methods
const refreshPatients = async () => {
  loading.value = true
  error.value = null
    try {
    const response = await fetch(`/api/doctors/patients`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
      },
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    
    if (data.success) {
      patients.value = data.data || []
    } else {
      throw new Error(data.message || 'Failed to fetch patients')
    }
  } catch (err) {
    console.error('Error fetching patients:', err)
    error.value = err.message || 'Failed to load patients'
    patients.value = []
  } finally {
    loading.value = false
  }
}

const viewPatientDetails = (patient) => {
  selectedPatient.value = patient
  showPatientModal.value = true
}

const closePatientModal = () => {
  showPatientModal.value = false
  selectedPatient.value = null
}

const viewConsultationHistory = (patient) => {
  // Navigate to consultation history for this patient
  router.push({
    name: 'PatientConsultationHistory',
    params: { patientId: patient.id }
  })
}

const contactPatient = (patient) => {
  // Open email client or show contact options
  if (patient.email) {
    window.location.href = `mailto:${patient.email}`
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

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(() => {
  refreshPatients()
})
</script>
