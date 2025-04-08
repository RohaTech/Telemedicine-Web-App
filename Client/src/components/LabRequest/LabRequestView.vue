<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabRequest\LabRequestView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Lab Request Details</h1>
      <div v-if="labRequestStore.errors.message" class="mb-4 text-red-600">
        {{ labRequestStore.errors.message }}
      </div>
      <div v-if="labRequest">
        <div class="mb-4">
          <strong>ID:</strong> {{ labRequest.id }}
        </div>
        <div class="mb-4">
          <strong>Consultation ID:</strong> {{ labRequest.consultation_id }}
        </div>
        <div class="mb-4">
          <strong>Doctor ID:</strong> {{ labRequest.doctor_id }}
        </div>
        <div class="mb-4">
          <strong>Patient ID:</strong> {{ labRequest.patient_id }}
        </div>
        <div class="mb-4">
          <strong>Laboratory ID:</strong> {{ labRequest.laboratory_id }}
        </div>
        <div class="mb-4">
          <strong>Test Type:</strong> {{ labRequest.test_type }}
        </div>
        <div class="mb-4">
          <strong>Status:</strong> {{ labRequest.status }}
        </div>
        <router-link
          to="/lab-requests"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading lab request...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useLabRequestStore } from '@/stores/labRequestStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const labRequestStore = useLabRequestStore();
const labRequest = ref(null);

const fetchLabRequest = async () => {
  const result = await labRequestStore.getLabRequest(route.params.id);
  if (result.success) {
    labRequest.value = result.data;
  }
};

onMounted(fetchLabRequest);
</script>