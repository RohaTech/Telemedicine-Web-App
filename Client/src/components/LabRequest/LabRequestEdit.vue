<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabRequest\LabRequestEdit.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Edit Lab Request</h1>
      <form @submit.prevent="handleSubmit" v-if="labRequest">
        <div class="mb-4">
          <label for="consultation_id" class="block mb-1 font-semibold">Consultation ID:</label>
          <input
            type="number"
            id="consultation_id"
            v-model="labRequest.consultation_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labRequestStore.errors.consultation_id" class="text-red-600 text-sm mt-1">
            {{ labRequestStore.errors.consultation_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="doctor_id" class="block mb-1 font-semibold">Doctor ID:</label>
          <input
            type="number"
            id="doctor_id"
            v-model="labRequest.doctor_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labRequestStore.errors.doctor_id" class="text-red-600 text-sm mt-1">
            {{ labRequestStore.errors.doctor_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
          <input
            type="number"
            id="patient_id"
            v-model="labRequest.patient_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labRequestStore.errors.patient_id" class="text-red-600 text-sm mt-1">
            {{ labRequestStore.errors.patient_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="laboratory_id" class="block mb-1 font-semibold">Laboratory ID:</label>
          <input
            type="number"
            id="laboratory_id"
            v-model="labRequest.laboratory_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labRequestStore.errors.laboratory_id" class="text-red-600 text-sm mt-1">
            {{ labRequestStore.errors.laboratory_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="test_type" class="block mb-1 font-semibold">Test Type:</label>
          <input
            type="text"
            id="test_type"
            v-model="labRequest.test_type"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labRequestStore.errors.test_type" class="text-red-600 text-sm mt-1">
            {{ labRequestStore.errors.test_type }}
          </p>
        </div>
        <div class="mb-4">
          <label for="status" class="block mb-1 font-semibold">Status:</label>
          <select
            id="status"
            v-model="labRequest.status"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          >
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <p v-if="labRequestStore.errors.status" class="text-red-600 text-sm mt-1">
            {{ labRequestStore.errors.status }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors mr-2"
        >
          Update Lab Request
        </button>
        <router-link
          to="/lab-requests"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Cancel
        </router-link>
      </form>
      <div v-if="labRequestStore.errors.message" class="mt-4 text-red-600">
        <p>{{ labRequestStore.errors.message }}</p>
      </div>
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
    labRequest.value = { ...result.data };
  }
};

const handleSubmit = async () => {
  const result = await labRequestStore.updateLabRequest(route.params.id, labRequest.value);
  if (result.success) {
    alert(result.message);
    router.push({ name: 'LabRequests' });
  }
};

onMounted(fetchLabRequest);
</script>