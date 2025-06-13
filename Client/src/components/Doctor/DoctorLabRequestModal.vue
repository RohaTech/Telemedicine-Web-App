<script setup>
import { ref, computed } from "vue";
import { useLabRequestStore } from "@/stores/labRequestStore";

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

// Store
const { createLabRequest } = useLabRequestStore();

// Form data
const labRequestData = ref({
  consultation_id: props.consultationId,
  patient_id: props.patientId,
  doctor_id: props.doctorId,
  test_type: "",
  status: "pending",
});

// Debug log to check props
console.log('DoctorLabRequestModal props:', {
  consultationId: props.consultationId,
  patientId: props.patientId,
  doctorId: props.doctorId
});

// Loading state
const isSubmitting = ref(false);

// Common lab test types
const testTypes = [
  "Complete Blood Count (CBC)",
  "Blood Sugar Test",
  "Lipid Profile",
  "Liver Function Test",
  "Kidney Function Test",
  "Thyroid Function Test",
  "Urine Analysis",
  "Electrolyte Panel",
  "Cardiac Markers",
  "Inflammatory Markers",
  "Vitamin D Test",
  "Hemoglobin A1C",
  "Cholesterol Test",
  "Blood Pressure",
  "X-Ray",
  "CT Scan",
  "MRI",
  "Ultrasound",
  "ECG",
  "Other",
];

// Form validation
const isFormValid = computed(() => {
  return labRequestData.value.test_type.trim() !== "";
});

// Handle form submission
const handleSubmit = async () => {
  if (!isFormValid.value) return;

  isSubmitting.value = true;

  // Debug log to see what data is being sent
  console.log('Submitting lab request data:', labRequestData.value);

  try {
    const result = await createLabRequest(labRequestData.value);

    if (result.success) {
      emit("success");
      emit("close");
    } else {
      // Handle validation errors
      console.error('Lab request creation failed:', result);
      if (result.errors) {
        const errorMessages = Object.values(result.errors).flat().join('\n');
        alert(`Validation errors:\n${errorMessages}`);
      } else {
        alert(result.error || "Failed to create lab request. Please try again.");
      }
    }
  } catch (error) {
    console.error("Error creating lab request:", error);
    alert("An unexpected error occurred. Please try again.");
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

        <!-- Custom Test Type (if Other is selected) -->
        <div v-if="labRequestData.test_type === 'Other'">
          <label class="mb-2 block text-sm font-medium text-gray-700">
            Specify Test Type *
          </label>
          <input
            v-model="labRequestData.test_type"
            type="text"
            placeholder="Enter specific test type..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            required
          />
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
