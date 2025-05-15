<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { useVuelidate } from "@vuelidate/core";
import { required, url } from "@vuelidate/validators";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Specialties list
const specialties = ref([
  "Cardiology",
  "Dermatology",
  "Endocrinology",
  "Gastroenterology",
  "Hematology",
  "Infectious Disease",
  "Internal Medicine",
  "Nephrology",
  "Neurology",
  "Obstetrics and Gynecology",
  "Oncology",
  "Ophthalmology",
  "Orthopedics",
  "Otolaryngology",
  "Pediatrics",
  "Psychiatry",
  "Pulmonology",
  "Rheumatology",
  "Surgery",
  "Urology",
]);

const authStore = useAuthStore();
const { errors } = storeToRefs(authStore);

// Form data
const doctorData = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  phone: "",
  city: "",
  region: "",
  profile_picture: null,
  gender: "",
  birth_date: "",
  medical_license_number: "",
  specialty: "",
  qualification: "",
  experience_years: "",
  university_attended: "",
  license_issue_date: "",
  license_expiry_date: "",
  status: "pending",
  payment: "",
  location: { lat: null, lng: null },
  terms_agreed: false,
  certificate_path: "",
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const certificatePreviewUrl = ref(null);
const profileImageUrl = ref(null); // For preview
const router = useRouter();
const successMessage = ref(null);

const map = ref(null);
const mapContainer = ref(null);
const marker = ref(null);

// Cloudinary Upload Widget
const widget = window.cloudinary.createUploadWidget(
  {
    cloud_name: "dqxy77qks",
    upload_preset: "tenadam-upload",
    multiple: false,
    sources: ["local"],
    resource_type: "auto",
  },
  (error, result) => {
    if (!error && result && result.event === "success") {
      const url = result.info.secure_url;
      if (url.match(/\.(jpg|jpeg|png|pdf)$/i)) {
        console.log("Certificate uploaded successfully:", url);
        doctorData.value.certificate_path = url;
        certificatePreviewUrl.value = url;
      } else {
        alert("Please upload a valid PDF or image file.");
      }
    } else if (error) {
      console.error("Cloudinary upload error:", error);
      alert("Failed to upload certificate. Please try again.");
    }
  },
);

const uploadCertificate = () => {
  widget.open();
};

const clearCertificate = () => {
  doctorData.value.certificate_path = "";
  certificatePreviewUrl.value = null;
};

const profileImageWidget = window.cloudinary.createUploadWidget(
  {
    cloud_name: "dqxy77qks",
    upload_preset: "tenadam-upload",
    multiple: false,
    sources: ["local"],
  },
  (error, result) => {
    if (!error && result && result.event === "success") {
      doctorData.value.profile_picture = result.info.secure_url;
      profileImageUrl.value = result.info.secure_url;
      console.log("Profile image uploaded: ", result.info.secure_url);
    }
  },
);

const uploadProfileImage = () => {
  profileImageWidget.open();
};

const clearProfileImage = () => {
  doctorData.value.profile_picture = "";
  profileImageUrl.value = null;
};

// Validation rules
const rules = {
  name: { required },
  email: { required },
  password: { required },
  password_confirmation: { required },
  phone: { required },
  city: { required },
  region: { required },
  gender: { required },
  birth_date: { required },
  medical_license_number: { required },
  specialty: { required },
  qualification: { required },
  experience_years: { required },
  university_attended: { required },
  license_issue_date: { required },
  license_expiry_date: { required },
  payment: { required },
  location: {
    lat: { required },
    lng: { required },
  },
  terms_agreed: { required, checked: (value) => value === true },
  certificate_path: { required, url },
};

const v$ = useVuelidate(rules, doctorData);

// Prepare location data
const prepareLocation = () => {
  const location = doctorData.value.location;
  if (!location || typeof location !== "object") {
    return null;
  }
  if (location.lat !== null && location.lng !== null) {
    try {
      const locationData = {
        lat: location.lat,
        lng: location.lng,
      };
      return JSON.stringify(locationData);
    } catch (error) {
      console.error("Failed to stringify location:", error);
      return null;
    }
  }
  return null;
};

