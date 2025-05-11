<script setup>
import { onMounted, ref } from "vue";
import Modal from "@/components/UI/Modal.vue";
import { useLabRequestStore } from "@/stores/labRequestStore";
import { storeToRefs } from "pinia";
import { useLabResultStore } from "@/stores/labResultStore";

const { getLabRequests } = useLabRequestStore();
const { createLabResult } = useLabResultStore();
const labRequests = ref([]);
const errors = ref(storeToRefs(useLabResultStore()));

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

onMounted(async () => {
  labRequests.value = await getLabRequests();
  console.log(labRequests.value);
});

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
          <tr v-if="labRequests.length === 0">
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
                  No lab requests found.
                </p>
              </div>
            </td>
          </tr>
          <tr
            v-for="(labRequest, index) in labRequests"
            :key="index"
            class="ark:border-gray-800 border-t border-gray-100"
          >
            <td class="px-5 py-4 sm:px-6">
              <p class="text-theme-sm text-gray-500">
                {{ labRequest.consultation?.patient?.name || "N/A" }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="flex justify-center -space-x-2">
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
      <template #body>
        <div
          class="relative max-h-[700px] w-full max-w-[700px] overflow-y-auto overflow-x-hidden rounded-3xl bg-white p-4 lg:p-11"
        >
          <h5
            class="modal-title mb-2 text-theme-xl font-semibold text-gray-800 lg:text-2xl"
          >
            More Details
          </h5>
          <p class="text-sm text-gray-500">Review lab request information</p>

          <div v-if="selectedLabRequest" class="mt-8">
            <!-- Basic Info Section -->
            <div class="flex flex-col gap-6 lg:items-start lg:justify-between">
              <div class="">
                <h4
                  class="text-left text-lg font-semibold text-gray-800 lg:mb-6"
                >
                  Patient's Information
                </h4>

                <div
                  class="grid grid-cols-1 gap-4 text-left lg:grid-cols-2 lg:gap-7 2xl:gap-x-32"
                >
                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Name
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.consultation.patient.name }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">Age</p>
                    <p class="text-sm font-medium text-gray-800">
                      {{
                        selectedLabRequest.consultation.patient.birth_date
                          ? new Date().getFullYear() -
                            new Date(
                              selectedLabRequest.consultation.patient.birth_date,
                            ).getFullYear()
                          : "N/A"
                      }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Gender
                    </p>
                    <p class="text-sm font-medium capitalize text-gray-800">
                      {{ selectedLabRequest.consultation.patient.gender }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Email address
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.consultation.patient.email }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Phone
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.consultation.patient.phone }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="">
                <h4
                  class="text-left text-lg font-semibold text-gray-800 lg:mb-6"
                >
                  Doctor's Information
                </h4>

                <div
                  class="grid grid-cols-1 gap-4 text-left lg:grid-cols-2 lg:gap-7 2xl:gap-x-32"
                >
                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Doctor Name
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.consultation.doctor.name }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Email address
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.consultation.doctor.email }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Phone
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.consultation.doctor.phone }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="">
                <h4
                  class="text-left text-lg font-semibold text-gray-800 lg:mb-6"
                >
                  Lab Request Details
                </h4>

                <div
                  class="grid grid-cols-1 gap-4 text-left lg:grid-cols-2 lg:gap-7 2xl:gap-x-32"
                >
                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Test Type
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{ selectedLabRequest.test_type }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Status
                    </p>
                    <p class="text-sm font-medium capitalize text-gray-800">
                      {{ selectedLabRequest.status }}
                    </p>
                  </div>

                  <div>
                    <p class="mb-2 text-xs leading-normal text-gray-500">
                      Requested Date
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      {{
                        new Date(
                          selectedLabRequest.created_at,
                        ).toLocaleDateString("en-GB")
                      }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Action buttons -->
            <div class="flex justify-end gap-x-6">
              <div
                class="modal-footer mt-8 flex items-center gap-3 sm:justify-end"
              >
                <button
                  @click="openSendResultPopup()"
                  class="ark:border-gray-700 ark:bg-gray-800 ark:hover:bg-white/[0.03] flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-success-50 hover:text-success-700 sm:w-auto"
                >
                  Send Result
                </button>
              </div>
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
        </div>
      </template>
    </Modal>

    <!-- Send Result Modal -->
    <Modal
      v-if="showSendResultPopup"
      @close="closeSendResultPopup"
      :fullScreenBackdrop="true"
    >
      <template #body>
        <div
          class="relative max-h-[700px] w-full max-w-[700px] overflow-y-auto overflow-x-hidden rounded-3xl bg-white p-4 lg:p-11"
        >
          <h5
            class="modal-title mb-2 text-theme-xl font-semibold text-gray-800 lg:text-2xl"
          >
            Send Lab Result
          </h5>
          <p class="text-sm text-gray-500">
            Enter the lab result details and send to the patient.
          </p>

          <div v-if="selectedLabRequest" class="mt-8">
            <!-- Form for sending lab result -->
            <div>
              <label
                for="result_details"
                class="mb-1.5 block text-left text-sm font-medium text-gray-700"
              >
                Result Details
              </label>
              <textarea
                v-model="labResultData.result_details"
                id="result_details"
                rows="4"
                class="ark:bg-gray-800 block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Enter result details here..."
              ></textarea>
            </div>

            <div class="mt-4">
              <label
                for="attachment"
                class="mb-1.5 block text-left text-sm font-medium text-gray-700"
              >
                Upload Attachment <span class="text-red-500">*</span>
              </label>
              <div
                :onclick="uploadAttachment"
                class="mt-1 flex h-11 w-1/2 cursor-pointer justify-center rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 hover:border-2 focus:border-first-accent"
              >
                Click to upload
              </div>
              <p
                v-if="isFileUploaded"
                class="mt-4 text-left font-semibold text-success-400"
              >
                Attachment Uploaded Successfully
              </p>
              <p
                v-if="errors?.attachment"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.attachment }}
              </p>
            </div>

            <!-- Action buttons -->
            <div
              class="modal-footer mt-8 flex items-center gap-3 sm:justify-end"
            >
              <button
                @click="closeSendResultPopup"
                class="ark:border-gray-700 ark:bg-gray-800 ark:hover:bg-white/[0.03] flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
              >
                Cancel
              </button>
              <button
                @click="handleSubmit"
                class="ark:border-gray-700 ark:bg-gray-800 ark:hover:bg-white/[0.03] flex w-full justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 sm:w-auto"
              >
                Send
              </button>
            </div>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>
