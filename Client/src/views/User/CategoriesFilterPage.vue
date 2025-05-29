<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { useDoctorStore } from "@/stores/doctorStore";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const { getDoctorsByCategory } = useDoctorStore();
const doctors = ref([]);

const fallbackImage =
  "https://res.cloudinary.com/dqxy77qks/image/upload/v1747340223/4b42ed27-d4d8-4205-b44b-595b7060097d.png";
const fallbackBannerImage =
  "https://res.cloudinary.com/dqxy77qks/image/upload/v1747728117/fe4b1d1b-62a8-4c84-be50-d78dd76ec00d.png";

onMounted(async () => {
  window.scrollTo(0, 0);
  doctors.value = await getDoctorsByCategory(route.params.slug);
  console.log(doctors.value);
});
</script>

<template>
  <UserLayout>
    <div
      class="flex h-64 w-full flex-col items-center justify-center bg-gradient-to-r from-[#133a50] to-[#0E2A46]/30 text-white"
    >
      <h1 class="text-5xl font-bold uppercase">
        doctors with {{ doctors[0]?.specialty }} Specialty
      </h1>
      <div class="mt-4 flex gap-x-2">
        <RouterLink :to="{ name: 'Home' }" class="hover:underline"
          >Home</RouterLink
        >
        <span class="text-second-accent"> //</span>
        <span>{{ doctors[0]?.specialty }}</span>
      </div>
    </div>

    <div
      v-if="doctors.length > 0"
      class="mx-auto mt-16 grid max-w-[1500px] grid-cols-2 gap-3 md:grid-cols-6 md:gap-8"
    >
      <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
        <RouterLink
          v-for="item in doctors"
          :key="item.id"
          :to="{ name: 'Home' }"
          class="w-[330px] overflow-hidden rounded-lg bg-white shadow-md transition duration-300 hover:shadow-lg"
        >
          <!-- Image -->
          <img
            :src="
              item.user.profile_picture
                ? item.user.profile_picture
                : fallbackBannerImage
            "
            :alt="`${item.user.name} Image`"
            class="h-40 w-full object-cover md:h-48"
          />

          <!-- Card Content -->
          <div class="p-4">
            <!-- Rating and Price -->
            <div class="mb-2 flex items-center justify-between">
              <div class="flex items-center">
                <svg
                  v-for="n in Math.floor(Math.random() * 5) + 1"
                  :key="n"
                  xmlns="http://www.w3.org/2000/svg"
                  class="size-5 fill-yellow-400"
                  viewBox="0 0 256 256"
                >
                  <path
                    d="M234.29,114.85l-45,38.83L203,211.75a16.4,16.4,0,0,1-24.5,17.82L128,198.49,77.47,229.57A16.4,16.4,0,0,1,53,211.75l13.76-58.07-45-38.83A16.46,16.46,0,0,1,31.08,86l59-4.76,22.76-55.08a16.36,16.36,0,0,1,30.27,0l22.75,55.08,59,4.76a16.46,16.46,0,0,1,9.37,28.86Z"
                  ></path>
                </svg>
                <span class="ml-1"
                  >{{ Math.floor(Math.random() * 5) + 1 }} ({{
                    Math.floor(Math.random() * 500) + 1
                  }})</span
                >
              </div>
              <span class="text-sm text-gray-600"
                >Starting from
                <span class="font-bold">{{ item.payment }} Br</span></span
              >
            </div>

            <!-- Category -->
            <h3 class="mb-2 h-[80px] text-lg font-semibold text-gray-800">
              {{ item.specialty }}
            </h3>

            <!-- Doctor and View Details -->
            <div class="flex items-center gap-x-4 text-sm text-gray-600">
              <div
                class="size-8 rounded-full bg-cover bg-center"
                :style="{
                  backgroundImage: `url(${
                    item.user.profile_picture
                      ? item.user.profile_picture
                      : fallbackImage
                  })`,
                }"
              ></div>
              {{ item.user.name }}
            </div>
            <RouterLink
              :to="{ name: 'UserDoctorDetail', params: { id: item.id } }"
              class="mt-3 flex items-center justify-end bg-first-accent px-3 py-2"
            >
              <button
                class="rounded-md bg-second-accent px-3 py-1.5 text-sm text-white transition duration-300 hover:scale-105 hover:text-gray-800"
              >
                View Details
              </button>
            </RouterLink>
          </div>
        </RouterLink>
      </div>
    </div>
    <div v-else class="mx-auto mt-16 w-full max-w-[648px] text-center">
      <p class="text-xl font-semibold text-[#0F172A]">
        No Doctors Found in This Specialty Yet
      </p>
      <p class="mt-2 text-[#64748B]">
        It looks like we don’t have doctors in this specialty right now. Let’s
        find another specialty to help you get the care you need!
      </p>
    </div>
  </UserLayout>
</template>
