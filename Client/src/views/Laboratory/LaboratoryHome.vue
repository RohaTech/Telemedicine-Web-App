<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useLaboratoryStore } from "@/stores/laboratoryStore";
import UserLayout from "@/layout/UserLayout.vue";
const route = useRoute();
const laboratoryStore = useLaboratoryStore();
const laboratory = ref(null);

onMounted(async () => {
  laboratory.value = await laboratoryStore.getLaboratory(route.params.id);

  console.log(laboratory.value);
});
</script>
<template>
  <UserLayout>
    <div
      class="mx-auto max-w-2xl rounded-md border border-gray-300 bg-gray-50 p-5"
    >
      <h1 class="mb-6 text-2xl font-bold">Laboratory Details</h1>
      <div v-if="laboratoryStore.errors.message" class="mb-4 text-red-600">
        {{ laboratoryStore.errors.message }}
      </div>
      <div v-if="laboratory">
        <div class="mb-4"><strong>ID:</strong> {{ laboratory.id }}</div>
        <div class="mb-4"><strong>Name:</strong> {{ laboratory.name }}</div>
        <div class="mb-4"><strong>Email:</strong> {{ laboratory.email }}</div>
        <div class="mb-4"><strong>Phone:</strong> {{ laboratory.phone }}</div>
        <div class="mb-4">
          <strong>Address:</strong> {{ laboratory.address }}
        </div>
        <RouterLink
          :to="{ name: 'Home' }"
          to="/laboratories"
          class="rounded-md bg-gray-600 px-4 py-2 text-white transition-colors hover:bg-gray-700"
        >
          Back to Home
        </RouterLink>
      </div>
      <div v-else class="text-gray-500">Loading laboratory...</div>
    </div>
  </UserLayout>
</template>
