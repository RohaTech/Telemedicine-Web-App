<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Laboratory\LaboratoryView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Laboratory Details</h1>
      <div v-if="laboratoryStore.errors.message" class="mb-4 text-red-600">
        {{ laboratoryStore.errors.message }}
      </div>
      <div v-if="laboratory">
        <div class="mb-4">
          <strong>ID:</strong> {{ laboratory.id }}
        </div>
        <div class="mb-4">
          <strong>Name:</strong> {{ laboratory.name }}
        </div>
        <div class="mb-4">
          <strong>Email:</strong> {{ laboratory.email }}
        </div>
        <div class="mb-4">
          <strong>Phone:</strong> {{ laboratory.phone }}
        </div>
        <div class="mb-4">
          <strong>Address:</strong> {{ laboratory.address }}
        </div>
        <router-link
          to="/laboratories"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading laboratory...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useLaboratoryStore } from '@/stores/laboratoryStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const laboratoryStore = useLaboratoryStore();
const laboratory = ref(null);

const fetchLaboratory = async () => {
  const result = await laboratoryStore.getLaboratory(route.params.id);
  if (result.success) {
    laboratory.value = result.data;
  }
};

onMounted(fetchLaboratory);
</script>