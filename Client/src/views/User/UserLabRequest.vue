<script setup>
import { onMounted, ref, computed } from "vue";
import Modal from "@/components/UI/Modal.vue";
import { useLabRequestStore } from "@/stores/labRequestStore";
import { storeToRefs } from "pinia";
import { useLabResultStore } from "@/stores/labResultStore";
import { useLaboratoryStore } from "@/stores/laboratoryStore";
import UserLayout from "@/layout/UserLayout.vue";
import { useAuthStore } from "@/stores/auth";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";

const currentPageTitle = ref("Lab Requests");

const { getLabRequests, updateLabRequest } = useLabRequestStore();
const { createLabResult } = useLabResultStore();
const { getLaboratories } = useLaboratoryStore();
const { user } = storeToRefs(useAuthStore());
const labRequests = ref([]);
const laboratories = ref([]);
const errors = ref(storeToRefs(useLabResultStore()));

const statusOptions = ["pending", "in_progress", "completed", "cancelled"];
const selectedStatus = ref("all"); // Default to 'all'

const showPopup = ref(false);
const showSendResultPopup = ref(false); // New state for send result popup
const showSelectLabPopup = ref(false); // New state for select laboratory popup
const selectedLabRequest = ref(null);
const selectedLaboratory = ref("");
const isFileUploaded = ref(false);
const searchCity = ref(""); // New search city state

const labResultData = ref({
  lab_request_id: "",
  laboratory_id: "",
  result_details: "",
  attachment: "",
});

onMounted(async () => {
  labRequests.value = await getLabRequests();
  laboratories.value = await getLaboratories();
  console.log(labRequests.value);
});

const filteredLabRequests = computed(() => {
  if (selectedStatus.value === "all") {
    return labRequests.value;
  }
  return labRequests.value.filter(
    (request) => request.status === selectedStatus.value,
  );
});

// Function to get user's city from profile
const getUserCity = () => {
  if (user.value && user.value.city) {
    return user.value.city;
  }
  return "";
};

// Computed property for filtered laboratories
const filteredLaboratories = computed(() => {
  let filtered = laboratories.value.filter((lab) => lab.status === "active");

  const userCity = getUserCity().toLowerCase();
  const searchCityValue = searchCity.value.toLowerCase();

  if (searchCityValue) {
    // If searching, filter by search city
    filtered = filtered.filter((lab) => {
      const labCity = (lab.city || "").toLowerCase();
      return labCity.includes(searchCityValue);
    });
  } else if (userCity) {
    // If not searching, show laboratories from same city first
    const sameCity = filtered.filter((lab) => {
      const labCity = (lab.city || "").toLowerCase();
      return labCity === userCity;
    });

    const otherCities = filtered.filter((lab) => {
      const labCity = (lab.city || "").toLowerCase();
      return labCity !== userCity;
    });

    filtered = [...sameCity, ...otherCities];
  }

  return filtered;
});

// Computed property for user's city laboratories only (for select dropdown)
const userCityLaboratories = computed(() => {
  const filtered = laboratories.value.filter((lab) => lab.status === "active");
  const userCity = getUserCity().toLowerCase();

  return filtered.filter((lab) => {
    const labCity = (lab.city || "").toLowerCase();
    return labCity === userCity;
  });
});

// Computed property for search results (separate from select)
const searchResults = computed(() => {
  if (!searchCity.value) return [];

  const filtered = laboratories.value.filter((lab) => lab.status === "active");
  const searchCityValue = searchCity.value.toLowerCase();

  return filtered.filter((lab) => {
    const labCity = (lab.city || "").toLowerCase();
    return labCity.includes(searchCityValue);
  });
});

// Function to get laboratory city
const getLabCity = (lab) => {
  return lab.city || "Unknown";
};

// Function to check if laboratory is in same city as user
const isLabInSameCity = (lab) => {
  const userCity = getUserCity().toLowerCase();
  const labCity = getLabCity(lab).toLowerCase();
  return userCity && labCity === userCity;
};

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

