<script setup>
import { onMounted, ref, computed } from "vue";
import Modal from "@/components/UI/Modal.vue";
import { useLabRequestStore } from "@/stores/labRequestStore";
import { storeToRefs } from "pinia";
import { useLabResultStore } from "@/stores/labResultStore";
import { useAuthStore } from "@/stores/auth"; // Import auth store

const { getLabRequests } = useLabRequestStore();
const { createLabResult } = useLabResultStore();
const labRequests = ref([]);
const errors = ref(storeToRefs(useLabResultStore()));

const statusOptions = ["in_progress", "completed", "cancelled"];
const selectedStatus = ref("all"); // Default to 'all'

const showPopup = ref(false);
const showSendResultPopup = ref(false); // New state for send result popup
const selectedLabRequest = ref(null);
const isFileUploaded = ref(false);

const labResultData = ref({
  lab_request_id: "",
  laboratory_id: "",
  result_details: "",
  attachment: "",
});

const authStore = useAuthStore(); // Initialize auth store

onMounted(async () => {
  await authStore.getUser(); // Fetch the current user
  labRequests.value = await getLabRequests();
  console.log(labRequests.value);
});

const filteredLabRequests = computed(() => {
  const userId = authStore.user?.id; // Get the current user's ID
  if (!userId) return []; // Return empty if user ID is not available

  let filtered = labRequests.value.filter(
    (request) => request.laboratory_id === userId,
  );

  if (selectedStatus.value === "all") {
    return filtered;
  }
  return filtered.filter((request) => request.status === selectedStatus.value);
});

// Function to change the selected status
const changeStatus = (status) => {
  selectedStatus.value = status;
};

const uploadAttachment = () => {
  widget.open();
};

const widget = window.cloudinary.createUploadWidget(
  {
    cloud_name: "dqxy77qks",
    upload_preset: "tenadam-upload",
    multiple: false,
    sources: ["local"],
  },
  (error, result) => {
    if (!error && result && result.event === "success") {
      console.log("uploaded Successfully...  ", result.info.secure_url);
      labResultData.value.attachment = result.info.secure_url;
      isFileUploaded.value = true;
    }
  },
);

const handleSubmit = async () => {
  labResultData.value.lab_request_id = selectedLabRequest.value.id;
  labResultData.value.laboratory_id = selectedLabRequest.value.laboratory_id;

  createLabResult(labResultData.value);
  isFileUploaded.value = false;
  labResultData.value = {
    lab_request_id: "",
    laboratory_id: "",
    result_details: "",
    attachment: "",
  };
  labRequests.value = await getLabRequests();

  closeSendResultPopup();
};

// Function to open popup with lab request details
const openDetailPopup = (labRequest) => {
  selectedLabRequest.value = labRequest;
  console.log(selectedLabRequest.value, "selectedLabRequest");
  showPopup.value = true;
};

// Function to close popup
const closePopup = () => {
  showPopup.value = false;
};

// Function to open send result popup
const openSendResultPopup = () => {
  showPopup.value = false; // Close the detail popup
  showSendResultPopup.value = true; // Open the send result popup
};

// Function to close send result popup
const closeSendResultPopup = () => {
  showSendResultPopup.value = false;
};
</script>

<template>
  <div
    class="ark:border-gray-800 ark:bg-white/[0.03] overflow-hidden rounded-xl border border-gray-200 bg-white"
  >
    <!-- Status Filter -->
    <div class="border-b border-gray-200 p-4">
      <h3 class="mb-2 text-sm font-medium text-gray-700">Filter by Status</h3>
      <div class="flex flex-wrap gap-3">
        <!-- "All" option -->
        <div class="flex items-center">
          <input
            type="radio"
            id="status-all"
            name="status-filter"
            :checked="selectedStatus === 'all'"
            @change="changeStatus('all')"
            class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
          <label
            for="status-all"
            class="ml-2 cursor-pointer text-sm capitalize text-gray-700"
            >All</label
          >
        </div>

        <!-- Individual status options -->
        <div
          v-for="status in statusOptions"
          :key="status"
          class="flex items-center"
        >
          <input
            type="radio"
            :id="'status-' + status"
            name="status-filter"
            :checked="selectedStatus === status"
            @change="changeStatus(status)"
            class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
          <label
            :for="'status-' + status"
            class="ml-2 cursor-pointer text-sm capitalize text-gray-700"
            >{{ status }}</label
          >
        </div>
      </div>
    </div>

    <div class="custom-scrollbar max-w-full overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="ark:border-gray-700 border-b border-gray-200">
            <th class="w-2/11 px-5 py-3 text-center sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">
                Patient Name
              </p>
            </th>
            <th class="w-2/11 px-5 py-3 text-center sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Doctor Name</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-center sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Test Type</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-center sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Status</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-center sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Action</p>
            </th>
          </tr>
        </thead>
        <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
          <!-- No results message -->
          <tr v-if="filteredLabRequests.length === 0">
            <td colspan="6" class="px-5 py-8 text-center sm:px-6">
              <div class="flex flex-col items-center justify-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-12 w-12 text-gray-300"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <p class="mt-2 text-base text-gray-500">
                  No lab requests found with status:
                  <span class="font-medium capitalize">{{
                    selectedStatus
                  }}</span>
                </p>
                <button
                  @click="changeStatus('all')"
                  class="mt-4 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Show All Lab Requests
                </button>
              </div>
            </td>
          </tr>
          <tr
            v-for="(labRequest, index) in filteredLabRequests"
            :key="index"
            class="ark:border-gray-800 border-t border-gray-100"
          >
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                {{ labRequest.consultation?.patient?.name || "N/A" }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="text-theme-sm text-gray-500">
                {{ labRequest.consultation?.doctor?.name || "N/A" }}
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <span>
                {{ labRequest.test_type }}
              </span>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                <span
                  :class="[
                    'rounded-full px-2 py-0.5 text-theme-xs font-medium',
                    {
                      'ark:bg-success-500/15 ark:text-success-500 bg-success-50 text-success-700':
                        labRequest.status === 'completed',
                      'ark:bg-warning-500/15 ark:text-warning-400 bg-warning-50 text-warning-700':
                        labRequest.status === 'in_progress',
                      'ark:bg-error-500/15 ark:text-error-500 bg-error-50 text-error-700':
                        labRequest.status === 'cancelled',
                    },
                  ]"
                >
                  {{ labRequest.status }}</span
                >
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p
                @click="openDetailPopup(labRequest)"
                class="cursor-pointer rounded bg-gray-200 p-1 py-2 text-center text-theme-sm font-bold text-gray-500 transition-colors duration-200 hover:bg-success-50 hover:text-success-700"
              >
                More Detail
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Using the Modal component -->
    <Modal v-if="showPopup" @close="closePopup" :fullScreenBackdrop="true">
      <!-- ...existing modal content... -->
    </Modal>

    <!-- Send Result Modal -->
    <Modal
      v-if="showSendResultPopup"
      @close="closeSendResultPopup"
      :fullScreenBackdrop="true"
    >
      <!-- ...existing modal content... -->
    </Modal>
  </div>
</template>
