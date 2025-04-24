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
  license: "",
  region: "",
  city: "",
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
    class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8"
  >
    <div
      class="w-full max-w-4xl rounded-lg border border-gray-50 bg-[#f3f5f5] p-8 drop-shadow-md"
    >
      <h1 class="mb-1 text-center text-3xl font-bold text-[#0F172A]">
        Register Your Laboratory
      </h1>
      <p class="mb-2 text-center text-lg text-first-accent">
        Join Tenadam: Provide Healthcare Services
      </p>
      <p class="mb-8 mt-3 text-center text-xs text-[#64748B]">
        FILL YOUR CREDENTIALS BELOW CORRECTLY
      </p>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-10">
        <!-- Basic Info -->
        <div>
          <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
            Basic Information
          </h2>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Name -->
            <div>
              <label for="name" class="mb-1 block text-sm text-[#0F172A]">
                Laboratory Name <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="labData.name"
                type="text"
                class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                placeholder="Enter laboratory name"
                required
              />
              <p
                v-if="errors?.name"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.name }}
              </p>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="mb-1 block text-sm text-[#0F172A]">
                Email <span class="text-red-500">*</span>
              </label>
              <input
                id="email"
                v-model="labData.email"
                type="email"
                class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                placeholder="Enter email"
                required
              />
              <p
                v-if="errors?.email"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.email }}
              </p>
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="mb-1 block text-sm text-[#0F172A]">
                Password <span class="text-red-500">*</span>
              </label>
              <input
                id="password"
                v-model="labData.password"
                type="password"
                class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                placeholder="Enter password"
                required
              />
              <p
                v-if="errors?.password"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.password }}
              </p>
            </div>

            <!-- Phone -->
            <div>
              <label for="phone" class="mb-1 block text-sm text-[#0F172A]">
                Phone <span class="text-red-500">*</span>
              </label>
              <input
                id="phone"
                v-model="labData.phone"
                type="tel"
                class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                placeholder="Enter phone number"
                required
              />
              <p
                v-if="errors?.phone"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.phone }}
              </p>
            </div>

            <!-- License Number -->
            <div>
              <label for="license" class="mb-1 block text-sm text-[#0F172A]">
                Upload Your License <span class="text-red-500">*</span>
              </label>
              <div
                class="flex h-11 w-1/2 cursor-pointer justify-center rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 hover:border-2 focus:border-first-accent"
              >
                Click to upload
              </div>
              <p
                v-if="errors?.license"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.license }}
              </p>
            </div>
          </div>
        </div>

        <!-- Location -->
        <div>
          <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">Location</h2>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Region -->
            <div>
              <label for="region" class="mb-1 block text-sm text-[#0F172A]">
                Region <span class="text-red-500">*</span>
              </label>
              <select
                id="region"
                v-model="labData.region"
                class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                required
              >
                <option value="" disabled selected>Select an option</option>
                <option value="addis_abeba">Addis Abeba</option>
                <option value="Tigray">Tigray</option>
                <option value="Afar">Afar</option>
                <option value="Amhara">Amhara</option>
                <option value="Oromia">Oromia</option>
                <option value="Somali">Somali</option>
                <option value="Benishangul-Gumuz">Benishangul-Gumuz</option>
                <option value="SNNPR">SNNPR</option>
                <option value="Gambella">Gambella</option>
                <option value="Harari">Harari</option>
                <option value="Sidama">Sidama</option>
                <option value="Dire Dawa">Dire Dawa</option>
              </select>
              <p
                v-if="errors?.region"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.region }}
              </p>
            </div>

            <!-- City -->
            <div>
              <label for="city" class="mb-1 block text-sm text-[#0F172A]">
                City <span class="text-red-500">*</span>
              </label>
              <input
                id="city"
                v-model="labData.city"
                type="text"
                class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                placeholder="Enter city"
                required
              />
              <p
                v-if="errors?.city"
                class="mt-2 text-xs font-semibold text-red-500"
              >
                {{ errors.city }}
              </p>
            </div>

            <!-- Location (Optional) -->
            <div class="md:col-span-2">
              <label class="mb-1 block text-sm text-[#0F172A]">
                Map Location
                <span class="ml-1 text-xs text-gray-500"
                  >(Optional - Click on map to set location)</span
                >
              </label>
              <div class="h-64 rounded-lg border border-gray-300 bg-gray-100">
                <!-- Map component would go here -->
                <p
                  class="flex h-full items-center justify-center text-sm text-gray-500"
                >
                  Map component will be integrated here
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tests Selection -->
        <div>
          <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
            Select Tests Provided
          </h2>
          <div
            class="grid max-h-96 grid-cols-1 gap-4 overflow-y-auto rounded-lg border border-gray-300 p-4 md:grid-cols-2"
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
                class="mt-1 h-5 w-5 rounded border-gray-300 text-[#26C6DA] focus:ring-first-accent"
              />
              <label :for="name" class="text-sm text-[#0F172A]">
                <span class="font-medium">{{ name }}</span>
                <p class="text-xs text-gray-500">{{ test.description }}</p>
              </label>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center gap-x-16">
          <button
            type="submit"
            class="flex w-[158px] items-center justify-around rounded-3xl bg-green-500 py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-green-700"
          >
            Register
            <div
              class="flex size-8 items-center justify-center rounded-full bg-green-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-5 fill-white"
                viewBox="0 0 256 256"
              >
                <path
                  d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"
                ></path>
              </svg>
            </div>
          </button>
          <button
            type="button"
            @click="() => $router.push('/')"
            class="flex w-[158px] items-center justify-around rounded-3xl bg-red-500 py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-red-700"
          >
            Cancel
            <div
              class="flex size-8 items-center justify-center rounded-full bg-red-700"
            >
              <svg
                class="size-5 fill-white"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 384 512"
              >
                <path
                  d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"
                />
              </svg>
            </div>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
