<script setup>
import { useLaboratoryStore } from "@/stores/laboratoryStore";
import { storeToRefs } from "pinia";
import { ref } from "vue";

const labData = ref({
  name: "",
  email: "",
  password: "",
  phone: "",
  address: "",
  tests: {
    "Complete Blood Count": {
      description:
        "Measures red and white blood cells, hemoglobin, and platelets.",
      provided: false,
    },
    "Basic Metabolic Panel": {
      description: "Tests blood sugar, electrolytes, and kidney function.",
      provided: false,
    },
    "Comprehensive Metabolic Panel": {
      description: "Includes liver function tests like albumin and bilirubin.",
      provided: false,
    },
    "Lipid Panel": {
      description: "Measures cholesterol levels to assess cardiovascular risk.",
      provided: false,
    },
    "Hemoglobin A1c": {
      description: "Monitors average blood sugar levels over 2-3 months.",
      provided: false,
    },
    "Thyroid Function Tests": {
      description: "Measures TSH, T3, and T4 to evaluate thyroid activity.",
      provided: false,
    },
    "Liver Function Tests": {
      description: "Assesses liver health through enzymes like ALT and AST.",
      provided: false,
    },
    "Kidney Function Tests": {
      description: "Evaluates kidney performance by measuring waste products.",
      provided: false,
    },
    Urinalysis: {
      description: "Analyzes urine for signs of infection or kidney disease.",
      provided: false,
    },
    "C-Reactive Protein": {
      description: "Measures inflammation levels in the body.",
      provided: false,
    },
    "Erythrocyte Sedimentation Rate": {
      description: "Assesses inflammation by red blood cell settling rate.",
      provided: false,
    },
    "Blood Glucose Test": {
      description: "Checks blood sugar levels to diagnose diabetes.",
      provided: false,
    },
    "Prothrombin Time": {
      description: "Evaluates blood clotting ability.",
      provided: false,
    },
    "Activated Partial Thromboplastin Time": {
      description: "Assesses blood clotting pathways.",
      provided: false,
    },
    "Electrolyte Panel": {
      description: "Measures sodium, potassium, chloride, and bicarbonate.",
      provided: false,
    },
    "Iron Studies": {
      description: "Evaluates iron levels to diagnose anemia.",
      provided: false,
    },
    "Vitamin D Test": {
      description: "Measures vitamin D levels for bone health.",
      provided: false,
    },
    "Blood Culture": {
      description: "Identifies bacteria or fungi in the blood.",
      provided: false,
    },
    "Stool Occult Blood Test": {
      description: "Detects hidden blood in stool for GI issues.",
      provided: false,
    },
    "Serum Uric Acid Test": {
      description: "Measures uric acid levels to diagnose gout.",
      provided: false,
    },
  },
});

const { createLaboratory } = useLaboratoryStore();

const { errors } = storeToRefs(useLaboratoryStore());

const submitForm = async () => {
  createLaboratory(labData.value);
};
</script>

<template>
  <div
    class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-12 sm:px-6 lg:px-8"
  >
    <div class="w-full max-w-3xl rounded-lg bg-white p-8 shadow-lg">
      <h1 class="mb-6 text-center text-3xl font-bold text-gray-800">
        Register Your Laboratory
      </h1>
      <p class="text-red-100" v-if="errors">Error Found</p>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700"
              >Laboratory Name</label
            >
            <input
              id="name"
              v-model="labData.name"
              type="text"
              class="mt-1 block w-full rounded-md border border-gray-300 p-3 focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter laboratory name"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700"
              >Email</label
            >
            <input
              id="email"
              v-model="labData.email"
              type="email"
              class="mt-1 block w-full rounded-md border border-gray-300 p-3 focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter email"
              required
            />
          </div>

          <!-- Password -->
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700"
              >Password</label
            >
            <input
              id="password"
              v-model="labData.password"
              type="password"
              class="mt-1 block w-full rounded-md border border-gray-300 p-3 focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter password"
              required
            />
          </div>

          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700"
              >Phone</label
            >
            <input
              id="phone"
              v-model="labData.phone"
              type="tel"
              class="mt-1 block w-full rounded-md border border-gray-300 p-3 focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter phone number"
            />
          </div>
        </div>

        <!-- Address -->
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700"
            >Address</label
          >
          <textarea
            id="address"
            v-model="labData.address"
            class="mt-1 block w-full rounded-md border border-gray-300 p-3 focus:border-blue-500 focus:ring-blue-500"
            placeholder="Enter address"
            rows="3"
          ></textarea>
        </div>

        <!-- Tests Selection -->
        <div>
          <h2 class="mb-4 text-xl font-semibold text-gray-800">
            Select Tests Provided
          </h2>
          <div
            class="grid max-h-96 grid-cols-1 gap-4 overflow-y-auto rounded-md border border-gray-200 p-4 md:grid-cols-2"
          >
            <div
              v-for="(test, name) in labData.tests"
              :key="name"
              class="flex items-start space-x-2"
            >
              <input
                type="checkbox"
                :id="name"
                v-model="test.provided"
                class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-500 focus:ring-blue-500"
              />
              <label :for="name" class="text-sm text-gray-700">
                <span class="font-medium">{{ name }}</span>
                <p class="text-xs text-gray-500">{{ test.description }}</p>
              </label>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
          <button
            type="submit"
            class="rounded-md bg-blue-500 px-6 py-3 text-lg font-medium text-white transition duration-300 hover:bg-yellow-500 hover:text-gray-800"
          >
            Register Laboratory
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
/* No custom styles needed, Tailwind CSS classes are used */
</style>