// Function to handle laboratory selection
const handleSelectLaboratory = async () => {
  if (!selectedLaboratory.value) {
    alert("Please select a laboratory");
    return;
  }

  try {
    const updateData = {
      laboratory_id: selectedLaboratory.value,
      status: "in_progress",
    };

    await updateLabRequest(selectedLabRequest.value.id, updateData);
    labRequests.value = await getLabRequests();
    closeSelectLabPopup();
    selectedLaboratory.value = "";
  } catch (error) {
    console.error("Error updating lab request:", error);
    alert("Failed to assign laboratory");
  }
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

// Function to open select laboratory popup
const openSelectLabPopup = (labRequest) => {
  selectedLabRequest.value = labRequest;
  showSelectLabPopup.value = true;
  searchCity.value = ""; // Reset search when opening modal
};

// Function to close select laboratory popup
const closeSelectLabPopup = () => {
  showSelectLabPopup.value = false;
  selectedLaboratory.value = "";
  searchCity.value = "";
};
</script>

<template>
  <UserLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 xl:px-10 xl:py-12"
    >
      <div class="mx-auto w-full text-center">
        <h3 class="mb-4 text-theme-xl font-semibold text-gray-800 sm:text-2xl">
          Lab Requests
        </h3>
      </div>

      <div
        class="ark:border-gray-800 ark:bg-white/[0.03] overflow-hidden rounded-xl border border-gray-200 bg-white"
      >
        <!-- Status Filter -->
        <div class="border-b border-gray-200 p-4">
          <h3 class="mb-2 text-sm font-medium text-gray-700">
            Filter by Status
          </h3>
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
                  <p class="text-theme-xs font-medium text-gray-500">
                    Doctor Name
                  </p>
                </th>
                <th class="w-2/11 px-5 py-3 text-center sm:px-6">
                  <p class="text-theme-xs font-medium text-gray-500">
                    Test Type
                  </p>
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
              <tr v-if="filteredLabRequests?.length === 0">
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
                          'ark:bg-info-500/15 ark:text-info-500 bg-blue-50 text-blue-700':
                            labRequest.status === 'pending',
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
                    v-if="labRequest.status === 'pending'"
                    @click="openSelectLabPopup(labRequest)"
                    class="cursor-pointer rounded bg-blue-200 p-1 py-2 text-center text-theme-sm font-bold text-blue-700 transition-colors duration-200 hover:bg-blue-300"
                  >
                    Select Laboratory
                  </p>
                  <p
                    v-else
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

        <!-- Select Laboratory Modal -->
        <Modal
          v-if="showSelectLabPopup"
          @close="closeSelectLabPopup"
          :fullScreenBackdrop="true"
        >
          <template #body>
            <div
              class="relative max-h-[600px] w-full max-w-[700px] overflow-y-auto overflow-x-hidden rounded-3xl bg-white p-4 lg:p-8"
            >
              <h5
                class="modal-title mb-4 text-theme-xl font-semibold text-gray-800 lg:text-2xl"
              >
                Select Laboratory
              </h5>
              <p class="mb-6 text-sm text-gray-500">
                Choose a laboratory to process this lab request
              </p>

              <div v-if="selectedLabRequest" class="mb-6">
                <div class="rounded-lg bg-gray-50 p-4">
                  <h6 class="mb-2 font-medium text-gray-800">
                    Lab Request Details:
                  </h6>
                  <p class="text-sm text-gray-600">
                    <strong>Doctor:</strong>
                    {{ selectedLabRequest.consultation?.doctor?.name || "N/A" }}
                  </p>
                  <p class="text-sm text-gray-600">
                    <strong>Test Type:</strong>
                    {{ selectedLabRequest.test_type }}
                  </p>
                  <p class="text-sm text-gray-600">
                    <strong>Requested Date:</strong>
                    {{
                      new Date(
                        selectedLabRequest.created_at,
                      ).toLocaleDateString()
                    }}
                  </p>
                </div>
              </div>

              <!-- Local Laboratories (Always shown) -->
              <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Laboratories in Your City ({{ getUserCity() || "Unknown" }}) *
                </label>
                <select
                  v-model="selectedLaboratory"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  required
                >
                  <option value="" disabled>
                    Choose a laboratory in your city...
                  </option>
                  <option
                    v-for="lab in userCityLaboratories"
                    :key="lab.id"
                    :value="lab.id"
                  >
                    {{ lab.name }}
                  </option>
                </select>
                <p
                  v-if="userCityLaboratories.length === 0"
                  class="mt-2 text-sm text-red-500"
                >
                  No active laboratories found in your city ({{
                    getUserCity() || "Unknown"
                  }})
                </p>
              </div>

              <!-- Search Section (Separate from select) -->
              <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Search Laboratories in Other Cities
                </label>
                <div class="relative">
                  <input
                    v-model="searchCity"
                    type="text"
                    placeholder="Enter city name to search..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                  <div
                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                  >
                    <svg
                      class="h-4 w-4 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                      ></path>
                    </svg>
                  </div>
                </div>

                <!-- Search Results (Separate div) -->
                <div v-if="searchCity" class="mt-3">
                  <p class="mb-2 text-sm font-medium text-gray-700">
                    Search Results for "{{ searchCity }}":
                  </p>
                  <div
                    v-if="searchResults.length > 0"
                    class="max-h-32 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50"
                  >
                    <div
                      v-for="lab in searchResults"
                      :key="lab.id"
                      @click="selectedLaboratory = lab.id"
                      class="cursor-pointer border-b border-gray-200 p-3 last:border-b-0 hover:bg-blue-50"
                      :class="{
                        'border-blue-300 bg-blue-100':
                          selectedLaboratory === lab.id,
                      }"
                    >
                      <div class="flex items-center justify-between">
                        <div>
                          <p class="font-medium text-gray-800">
                            {{ lab.name }}
                          </p>
                          <p class="text-sm text-gray-600">{{ lab.city }}</p>
                        </div>
                        <div
                          v-if="selectedLaboratory === lab.id"
                          class="text-blue-600"
                        >
                          <svg
                            class="h-5 w-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                  <p v-else class="mt-2 text-sm text-red-500">
                    No laboratories found in "{{ searchCity }}"
                  </p>
                  <button
                    @click="searchCity = ''"
                    class="mt-2 text-xs text-blue-600 underline hover:no-underline"
                  >
                    Clear search
                  </button>
                </div>
              </div>

              <!-- Selected Laboratory Display -->
              <div v-if="selectedLaboratory" class="mb-6">
                <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                  <p class="text-sm font-medium text-green-800">
                    Selected Laboratory:
                  </p>
                  <p class="text-sm text-green-700">
                    {{
                      laboratories.find((lab) => lab.id === selectedLaboratory)
                        ?.name
                    }}
                    -
                    {{
                      laboratories.find((lab) => lab.id === selectedLaboratory)
                        ?.city
                    }}
                  </p>
                </div>
              </div>

              <div class="flex justify-end space-x-3">
                <button
                  @click="closeSelectLabPopup"
                  type="button"
                  class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  @click="handleSelectLaboratory"
                  type="button"
                  class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="!selectedLaboratory"
                >
                  Assign Laboratory
                </button>
              </div>
            </div>
          </template>
        </Modal>
      </div>
    </div>
  </UserLayout>
</template>
