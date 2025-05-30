<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { useAppointmentStore } from "@/stores/appointmentStore";
import { useAuthStore } from "@/stores/auth";
import { useDoctorStore } from "@/stores/doctorStore";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import Modal from "@/components/UI/Modal.vue";
import { useToast } from "vue-toastification"; // <-- Import useToast

const appointmentData = ref({
  doctor_id: "",
  patient_id: "",
  date: "",
  time: "",
  status: "waiting",
});

const fallbackBannerImage =
  "https://res.cloudinary.com/dqxy77qks/image/upload/v1747602382/Flux_Dev_Create_a_highresolution_wideangle_realistic_image_for_0_wve4x4.jpg";

const { getDoctor } = useDoctorStore();
const { createAppointment } = useAppointmentStore();
const { getAppointments } = useAppointmentStore();
const authStore = useAuthStore();

const route = useRoute();
const doctor = ref(null);
const showPopup = ref(false);
const appointments = ref([]);
const isAppointed = ref(false);

const ratingValue = Math.floor(Math.random() * 5) + 1;
const ratingCount = Math.floor(Math.random() * 500) + 1;

const toast = useToast(); // <-- Initialize toast

onMounted(async () => {
  window.scrollTo(0, 0);
  doctor.value = await getDoctor(route.params.id);
  appointments.value = await getAppointments();
  await authStore.getUser();

  // Check if the current user already has an appointment with this doctor
  isAppointed.value = appointments.value.data?.some(
    (a) =>
      a.patient_id === authStore.user.id && a.doctor_id === doctor.value.id,
  );
});

const handleBook = async () => {
  const now = new Date();
  appointmentData.value.doctor_id = doctor.value?.id;
  appointmentData.value.patient_id = authStore?.user.id;
  appointmentData.value.date = now.toISOString().split("T")[0]; // YYYY-MM-DD
  appointmentData.value.time = now.toTimeString().split(" ")[0]; // HH:mm:ss

  const result = await createAppointment(appointmentData.value);
  console.log(appointmentData.value);

  if (result && result.success) {
    toast.success("Appointment booked successfully!");
    showPopup.value = false;
  } else {
    toast.error("Error Booking . Try Again!");
    showPopup.value = false;
  }
};

const closePopup = () => {
  showPopup.value = false;
};

const openPopup = () => {
  showPopup.value = true;
};
</script>

