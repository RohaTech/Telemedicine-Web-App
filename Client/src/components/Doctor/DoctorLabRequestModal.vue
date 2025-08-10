<script setup>
import { ref, computed } from "vue";
import { useLabRequestStore } from "@/stores/labRequestStore";
import { useToast } from "vue-toastification";

// Props
const props = defineProps({
  consultationId: {
    type: [String, Number],
    required: true,
  },
  patientId: {
    type: [String, Number],
    required: true,
  },
  doctorId: {
    type: [String, Number],
    required: true,
  },
});

// Emits
const emit = defineEmits(["close", "success"]);

// Store and Toast
const { createLabRequest } = useLabRequestStore();
const toast = useToast();

// Form data
const labRequestData = ref({
  consultation_id: props.consultationId,
  test_type: "",
  status: "pending",
});

// Loading state
const isSubmitting = ref(false);

// Updated test types to match the controller
const testTypes = [
  "Complete Blood Count",
  "Basic Metabolic Panel",
  "Comprehensive Metabolic Panel",
  "Lipid Panel",
  "Hemoglobin A1c",
  "Thyroid Function Tests",
  "Liver Function Tests",
  "Kidney Function Tests",
  "Urinalysis",
  "C-Reactive Protein",
  "Erythrocyte Sedimentation Rate",
  "Blood Glucose Test",
  "Prothrombin Time",
  "Activated Partial Thromboplastin Time",
  "Electrolyte Panel",
  "Iron Studies",
  "Vitamin D Test",
  "Blood Culture",
  "Stool Occult Blood Test",
  "Serum Uric Acid Test",
];

// Form validation
const isFormValid = computed(() => {
  return labRequestData.value.test_type.trim() !== "";
});

// Handle form submission
const handleSubmit = async () => {
  if (!isFormValid.value) {
    toast.warning("Please select a test type before submitting.", {
      position: "top-right",
      timeout: 3000,
    });
    return;
  }

  console.log(labRequestData.value);

  isSubmitting.value = true;
  try {
    await createLabRequest(labRequestData.value);

    toast.success("Lab request created successfully!", {
      position: "top-right",
      timeout: 5000,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: false,
      closeButton: "button",
      icon: true,
      rtl: false,
    });

    

    emit("success");
    emit("close");
  } catch (error) {
    console.error("Error creating lab request:", error);

    toast.error("Failed to create lab request. Please try again.", {
      position: "top-right",
      timeout: 5000,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: false,
      closeButton: "button",
      icon: true,
      rtl: false,
    });
  } finally {
    isSubmitting.value = false;
  }
};

// Handle modal close
const handleClose = () => {
  emit("close");
};
</script>

<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div
      class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg bg-white p-6 shadow-xl"
    >
      <!-- Modal Header -->
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Request Lab Test</h2>
        <button
          @click="handleClose"
          class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
        >
          <svg
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Modal Body -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Test Type Selection -->
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">
            Test Type *
          </label>
          <select
            v-model="labRequestData.test_type"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            required
          >
            <option value="" disabled>Select a test type...</option>
            <option
              v-for="testType in testTypes"
              :key="testType"
              :value="testType"
            >
              {{ testType }}
            </option>
          </select>
        </div>

        <!-- Information Note -->
        <div class="rounded-lg bg-blue-50 p-4">
          <div class="flex">
            <svg
              class="h-5 w-5 text-blue-400"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
            <div class="ml-3">
              <p class="text-sm text-blue-700">
                <strong>Note:</strong> After submitting this request, the
                patient will be able to select a laboratory to process the test.
                The lab request will be created with a "pending" status until a
                laboratory is assigned.
              </p>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 pt-4">
          <button
            @click="handleClose"
            type="button"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="!isFormValid || isSubmitting"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {{ isSubmitting ? "Requesting..." : "Request Lab Test" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
