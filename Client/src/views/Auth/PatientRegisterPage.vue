<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { ref } from "vue";
import { useRouter } from "vue-router";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";

const { authenticate } = useAuthStore();
const { errors } = storeToRefs(useAuthStore());

// Form data for patient registration
const patientData = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "patient",
  phone: "",
  birth_date: "",
  gender: "",
  region: "",
  city: "",
  profile_picture: null,
  terms_agreed: false,
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const imagePreviewUrl = ref(null);

// Router for navigation
const router = useRouter();

// Handle form submission
const submitForm = async () => {
  authenticate("register", patientData.value);
  console.log(patientData.value);
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
      patientData.value.profile_picture = result.info.secure_url;
      imagePreviewUrl.value = result.info.secure_url;
    }
  },
);

const uploadImage = () => {
  widget.open();
};

// Handle cancel button
const cancelForm = () => {
  // Reset form or redirect
  router.push("/");
};

// Flatpickr configuration
const flatpickrConfig = {
  dateFormat: "Y-m-d",
  altInput: true,
  altFormat: "F j, Y",
  wrap: true,
};

const clearImage = () => {
  patientData.value.profile_picture = null;
  imagePreviewUrl.value = null;
};
</script>

<template>
  <UserLayout>
    <div class="flex min-h-screen justify-center px-4 py-12 sm:px-6 lg:px-8">
      <div
        class="w-full max-w-[860px] border border-gray-50 bg-[#f3f5f5] p-16 drop-shadow-md"
      >
        <!-- Header -->
        <h1 class="mb-1 text-3xl font-bold text-[#0F172A]">
          PATIENT REGISTRATION
        </h1>
        <p class="mb-2 text-lg text-first-accent">
          Join Tenadam: Access Healthcare Easily
        </p>
        <p class="mb-8 mt-3 text-xs text-[#64748B]">
          FILL YOUR CREDENTIALS BELOW CORRECTLY
        </p>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-10">
          <!-- Credentials Section -->
          <div>
            <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
              Credentials
            </h2>
            <div class="space-y-4">
              <!-- Email -->
              <div>
                <label for="email" class="mb-1 block text-sm text-[#0F172A]">
                  Email <span class="text-red-500">*</span>
                </label>
                <input
                  id="email"
                  v-model="patientData.email"
                  type="email"
                  class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Email"
                  required
                />

                <p
                  v-if="errors.email"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.email }}
                </p>
              </div>

              <!-- Password -->
              <div>
                <label for="password" class="mb-1 block text-sm text-[#0F172A]">
                  Password (Minimum 8 Characters)
                  <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="password"
                    v-model="patientData.password"
                    :type="showPassword ? 'text' : 'password'"
                    class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                    placeholder="Password"
                    required
                    minlength="8"
                  />
                  <p
                    v-if="errors.password"
                    class="mt-2 text-xs font-semibold text-red-500"
                  >
                    {{ errors.password }}
                  </p>
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#94A3B8] hover:text-[#0F172A] focus:outline-none"
                    aria-label="Toggle password visibility"
                  >
                    <svg
                      v-if="showPassword"
                      class="h-5 w-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                      />
                    </svg>
                    <svg
                      v-else
                      class="h-5 w-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Confirm Password -->
              <div>
                <label
                  for="password_confirmation"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  Confirm Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="password_confirmation"
                    v-model="patientData.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                    placeholder="Confirm Password"
                    required
                    minlength="8"
                  />
                  <p
                    v-if="errors.password_confirmation"
                    class="mt-2 text-xs font-semibold text-red-500"
                  >
                    {{ errors.password_confirmation }}
                  </p>
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#94A3B8] hover:text-[#0F172A] focus:outline-none"
                    aria-label="Toggle confirm password visibility"
                  >
                    <svg
                      v-if="showConfirmPassword"
                      class="h-5 w-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                      />
                    </svg>
                    <svg
                      v-else
                      class="h-5 w-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Information Section -->
          <div>
            <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
              Profile Information
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- Full Name -->
              <div>
                <label for="name" class="mb-1 block text-sm text-[#0F172A]">
                  Full Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="name"
                  v-model="patientData.name"
                  type="text"
                  class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Full Name"
                  required
                />
                <p
                  v-if="errors.name"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.name }}
                </p>
              </div>

              <!-- Birth Date -->
              <div>
                <label
                  for="birth_date"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  Birth Date <span class="text-red-500">*</span>
                </label>
                <div class="relative cursor-pointer">
                  <flat-pickr
                    v-model="patientData.birth_date"
                    :config="flatpickrConfig"
                    class="ark:bg-dark-900 focus:outline-hidden h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                    placeholder="Select date"
                    required
                  />
                  <p
                    v-if="errors.birth_date"
                    class="mt-2 text-xs font-semibold text-red-500"
                  >
                    {{ errors.birth_date }}
                  </p>
                </div>
              </div>

              <!-- Gender -->
              <div>
                <label for="gender" class="mb-1 block text-sm text-[#0F172A]">
                  Gender <span class="text-red-500">*</span>
                </label>
                <select
                  id="gender"
                  v-model="patientData.gender"
                  class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  required
                >
                  <option value="" disabled selected>Select an option</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
                <p
                  v-if="errors.gender"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.gender }}
                </p>
              </div>

              <!-- Region -->
              <div>
                <label for="region" class="mb-1 block text-sm text-[#0F172A]"
                  >Region</label
                >
                <select
                  id="region"
                  v-model="patientData.region"
                  class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
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
                  <option value="Sidama ">Sidama</option>
                  <option value="Dire Dawa">Dire Dawa</option>
                </select>
                <p
                  v-if="errors.region"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.region }}
                </p>
              </div>

              <!-- City -->
              <div>
                <label for="city" class="mb-1 block text-sm text-[#0F172A]"
                  >City</label
                >
                <input
                  id="city"
                  v-model="patientData.city"
                  type="text"
                  class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="City"
                />
                <p
                  v-if="errors.city"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.city }}
                </p>
              </div>

              <!-- Phone -->
              <div class="md:col-span-2">
                <label for="phone" class="mb-1 block text-sm text-[#0F172A]">
                  Phone <span class="text-red-500">*</span>
                </label>
                <input
                  id="phone"
                  v-model="patientData.phone"
                  type="tel"
                  class="ark:bg-dark-900 focus:outline-hidden ark:border-gray-700 ark:bg-gray-900 ark:text-white/90 ark:placeholder:text-white/30 ark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Phone"
                  required
                />
                <p
                  v-if="errors.phone"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.phone }}
                </p>
              </div>

              <!-- Profile Picture -->
              <div class="md:col-span-2">
                <span class="mb-1 block text-sm text-[#0F172A]"
                  >Profile Picture</span
                >
                <div
                  :onclick="uploadImage"
                  class="mt-2 flex cursor-pointer justify-center rounded-lg border border-dashed border-[#0F172A]/25 px-6 py-10"
                >
                  <div class="text-center">
                    <svg
                      class="mx-auto h-12 w-12 text-[#94A3B8]"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                      aria-hidden="true"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm1.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <div class="mt-4 flex text-sm text-[#64748B]">
                      <span
                        class="relative cursor-pointer rounded-md bg-white font-semibold text-[#26C6DA] focus-within:outline-none focus-within:ring-2 focus-within:ring-[#26C6DA] focus-within:ring-offset-2 hover:text-[#00BCD4]"
                      >
                        <span> Click To Upload A Image</span>
                      </span>
                      <!-- <p class="pl-1">or drag and drop</p> -->
                    </div>

                    <!-- Image Preview -->
                    <div v-if="imagePreviewUrl" class="mt-4">
                      <img
                        :src="imagePreviewUrl"
                        alt="Profile picture preview"
                        class="mx-auto h-32 w-32 rounded-full border border-[#E2E8F0] object-cover"
                      />
                      <button
                        type="button"
                        @click="clearImage"
                        class="mt-2 text-sm text-red-500 hover:text-red-700 focus:outline-none"
                      >
                        Remove Image
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Terms Agreement -->
          <div class="flex items-start space-x-2">
            <input
              id="terms_agreed"
              v-model="patientData.terms_agreed"
              type="checkbox"
              class="h-5 w-5 rounded border-gray-200 text-[#26C6DA] focus:ring-first-accent"
            />
            <label for="terms_agreed" class="text-sm text-[#0F172A]">
              I Agree to Tenadam’s
              <a href="#" class="text-[#26C6DA] hover:underline"
                >Terms of Use</a
              >
              and
              <a href="#" class="text-[#26C6DA] hover:underline"
                >Privacy Policy</a
              >
              for Accessing Healthcare Services. By Checking This Box, I Commit
              to Using the Platform Responsibly and Protecting My Health Data.
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex gap-x-16">
            <button
              type="submit"
              class="flex w-[158px] items-center justify-around rounded-3xl bg-green-500 py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-green-700"
            >
              Join Now
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
              @click="cancelForm"
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
  </UserLayout>
</template>
