<script setup>
import { ref } from 'vue';
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
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Categories</h1>
      <img :src="categories_pattern" :alt="'Categories Pattern'" class="h-8 md:h-10" />
      <RouterLink
        :to="{ name: 'Home' }"
        class="uppercase flex items-center justify-center bg-first-accent py-2 md:py-3 px-1 md:px-8  w-fit text-white rounded-md shadow-sm hover:bg-second-accent hover:text-gray-800 transition duration-300"
      >
        More Categories
      </RouterLink>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-2 gap-3 md:grid-cols-5 md:gap-8 max-w-[1200px] mx-auto mt-16">
      <RouterLink
        v-for="category in categories"
        :key="category.name"
        :to="`/category/${category.slug}`"
        class="bg-first-accent text-white text-center py-4 md:py-6 rounded-md shadow-sm hover:bg-second-accent hover:text-gray-800 transition duration-300 text-sm md:text-base font-medium"
      >
        {{ category.name }}
      </RouterLink>
    </div>
  </div>
</template>