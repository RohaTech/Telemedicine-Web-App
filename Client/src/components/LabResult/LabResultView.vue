<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabResult\LabResultView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Lab Result Details</h1>
      <div v-if="labResultStore.errors.message" class="mb-4 text-red-600">
        {{ labResultStore.errors.message }}
      </div>
      <div v-if="labResult">
        <div class="mb-4">
          <strong>ID:</strong> {{ labResult.id }}
        </div>
        <div class="mb-4">
          <strong>Lab Request ID:</strong> {{ labResult.lab_request_id }}
        </div>
        <div class="mb-4">
          <strong>Laboratory ID:</strong> {{ labResult.laboratory_id }}
        </div>
        <div class="mb-4">
          <strong>Result Details:</strong> {{ labResult.result_details }}
        </div>
        <div class="mb-4">
          <strong>Attachment:</strong>
          <a v-if="labResult.attachment" :href="`http://127.0.0.1:8000/storage/${labResult.attachment}`" target="_blank" class="text-blue-600 hover:underline">View Attachment</a>
          <span v-else>None</span>
        </div>
        <router-link
          to="/lab-results"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading lab result...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useLabResultStore } from '@/stores/labResultStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const labResultStore = useLabResultStore();
const labResult = ref(null);

const fetchLabResult = async () => {
  const result = await labResultStore.getLabResult(route.params.id);
  if (result.success) {
    labResult.value = result.data;
  }
};

onMounted(fetchLabResult);
</script>