// Handle form submission
const submitForm = async () => {
  try {
    const isValid = await v$.value.$validate();
    if (!isValid) {
      console.error("Form validation failed:", v$.value.$errors);
      alert(
        "Please complete all required fields, including selecting a map location and uploading a certificate.",
      );
      return;
    }

    const submissionData = {
      name: doctorData.value.name,
      email: doctorData.value.email,
      password: doctorData.value.password,
      password_confirmation: doctorData.value.password_confirmation,
      phone: doctorData.value.phone,
      city: doctorData.value.city,
      profile_picture: doctorData.value.profile_picture,
      region: doctorData.value.region,
      gender: doctorData.value.gender,
      birth_date: doctorData.value.birth_date,
      role: "doctor",
      medical_license_number: doctorData.value.medical_license_number,
      specialty: doctorData.value.specialty,
      status: doctorData.value.status,
      qualification: doctorData.value.qualification,
      experience_years: parseInt(doctorData.value.experience_years) || 0,
      university_attended: doctorData.value.university_attended,
      license_issue_date: doctorData.value.license_issue_date,
      license_expiry_date: doctorData.value.license_expiry_date,
      payment: parseFloat(doctorData.value.payment) || 0,
      location: prepareLocation() || "",
      terms_agreed: doctorData.value.terms_agreed,
      certificate_path: doctorData.value.certificate_path,
    };

    console.log("Submission Data:", submissionData);
    await authStore.authenticate("register-doctor", submissionData);

    successMessage.value =
      "Registration successful. Your account is under review.";
    setTimeout(() => {
      router.push({ name: "DoctorStatus" });
    }, 3000);
  } catch (error) {
    console.error("Registration failed:", error);
    alert(
      "Registration failed: " +
        (error.response?.data?.message || error.message),
    );
  }
};

// Handle cancel button
const cancelForm = () => {
  router.push("/");
};

// Flatpickr configuration
const flatpickrConfig = {
  dateFormat: "Y-m-d",
  altInput: true,
  altFormat: "F j, Y",
  wrap: true,
};

onMounted(() => {
  // Initialize Leaflet map
  map.value = L.map(mapContainer.value).setView([9.02, 38.74], 6); // Ethiopia coordinates

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map.value);

  // Add click event listener to the map
  map.value.on("click", (e) => {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    // Update doctorData.location
    doctorData.value.location.lat = lat;
    doctorData.value.location.lng = lng;

    console.log("Selected location:", doctorData.value.location);

    // Add or move the marker
    if (marker.value) {
      marker.value.setLatLng([lat, lng]);
    } else {
      marker.value = L.marker([lat, lng]).addTo(map.value);
    }
  });
});
</script>

