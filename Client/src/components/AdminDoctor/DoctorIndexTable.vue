<script setup>
import { useDoctorStore } from "@/stores/doctorStore";
import { onMounted, ref, computed } from "vue";
import Modal from "@/components/UI/Modal.vue";

const { getDoctors, updateDoctorStatus } = useDoctorStore();
const doctors = ref([]);

const statusOptions = ["active", "pending", "suspended", "expired"];
const selectedStatus = ref("all"); // Default to 'all'

onMounted(async () => {
  doctors.value = await getDoctors();
  console.log(doctors.value);
});

const handleStatusUpdate = async (status, doctorId) => {
  await updateDoctorStatus(status, doctorId);
  doctors.value = await getDoctors();
  closePopup();
};

const filteredDoctors = computed(() => {
  if (selectedStatus.value === "all") {
    return doctors.value;
  }
  return doctors.value.filter((doc) => doc.status === selectedStatus.value);
});

// Function to change the selected status
const changeStatus = (status) => {
  selectedStatus.value = status;
};

// Popup state management
const showPopup = ref(false);
const selectedDoctor = ref(null);

// Function to open popup with doctor details
const openDetailPopup = (doctor) => {
  selectedDoctor.value = doctor;
  console.log(selectedDoctor.value);
  showPopup.value = true;
};

// Function to close popup
const closePopup = () => {
  showPopup.value = false;
};

// Computed properties for accessing nested data
const decodedLocation = computed(() => {
  if (selectedDoctor.value && selectedDoctor.value.location) {
    try {
      return typeof selectedDoctor.value.location === "string"
        ? JSON.parse(selectedDoctor.value.location)
        : selectedDoctor.value.location;
    } catch (e) {
      console.error("Error parsing location:", e);
      return {};
    }
  }
  return {};
});

