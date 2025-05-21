<script setup>
import { onMounted, ref } from "vue";
import doctorImage from "/images/HomePage/doctor_placeholder.png";
import doctor_profile from "/images/HomePage/doctor_profile.png";
import { RouterLink } from "vue-router";
import { useDoctorStore } from "@/stores/doctorStore";

const doctors = ref([]);

const fallbackImage =
  "https://res.cloudinary.com/dqxy77qks/image/upload/v1747340223/4b42ed27-d4d8-4205-b44b-595b7060097d.png";
const fallbackBannerImage =
  "https://res.cloudinary.com/dqxy77qks/image/upload/v1747728117/fe4b1d1b-62a8-4c84-be50-d78dd76ec00d.png";

const { getActiveDoctors } = useDoctorStore();

onMounted(async () => {
  doctors.value = await getActiveDoctors();

  console.log(doctors.value);
});

const recentlyVisited = [
  {
    category: "Pathology",
    rating: 4.7,
    reviews: 187,
    price: "1200Br",
    doctor: "Dr. Melkamu Tebeje",
    slug: "pathology",
  },
  {
    category: "Pulmonology Obstetrics and Gynecology",
    rating: 4.4,
    reviews: 536,
    price: "1200Br",
    doctor: "Dr. Melkamu Tebeje",
    slug: "pulmonology-obstetrics-and-gynecology",
  },
  {
    category: "Pediatrics",
    rating: 4.7,
    reviews: 641,
    price: "1200Br",
    doctor: "Dr. Melkamu Tebeje",
    slug: "pediatrics",
  },
  {
    category: "Dermatology",
    rating: 4.4,
    reviews: 601,
    price: "1200Br",
    doctor: "Dr. Melkamu Tebeje",
    slug: "dermatology",
  },
];
</script>

<template>
  <div class="mb-16 mt-24 px-4 md:px-24">
    <div class="w-fit border border-second-accent px-2 py-1 text-purple-600">
      Top Popular Doctor
    </div>

    <div class="mb-8 flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-800 md:text-4xl">
        Explore Featured
      </h1>

      <RouterLink
        :to="{ name: 'Home' }"
        class="flex w-40 items-center justify-center rounded-md bg-first-accent px-4 py-2 uppercase text-white shadow-sm transition duration-300 hover:bg-second-accent hover:text-gray-800 md:w-52 md:px-8 md:py-3"
      >
        Browse More
      </RouterLink>
    </div>

    <!-- Grid of Cards -->
    <div v-if="doctors" class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
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
</template>