<template>
  <UserLayout>
    <div class="flex min-h-screen justify-center px-4 py-12 sm:px-6 lg:px-8">
      <div
        class="w-full max-w-[860px] border border-gray-50 bg-[#f3f5f5] p-16 drop-shadow-md"
      >
        <h1 class="mb-1 text-3xl font-bold text-[#0F172A]">
          DOCTOR REGISTRATION
        </h1>
        <p class="mb-2 text-lg text-first-accent">
          Join Tenadam: Provide Healthcare Services
        </p>
        <p class="mb-8 mt-3 text-xs text-[#64748B]">
          FILL YOUR CREDENTIALS BELOW CORRECTLY
        </p>

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
                  v-model="doctorData.email"
                  type="email"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Email"
                  required
                />
                <p
                  v-if="errors.email"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.email.join(", ") }}
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
                    v-model="doctorData.password"
                    :type="showPassword ? 'text' : 'password'"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                    placeholder="Password"
                    required
                    minlength="8"
                  />
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
                <p
                  v-if="errors.password"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.password.join(", ") }}
                </p>
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
                    v-model="doctorData.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                    placeholder="Confirm Password"
                    required
                    minlength="8"
                  />
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
                <p
                  v-if="errors.password_confirmation"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.password_confirmation.join(", ") }}
                </p>
              </div>
            </div>
          </div>

          <!-- Personal Information Section -->
          <div>
            <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
              Personal Information
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- Name -->
              <div>
                <label for="name" class="mb-1 block text-sm text-[#0F172A]">
                  Full Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="name"
                  v-model="doctorData.name"
                  type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Full Name"
                  required
                />
                <p
                  v-if="errors.name"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.name.join(", ") }}
                </p>
              </div>
              <!-- Phone -->
              <div>
                <label for="phone" class="mb-1 block text-sm text-[#0F172A]">
                  Phone <span class="text-red-500">*</span>
                </label>
                <input
                  id="phone"
                  v-model="doctorData.phone"
                  type="tel"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Phone"
                  required
                />
                <p
                  v-if="errors.phone"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.phone.join(", ") }}
                </p>
              </div>
              <!-- Gender -->
              <div>
                <label for="gender" class="mb-1 block text-sm text-[#0F172A]">
                  Gender <span class="text-red-500">*</span>
                </label>
                <select
                  id="gender"
                  v-model="doctorData.gender"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  required
                >
                  <option value="" disabled selected>Select gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
                <p
                  v-if="errors.gender"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.gender.join(", ") }}
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
                <flat-pickr
                  v-model="doctorData.birth_date"
                  :config="flatpickrConfig"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Select date"
                  required
                />
                <p
                  v-if="errors.birth_date"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.birth_date.join(", ") }}
                </p>
              </div>
              <!-- Region -->
              <div>
                <label for="region" class="mb-1 block text-sm text-[#0F172A]">
                  Region <span class="text-red-500">*</span>
                </label>
                <select
                  id="region"
                  v-model="doctorData.region"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
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
                  v-if="errors.region"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.region.join(", ") }}
                </p>
              </div>
              <!-- City -->
              <div>
                <label for="city" class="mb-1 block text-sm text-[#0F172A]">
                  City <span class="text-red-500">*</span>
                </label>
                <input
                  id="city"
                  v-model="doctorData.city"
                  type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="City"
                  required
                />
                <p
                  v-if="errors.city"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.city.join(", ") }}
                </p>
              </div>
            </div>

            <!-- Add here the profile_picture upload -->
            <!-- Profile Image Upload -->
            <div>
              <span class="mb-1 block text-sm text-[#0F172A]">
                Profile Image
              </span>
              <div
                @click="uploadProfileImage"
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
                      Click to Upload a Profile Image
                    </span>
                  </div>
                  <!-- Image Preview -->
                  <div v-if="profileImageUrl" class="mt-4">
                    <img
                      :src="profileImageUrl"
                      alt="Profile image preview"
                      class="mx-auto h-32 w-32 rounded-full border border-[#E2E8F0] object-cover"
                    />
                    <button
                      type="button"
                      @click.stop="clearProfileImage"
                      class="mt-2 text-sm text-red-500 hover:text-red-700 focus:outline-none"
                    >
                      Remove Image
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Information Section -->
          <div>
            <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
              Professional Information
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- Medical License Number -->
              <div>
                <label
                  for="medical_license_number"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  Medical License Number <span class="text-red-500">*</span>
                </label>
                <input
                  id="medical_license_number"
                  v-model="doctorData.medical_license_number"
                  type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Medical License Number"
                  required
                />
                <p
                  v-if="errors.medical_license_number"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.medical_license_number.join(", ") }}
                </p>
              </div>
              <!-- Specialty -->
              <div>
                <label
                  for="specialty"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  Specialty <span class="text-red-500">*</span>
                </label>
                <select
                  id="specialty"
                  v-model="doctorData.specialty"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  required
                >
                  <option value="" disabled selected>Select a specialty</option>
                  <option
                    v-for="specialty in specialties"
                    :key="specialty"
                    :value="specialty"
                  >
                    {{ specialty }}
                  </option>
                </select>
                <p
                  v-if="errors.specialty"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.specialty.join(", ") }}
                </p>
              </div>
              <!-- Qualification -->
              <div>
                <label
                  for="qualification"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  Qualification <span class="text-red-500">*</span>
                </label>
                <input
                  id="qualification"
                  v-model="doctorData.qualification"
                  type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Qualification"
                  required
                />
                <p
                  v-if="errors.qualification"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.qualification.join(", ") }}
                </p>
              </div>
              <!-- Experience Years -->
              <div>
                <label
                  for="experience_years"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  Years of Experience <span class="text-red-500">*</span>
                </label>
                <input
                  id="experience_years"
                  v-model="doctorData.experience_years"
                  type="number"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Years of Experience"
                  required
                  min="0"
                />
                <p
                  v-if="errors.experience_years"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.experience_years.join(", ") }}
                </p>
              </div>
              <!-- University Attended -->
              <div>
                <label
                  for="university_attended"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  University Attended <span class="text-red-500">*</span>
                </label>
                <input
                  id="university_attended"
                  v-model="doctorData.university_attended"
                  type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="University Attended"
                  required
                />
                <p
                  v-if="errors.university_attended"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.university_attended.join(", ") }}
                </p>
              </div>
              <!-- License Issue Date -->
              <div>
                <label
                  for="license_issue_date"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  License Issue Date <span class="text-red-500">*</span>
                </label>
                <flat-pickr
                  v-model="doctorData.license_issue_date"
                  :config="flatpickrConfig"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Select date"
                  required
                />
                <p
                  v-if="errors.license_issue_date"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.license_issue_date.join(", ") }}
                </p>
              </div>
              <!-- License Expiry Date -->
              <div>
                <label
                  for="license_expiry_date"
                  class="mb-1 block text-sm text-[#0F172A]"
                >
                  License Expiry Date <span class="text-red-500">*</span>
                </label>
                <flat-pickr
                  v-model="doctorData.license_expiry_date"
                  :config="flatpickrConfig"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Select date"
                  required
                />
                <p
                  v-if="errors.license_expiry_date"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.license_expiry_date.join(", ") }}
                </p>
              </div>
              <!-- Payment -->
              <div>
                <label for="payment" class="mb-1 block text-sm text-[#0F172A]">
                  Payment (ETB) <span class="text-red-500">*</span>
                </label>
                <input
                  id="payment"
                  v-model="doctorData.payment"
                  type="number"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-first-accent"
                  placeholder="Payment"
                  required
                  min="0"
                  step="0.01"
                />
                <p
                  v-if="errors.payment"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.payment.join(", ") }}
                </p>
              </div>
              <!-- Map Location -->
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm text-[#0F172A]">
                  Map Location <span class="text-red-500">*</span>
                  <span class="ml-1 text-xs text-gray-500"
                    >(Click on map to set location)</span
                  >
                </label>
                <div
                  class="h-[400px] rounded-lg border border-gray-300 bg-gray-100"
                  ref="mapContainer"
                ></div>
                <p
                  v-if="v$.location.lat.$error || v$.location.lng.$error"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  Please select a location on the map.
                </p>
              </div>
            </div>
          </div>

          <!-- Certificate Upload Section -->
          <div>
            <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">
              Certificate
            </h2>
            <div class="grid grid-cols-1 gap-4">
              <div>
                <span class="mb-1 block text-sm text-[#0F172A]">
                  Certificate (PDF/Image) <span class="text-red-500">*</span>
                </span>
                <div
                  @click="uploadCertificate"
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
                        Click to Upload a Certificate
                      </span>
                    </div>
                    <!-- Certificate Preview -->
                    <div v-if="certificatePreviewUrl" class="mt-4">
                      <img
                        v-if="
                          certificatePreviewUrl.includes('.jpg') ||
                          certificatePreviewUrl.includes('.jpeg') ||
                          certificatePreviewUrl.includes('.png')
                        "
                        :src="certificatePreviewUrl"
                        alt="Certificate preview"
                        class="mx-auto h-32 w-32 border border-[#E2E8F0] object-cover"
                      />
                      <a
                        v-else
                        :href="certificatePreviewUrl"
                        target="_blank"
                        class="text-[#26C6DA] hover:underline"
                      >
                        View Certificate (PDF)
                      </a>
                      <button
                        type="button"
                        @click="clearCertificate"
                        class="mt-2 text-sm text-red-500 hover:text-red-700 focus:outline-none"
                      >
                        Remove Certificate
                      </button>
                    </div>
                  </div>
                </div>
                <p
                  v-if="v$.certificate_path.$error"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  Certificate is required and must be a valid URL.
                </p>
                <p
                  v-if="errors.certificate_path"
                  class="mt-2 text-xs font-semibold text-red-500"
                >
                  {{ errors.certificate_path.join(", ") }}
                </p>
              </div>
            </div>
          </div>

          <!-- Terms Agreement -->
          <div class="flex items-start space-x-2">
            <input
              id="terms_agreed"
              v-model="doctorData.terms_agreed"
              type="checkbox"
              class="h-5 w-5 rounded border-gray-200 text-[#26C6DA] focus:ring-first-accent"
              required
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
              for Providing Healthcare Services.
            </label>
            <p
              v-if="errors.terms_agreed"
              class="mt-2 text-xs font-semibold text-red-500"
            >
              {{ errors.terms_agreed.join(", ") }}
            </p>
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
