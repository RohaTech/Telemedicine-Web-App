<script setup>
import { ref } from "vue";
import categories_pattern from "/images/HomePage/categories_pattern.png";

const categories = ref([
  { name: "Obstetrics and Gynecology", slug: "obstetrics-and-gynecology" },
  { name: "Pediatrics", slug: "pediatrics" },
  { name: "Dermatology", slug: "dermatology" },
  { name: "Cardiology", slug: "cardiology" },
  { name: "Orthopedics", slug: "orthopedics" },
  { name: "Pathology", slug: "pathology" },
  { name: "Oncology", slug: "oncology" },
  { name: "Hematology", slug: "hematology" },
  { name: "Pulmonology", slug: "pulmonology" },
  { name: "Hepatology", slug: "hepatology" },
]);
</script>

<template>
  <div class="my-16 px-4 md:px-24">
    <!-- Header Section -->
    <div class="mb-8 flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-800 md:text-4xl">Categories</h1>
      <img
        :src="categories_pattern"
        :alt="'Categories Pattern'"
        class="h-8 md:h-10"
      />
      <RouterLink
        :to="{ name: 'Home' }"
        class="flex w-fit items-center justify-center rounded-md bg-first-accent px-1 py-2 uppercase text-white shadow-sm transition duration-300 hover:bg-second-accent hover:text-gray-800 md:px-8 md:py-3"
      >
        More Categories
      </RouterLink>
    </div>

    <!-- Categories Grid -->
    <div
      class="mx-auto mt-16 grid max-w-[1200px] grid-cols-2 gap-3 md:grid-cols-5 md:gap-8"
    >
      <RouterLink
        v-for="category in categories"
        :key="category.name"
        :to="{ name: 'Home' }"
        class="rounded-md bg-first-accent py-4 text-center text-sm font-medium text-white shadow-sm transition duration-300 hover:bg-second-accent hover:text-gray-800 md:py-6 md:text-base"
      >
        {{ category.name }}
      </RouterLink>
    </div>
  </div>
</template>