// Function to download certificate
const downloadCertificate = () => {
  if (selectedDoctor.value && selectedDoctor.value.certificate_path) {
    const originalUrl = selectedDoctor.value.certificate_path;

    if (originalUrl.includes("cloudinary.com")) {
      const uploadIndex = originalUrl.indexOf("/upload/");
      if (uploadIndex !== -1) {
        const downloadUrl =
          originalUrl.substring(0, uploadIndex + 8) +
          "fl_attachment/" +
          originalUrl.substring(uploadIndex + 8);
        window.open(downloadUrl, "_blank");
        return;
      }
    }
    window.open(originalUrl, "_blank");
  }
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
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Doctor Name</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Email</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Phone</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Specialty</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Region</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Status</p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p class="text-theme-xs font-medium text-gray-500">Action</p>
            </th>
          </tr>
        </thead>
        <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
          <!-- No results message -->
          <tr v-if="filteredDoctors.length === 0">
            <td colspan="7" class="px-5 py-8 text-center sm:px-6">
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
                  No doctors found with status:
                  <span class="font-medium capitalize">{{
                    selectedStatus
                  }}</span>
                </p>
                <button
                  @click="changeStatus('all')"
                  class="mt-4 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Show All Doctors
                </button>
              </div>
            </td>
          </tr>
          <tr
            v-for="(doctor, index) in filteredDoctors"
            :key="index"
            class="ark:border-gray-800 border-t border-gray-100"
          >
            <td class="px-5 py-4 sm:px-6">
              <div class="flex items-center gap-3">
                <div>
                  <span class="block text-theme-sm font-medium text-gray-800">
                    {{ doctor.user.name }}
                  </span>
                </div>
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                {{ doctor.user.email }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                {{ doctor.user.phone }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                {{ doctor.specialty }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                {{ doctor.user.region }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                <span
                  :class="[
                    'rounded-full px-2 py-0.5 text-theme-xs font-medium',
                    {
                      'ark:bg-success-500/15 ark:text-success-500 bg-success-50 text-success-700':
                        doctor.status === 'active',
                      'ark:bg-warning-500/15 ark:text-warning-400 bg-warning-50 text-warning-700':
                        doctor.status === 'pending',
                      'ark:bg-error-500/15 ark:text-error-500 bg-error-50 text-error-700':
                        doctor.status === 'suspended' ||
                        doctor.status === 'expired',
                    },
                  ]"
                >
                  {{ doctor.status }}</span
                >
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p
                @click="openDetailPopup(doctor)"
                class="cursor-pointer rounded bg-gray-200 p-1 py-2 text-center text-theme-sm font-bold text-gray-500 transition-colors duration-200 hover:bg-success-50 hover:text-success-700"
              >
                More Detail
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Doctor Details -->
    <Modal v-if="showPopup" @close="closePopup" :fullScreenBackdrop="true">
      <template #body>
        <div
          class="relative max-h-[700px] w-full max-w-[700px] overflow-y-auto overflow-x-hidden rounded-3xl bg-white p-4 lg:p-11"
        >
          <h5
            class="modal-title mb-2 text-theme-xl font-semibold text-gray-800 lg:text-2xl"
          >
            Doctor Details
          </h5>
          <p class="text-sm text-gray-500">
            Review doctor information and credentials
          </p>

          <div v-if="selectedDoctor" class="mt-8">
            <!-- Basic Info Section -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Doctor Name
                </label>
                <p class="text-base text-gray-800">
                  {{ selectedDoctor.user.name }}
                </p>

                <div class="mt-4">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Email Address
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.user.email }}
                  </p>
                </div>

                <div class="mt-4">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Phone Number
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.user.phone || "Not provided" }}
                  </p>
                </div>

                <div class="mt-4">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Medical License
                  </label>
                  <button
                    v-if="selectedDoctor.certificate_path"
                    @click="downloadCertificate"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path
                        d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                      ></path>
                      <polyline points="7 10 12 15 17 10"></polyline>
                      <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Certificate
                  </button>
                  <p v-else class="text-base italic text-gray-500">
                    No certificate available
                  </p>
                </div>
              </div>

              <!-- Professional Info Section -->
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Specialty
                </label>
                <p class="text-base text-gray-800">
                  {{ selectedDoctor.specialty || "Not provided" }}
                </p>

                <div class="mt-4">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Qualification
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.qualification || "Not provided" }}
                  </p>
                </div>

                <div class="mt-4">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Experience (Years)
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.experience_years || "Not provided" }}
                  </p>
                </div>

                <div class="mt-4">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    University Attended
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.university_attended || "Not provided" }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Location and License Info Section -->
            <div class="mt-6">
              <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Location
              </label>
              <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Region
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.user.region || "Not provided" }}
                  </p>
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    City
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.user.city || "Not provided" }}
                  </p>
                </div>
                <div
                  v-if="decodedLocation.lat && decodedLocation.lng"
                  class="mt-4"
                >
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Coordinates
                  </label>
                  <p class="text-base text-gray-800">
                    Lat: {{ decodedLocation.lat }}, Long:
                    {{ decodedLocation.lng }}
                  </p>
                </div>
              </div>
            </div>

            <!-- License Dates and Status -->
            <div class="mt-6">
              <label class="mb-1.5 block text-sm font-medium text-gray-700">
                License Details
              </label>
              <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    License Number
                  </label>
                  <p class="text-base text-gray-800">
                    {{
                      selectedDoctor.medical_license_number || "Not provided"
                    }}
                  </p>
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Issue Date
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.license_issue_date || "Not provided" }}
                  </p>
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Expiry Date
                  </label>
                  <p class="text-base text-gray-800">
                    {{ selectedDoctor.license_expiry_date || "Not provided" }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Status and Actions -->
            <div class="mt-6">
              <div class="flex items-end gap-x-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Status
                </label>
                <span
                  :class="[
                    'rounded-xl border px-3 py-1 text-sm font-medium',
                    {
                      'bg-success-50 text-success-700':
                        selectedDoctor.status === 'active',
                      'bg-warning-50 text-warning-700':
                        selectedDoctor.status === 'pending',
                      'bg-error-50 text-error-700':
                        selectedDoctor.status === 'suspended' ||
                        selectedDoctor.status === 'expired',
                    },
                  ]"
                >
                  {{ selectedDoctor.status }}
                </span>
              </div>
              <div class="mt-4">
                <span class="mb-1.5 block text-sm font-medium text-gray-700">
                  Handle The Request
                </span>
                <div class="flex items-center gap-3">
                  <button
                    v-if="selectedDoctor.status != 'active'"
                    @click="handleStatusUpdate('active', selectedDoctor.id)"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition-all duration-300 ease-linear hover:bg-gray-50 hover:text-success-600 hover:ring-success-600"
                  >
                    Approve
                  </button>
                  <button
                    v-if="selectedDoctor.status === 'active'"
                    @click="handleStatusUpdate('suspended', selectedDoctor.id)"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition-all duration-300 ease-linear hover:bg-gray-50 hover:text-success-600 hover:ring-success-600"
                  >
                    Suspend
                  </button>
                  <button
                    v-if="selectedDoctor.status != 'expired'"
                    @click="handleStatusUpdate('expired', selectedDoctor.id)"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition-all duration-300 ease-linear hover:bg-gray-50 hover:text-error-600 hover:ring-error-600"
                  >
                    Decline
                  </button>
                  <button
                    v-if="selectedDoctor.status === 'expired'"
                    @click="handleStatusUpdate('pending', selectedDoctor.id)"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition-all duration-300 ease-linear hover:bg-gray-50 hover:text-error-600 hover:ring-error-600"
                  >
                    Pending
                  </button>
                </div>
              </div>
            </div>

            <!-- Action buttons -->
            <div
              class="modal-footer mt-8 flex items-center gap-3 sm:justify-end"
            >
              <button
                @click="closePopup"
                class="ark:border-gray-700 ark:bg-gray-800 ark:hover:bg-white/[0.03] flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>
