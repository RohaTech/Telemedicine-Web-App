<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabRequest\LabRequestList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Lab Requests List</h1>
      <div v-if="labRequestStore.errors.message" class="mb-4 text-red-600">
        {{ labRequestStore.errors.message }}
      </div>
      <div v-if="labRequests.length === 0" class="text-gray-500">No lab requests found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Consultation ID</th>
            <th class="p-2 text-left">Doctor ID</th>
            <th class="p-2 text-left">Patient ID</th>
            <th class="p-2 text-left">Laboratory ID</th>
            <th class="p-2 text-left">Test Type</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="labRequest in labRequests" :key="labRequest.id" class="border-b">
            <td class="p-2">{{ labRequest.id }}</td>
            <td class="p-2">{{ labRequest.consultation_id }}</td>
            <td class="p-2">{{ labRequest.doctor_id }}</td>
            <td class="p-2">{{ labRequest.patient_id }}</td>
            <td class="p-2">{{ labRequest.laboratory_id }}</td>
            <td class="p-2">{{ labRequest.test_type }}</td>
            <td class="p-2">{{ labRequest.status }}</td>
            <td class="p-2">
              <router-link
                :to="{ name: 'LabRequestView', params: { id: labRequest.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'LabRequestEdit', params: { id: labRequest.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deleteLabRequest(labRequest.id)"
                class="text-red-600 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useLabRequestStore } from '@/stores/labRequestStore';
import UserLayout from '@/layout/UserLayout.vue';
const labRequestStore = useLabRequestStore();
const labRequests = ref([]);

const fetchLabRequests = async () => {
  const result = await labRequestStore.getLabRequests();
  if (result.success) {
    labRequests.value = result.data;
  }
};

const deleteLabRequest = async (id) => {
  if (confirm('Are you sure you want to delete this lab request?')) {
    const result = await labRequestStore.deleteLabRequest(id);
    if (result.success) {
      labRequests.value = labRequests.value.filter((req) => req.id !== id);
    }
  }
};

onMounted(fetchLabRequests);
</script>