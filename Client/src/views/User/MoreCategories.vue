<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { onMounted, ref } from "vue";

onMounted(() => {
  window.scrollTo(0, 0);
});

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
  { name: "Endocrinology", slug: "endocrinology" },
  { name: "Gastroenterology", slug: "gastroenterology" },
  { name: "Infectious Disease", slug: "infectious-disease" },
  { name: "Internal Medicine", slug: "internal-medicine" },
  { name: "Nephrology", slug: "nephrology" },
  { name: "Neurology", slug: "neurology" },
  { name: "Ophthalmology", slug: "ophthalmology" },
  { name: "Otolaryngology", slug: "otolaryngology" },
  { name: "Psychiatry", slug: "psychiatry" },
  { name: "Rheumatology", slug: "rheumatology" },
  { name: "Surgery", slug: "surgery" },
  { name: "Urology", slug: "urology" },
]);
</script>

<template>
  <UserLayout>
    <div class="">
      <div
        class="flex h-64 w-full flex-col items-center justify-center bg-gradient-to-r from-[#133a50] to-[#0E2A46]/30 text-white"
      >
        <h1 class="text-5xl font-bold uppercase">More Categories</h1>
        <div class="mt-4 flex gap-x-2">
          <RouterLink :to="{ name: 'Home' }" class="hover:underline"
            >Home</RouterLink
          >
          <span class="text-second-accent"> //</span>
          <span>Categories</span>
        </div>
      </div>
    </div>

    <div
      class="mx-auto mt-16 grid max-w-[1500px] grid-cols-2 gap-3 md:grid-cols-6 md:gap-8"
    >
      <RouterLink
        v-for="category in categories.sort((a, b) =>
          a.name.localeCompare(b.name),
        )"
        :key="category.name"
        :to="{ name: 'CategoriesFilter', params: { slug: category.slug } }"
        class="rounded-md bg-first-accent py-4 text-center text-sm font-medium text-white shadow-sm transition duration-300 hover:bg-second-accent hover:text-gray-800 md:py-6 md:text-base"
      >
        {{ category.name }}
      </RouterLink>
    </div>
  </UserLayout>
</template>