<template>
  <UserLayout>
    <div class="">
      <div
        class="flex h-64 w-full flex-col items-center justify-center bg-gradient-to-r from-[#133a50] to-[#0E2A46]/30 text-white"
      >
        <h1 class="text-5xl font-bold uppercase">Physician Details</h1>

        <div class="mt-4 flex gap-x-2">
          <RouterLink :to="{ name: 'Home' }" class="hover:underline"
            >Home</RouterLink
          >
          <span class="text-second-accent"> //</span>
          <span>{{ doctor?.user?.name }}</span>
        </div>
      </div>

      <div
        v-if="doctor"
        class="mx-auto mt-16 flex max-w-[1250px] justify-between bg-gray-200/50 p-16 shadow-theme-xl"
      >
        <div class="max-w-[600px]">
          <div
            class="min-h-80 min-w-[550px] bg-cover bg-center"
            :style="{
              backgroundImage: `url(${fallbackBannerImage})`,
            }"
          ></div>
          <div class="mt-4">
            <div class="flex items-center gap-x-2">
              <div class="flex gap-x-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="size-5"
                  fill="#fc6441"
                  viewBox="0 0 256 256"
                  v-for="n in ratingValue"
                >
                  <path
                    d="M234.29,114.85l-45,38.83L203,211.75a16.4,16.4,0,0,1-24.5,17.82L128,198.49,77.47,229.57A16.4,16.4,0,0,1,53,211.75l13.76-58.07-45-38.83A16.46,16.46,0,0,1,31.08,86l59-4.76,22.76-55.08a16.36,16.36,0,0,1,30.27,0l22.75,55.08,59,4.76a16.46,16.46,0,0,1,9.37,28.86Z"
                  ></path>
                </svg>
              </div>
              <span class="text-lg"> ({{ ratingValue }})</span>
              <span class="text-lg"> {{ ratingCount }} Ratings</span>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="ml-6 size-8 cursor-pointer fill-[#f9fafb] stroke-black hover:fill-red-600"
                viewBox="0 0 256 256"
              >
                <path
                  d="M240,102c0,70-103.79,126.66-108.21,129a8,8,0,0,1-7.58,0C119.79,228.66,16,172,16,102A62.07,62.07,0,0,1,78,40c20.65,0,38.73,8.88,50,23.89C139.27,48.88,157.35,40,178,40A62.07,62.07,0,0,1,240,102Z"
                ></path>
              </svg>
            </div>
            <h1 class="my-4 text-4xl font-bold">{{ doctor.specialty }}</h1>

            <div
              class="flex items-center gap-x-8 border-b border-b-gray-500 py-2"
            >
              <h1 class="text-xl font-semibold capitalize">
                Dr.{{ doctor.user.name }}
              </h1>
              <div class="flex items-center gap-x-4">
                <!-- Phone -->
                <div class="relative cursor-pointer">
                  <a :href="`tel:${doctor.user.phone}`">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-8"
                      fill="#161313"
                      viewBox="0 0 256 256"
                    >
                      <path
                        d="M224,48H32a8,8,0,0,0-8,8V192a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A8,8,0,0,0,224,48Zm-96,85.15L52.57,64H203.43ZM98.71,128,40,181.81V74.19Zm11.84,10.85,12,11.05a8,8,0,0,0,10.82,0l12-11.05,58,53.15H52.57ZM157.29,128,216,74.18V181.82Z"
                      ></path>
                    </svg>
                  </a>
                </div>
                <!-- Email -->
                <div class="relative cursor-pointer">
                  <a :href="`mailto:${doctor.user.email}`">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-8"
                      fill="#161313"
                      viewBox="0 0 256 256"
                    >
                      <path
                        d="M144.27,45.93a8,8,0,0,1,9.8-5.66,86.22,86.22,0,0,1,61.66,61.66,8,8,0,0,1-5.66,9.8A8.23,8.23,0,0,1,208,112a8,8,0,0,1-7.73-5.94,70.35,70.35,0,0,0-50.33-50.33A8,8,0,0,1,144.27,45.93Zm-2.33,41.8c13.79,3.68,22.65,12.54,26.33,26.33A8,8,0,0,0,176,120a8.23,8.23,0,0,0,2.07-.27,8,8,0,0,0,5.66-9.8c-5.12-19.16-18.5-32.54-37.66-37.66a8,8,0,1,0-4.13,15.46Zm81.94,95.35A56.26,56.26,0,0,1,168,232C88.6,232,24,167.4,24,88A56.26,56.26,0,0,1,72.92,32.12a16,16,0,0,1,16.62,9.52l21.12,47.15,0,.12A16,16,0,0,1,109.39,104c-.18.27-.37.52-.57.77L88,129.45c7.49,15.22,23.41,31,38.83,38.51l24.34-20.71a8.12,8.12,0,0,1,.75-.56,16,16,0,0,1,15.17-1.4l.13.06,47.11,21.11A16,16,0,0,1,223.88,183.08Zm-15.88-2s-.07,0-.11,0h0l-47-21.05-24.35,20.71a8.44,8.44,0,0,1-.74.56,16,16,0,0,1-15.75,1.14c-18.73-9.05-37.4-27.58-46.46-46.11a16,16,0,0,1,1-15.7,6.13,6.13,0,0,1,.57-.77L96,95.15l-21-47a.61.61,0,0,1,0-.12A40.2,40.2,0,0,0,40,88,128.14,128.14,0,0,0,168,216,40.21,40.21,0,0,0,208,181.07Z"
                      ></path>
                    </svg>
                  </a>
                </div>
              </div>
            </div>
            <div class="mt-10">
              <h1 class="text-xl font-semibold uppercase">Overview</h1>
              <p class="mt-3 text-sm">
                {{ doctor.overview }}
              </p>
            </div>
          </div>
        </div>

        <div class="h-fit w-[300px] rounded-lg bg-[#e8e8f4] pb-4 shadow-md">
          <div
            class="h-[200px] w-full bg-cover bg-center"
            :style="{
              backgroundImage: `url(${
                doctor.user.profile_picture
                  ? doctor.user.profile_picture
                  : fallbackBannerImage
              })`,
            }"
          ></div>
          <div class="mt-4 px-3">
            <div class="flex justify-between">
              <span>Fee</span>
              <span class="font-semibold">Br {{ doctor.payment }}</span>
            </div>
            <button
              v-if="!isAppointed"
              @click="openPopup"
              class="my-6 w-full rounded-md border border-first-accent px-4 py-2 text-first-accent shadow-sm transition duration-300 hover:bg-first-accent hover:text-white"
            >
              Book Now
            </button>
            <div v-else class="my-6 flex w-full flex-col gap-y-1">
              <p class="text-sm font-semibold">Already Have Appointment</p>
              <RouterLink
                :to="{ name: 'Home' }"
                class="w-full rounded-md border border-first-accent px-4 py-2 text-center text-first-accent shadow-sm transition duration-300 hover:bg-first-accent hover:text-white"
              >
                Go To Appointment
              </RouterLink>
            </div>

            <div class="space-y-4">
              <div class="">
                <h1 class="font-semibold uppercase text-first-accent">
                  Location
                </h1>

                <div class="mt-2">
                  {{ doctor.user.city }} , {{ doctor.user.region }}
                </div>
              </div>
              <div class="">
                <h1 class="font-semibold uppercase text-first-accent">
                  Languages
                </h1>
                <div class="grid grid-cols-3 gap-x-2">
                  <div
                    class="mt-2 rounded-md bg-second-accent px-2 py-1 text-sm text-white shadow-sm"
                    v-for="(language, index) in JSON.parse(doctor.languages)"
                    :key="index"
                  >
                    {{ language }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal v-if="showPopup" @close="closePopup" :fullScreenBackdrop="true">
      <template #body>
        <div
          class="relative max-h-[700px] w-full max-w-[700px] overflow-y-auto overflow-x-hidden rounded-3xl bg-white p-4 lg:p-11"
        >
          <h5
            class="modal-title mb-2 text-theme-xl font-semibold text-gray-800 lg:text-2xl"
          >
            Book Doctor Appointment
          </h5>
          <p class="text-sm text-gray-500">
            confirm your appointment with Dr.
            {{ doctor?.user?.name }}.
          </p>

          <div class="mt-4 flex gap-x-4">
            <button
              @click="closePopup"
              class="my-6 w-fit rounded-md border border-gray-400 px-4 py-2 text-black shadow-sm transition duration-300 hover:bg-first-accent hover:text-white"
            >
              Cancel
            </button>
            <button
              @click="handleBook"
              class="my-6 w-fit rounded-md border border-gray-400 px-4 py-2 text-black shadow-sm transition duration-300 hover:bg-success-400 hover:text-white"
            >
              Confirm
            </button>
          </div>
        </div>
      </template>
    </Modal>
  </UserLayout>
</template>
