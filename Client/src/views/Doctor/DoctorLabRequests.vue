<template>
  <DoctorLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900">Lab Requests</h1>
            <p class="text-sm text-gray-600 mt-1">Manage and track laboratory test requests</p>
          </div>
          <div class="flex items-center space-x-3">
            <!-- Refresh Button -->
            <button
              @click="fetchLabRequests"
              :disabled="isLoading"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              <svg 
                :class="['h-4 w-4 mr-2', isLoading ? 'animate-spin' : '']" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Refresh
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-12">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
          <p class="mt-2 text-gray-600">Loading lab requests...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Error loading lab requests</h3>
              <p class="mt-1 text-sm text-red-700">{{ error }}</p>
            </div>
          </div>
        </div>

        <!-- Lab Requests Table -->
        <div v-else class="bg-white shadow rounded-lg overflow-hidden">
          <!-- Table Header -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">
                Lab Requests ({{ labRequests.length }})
              </h2>
              
              <!-- Filter by Status -->
              <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Filter by status:</label>
                <select 
                  v-model="selectedStatus" 
                  @change="filterByStatus"
                  class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="filteredLabRequests.length === 0" class="text-center py-12">
            <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No lab requests found</h3>
            <p class="text-gray-600">
              {{ selectedStatus ? `No ${selectedStatus} lab requests at the moment.` : 'No lab requests have been created yet.' }}
            </p>
          </div>

          <!-- Table -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Request ID
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Patient
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Test Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Laboratory
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created Date
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr 
                  v-for="labRequest in filteredLabRequests" 
                  :key="labRequest.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #{{ labRequest.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                          <span class="text-xs font-medium text-white">
                            {{ getInitials(labRequest.consultation?.patient?.name || 'Unknown') }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ labRequest.consultation?.patient?.name || 'Unknown Patient' }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ labRequest.consultation?.patient?.email || 'No email' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ labRequest.test_type }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ labRequest.laboratory?.name || 'Not assigned' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(labRequest.status)">
                      {{ formatStatus(labRequest.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(labRequest.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <!-- View Button -->
                    <button
                      @click="viewLabRequest(labRequest)"
                      class="text-blue-600 hover:text-blue-900 transition-colors"
                      title="View Details"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>

                    <!-- Delete Button -->
                    <button
                      @click="confirmDeleteLabRequest(labRequest)"
                      :disabled="labRequest.status === 'completed'"
                      class="text-red-600 hover:text-red-900 transition-colors disabled:text-gray-400 disabled:cursor-not-allowed"
                      title="Delete Request"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click="closeDeleteModal"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4"
        @click.stop
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Delete Lab Request</h3>
        </div>
        <div class="px-6 py-4">
          <p class="text-gray-600">
            Are you sure you want to delete this lab request? This action cannot be undone.
          </p>
          <div class="mt-4 bg-gray-50 p-3 rounded-md">
            <p class="text-sm"><strong>Request ID:</strong> #{{ labRequestToDelete?.id }}</p>
            <p class="text-sm"><strong>Test Type:</strong> {{ labRequestToDelete?.test_type }}</p>
            <p class="text-sm"><strong>Patient:</strong> {{ labRequestToDelete?.consultation?.patient?.name }}</p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
          <button
            @click="closeDeleteModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
          >
            Cancel
          </button>
          <button
            @click="deleteLabRequest"
            :disabled="isDeleting"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
          >
            <span v-if="isDeleting">Deleting...</span>
            <span v-else>Delete</span>
          </button>
        </div>
      </div>
    </div>

    <!-- View Details Modal -->
    <div
      v-if="showViewModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click="closeViewModal"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Lab Request Details</h3>
        </div>
        <div class="px-6 py-4 space-y-4" v-if="selectedLabRequest">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-500">Request ID</label>
              <p class="mt-1 text-sm text-gray-900">#{{ selectedLabRequest.id }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-500">Status</label>
              <p class="mt-1">
                <span :class="getStatusBadgeClass(selectedLabRequest.status)">
                  {{ formatStatus(selectedLabRequest.status) }}
                </span>
              </p>
            </div>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-500">Test Type</label>
            <p class="mt-1 text-sm text-gray-900">{{ selectedLabRequest.test_type }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-500">Patient Name</label>
              <p class="mt-1 text-sm text-gray-900">{{ selectedLabRequest.consultation?.patient?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-500">Patient Email</label>
              <p class="mt-1 text-sm text-gray-900">{{ selectedLabRequest.consultation?.patient?.email || 'No email' }}</p>
            </div>
          </div>

          <div>
            <label class="text-sm font-medium text-gray-500">Laboratory</label>
            <p class="mt-1 text-sm text-gray-900">{{ selectedLabRequest.laboratory?.name || 'Not assigned' }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-500">Created Date</label>
              <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLabRequest.created_at) }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-500">Updated Date</label>
              <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLabRequest.updated_at) }}</p>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
          <button
            @click="closeViewModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import { useLabRequestStore } from '@/stores/labRequestStore';
import DoctorLayout from '@/layout/DoctorLayout.vue';

const toast = useToast();
const labRequestStore = useLabRequestStore();

// State
const selectedStatus = ref('');
const showDeleteModal = ref(false);
const showViewModal = ref(false);
const labRequestToDelete = ref(null);
const selectedLabRequest = ref(null);
const isDeleting = ref(false);

// Computed
const labRequests = computed(() => labRequestStore.labRequests || []);
const isLoading = computed(() => labRequestStore.isLoading);
const error = computed(() => labRequestStore.errors?.message || '');

const filteredLabRequests = computed(() => {
  if (!selectedStatus.value) {
    return labRequests.value;
  }
  return labRequests.value.filter(request => request.status === selectedStatus.value);
});

// Methods
const fetchLabRequests = async () => {
  const result = await labRequestStore.fetchLabRequests();
  if (!result.success) {
    toast.error(result.error || 'Failed to load lab requests', {
      position: 'top-right',
      timeout: 3000
    });
  }
};

const filterByStatus = () => {
  // The computed property will automatically update the filtered list
};

const getStatusBadgeClass = (status) => {
  const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
  
  switch (status) {
    case 'pending':
      return `${baseClasses} bg-yellow-100 text-yellow-800`;
    case 'completed':
      return `${baseClasses} bg-green-100 text-green-800`;
    case 'cancelled':
      return `${baseClasses} bg-red-100 text-red-800`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`;
  }
};

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getInitials = (name) => {
  if (!name) return 'U';
  const names = name.split(' ');
  return names.map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
};

const viewLabRequest = (labRequest) => {
  selectedLabRequest.value = labRequest;
  showViewModal.value = true;
};

const closeViewModal = () => {
  showViewModal.value = false;
  selectedLabRequest.value = null;
};

const confirmDeleteLabRequest = (labRequest) => {
  if (labRequest.status === 'completed') {
    toast.warning('Cannot delete completed lab requests', {
      position: 'top-right',
      timeout: 3000
    });
    return;
  }
  
  labRequestToDelete.value = labRequest;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  labRequestToDelete.value = null;
};

const deleteLabRequest = async () => {
  if (!labRequestToDelete.value) return;
  
  isDeleting.value = true;
  
  try {
    const result = await labRequestStore.deleteLabRequest(labRequestToDelete.value.id);
    
    if (result.success) {
      toast.success('Lab request deleted successfully', {
        position: 'top-right',
        timeout: 3000
      });
      closeDeleteModal();
    } else {
      throw new Error(result.error);
    }
  } catch (err) {
    console.error('Error deleting lab request:', err);
    toast.error(err.message || 'Failed to delete lab request', {
      position: 'top-right',
      timeout: 3000
    });
  } finally {
    isDeleting.value = false;
  }
};

// Lifecycle
onMounted(() => {
  fetchLabRequests();
});
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
