<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabResult\LabResultList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Lab Results List</h1>
      <div v-if="labResultStore.errors.message" class="mb-4 text-red-600">
        {{ labResultStore.errors.message }}
      </div>
      <div v-if="labResults.length === 0" class="text-gray-500">No lab results found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Lab Request ID</th>
            <th class="p-2 text-left">Laboratory ID</th>
            <th class="p-2 text-left">Result Details</th>
            <th class="p-2 text-left">Attachment</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="labResult in labResults" :key="labResult.id" class="border-b">
            <td class="p-2">{{ labResult.id }}</td>
            <td class="p-2">{{ labResult.lab_request_id }}</td>
            <td class="p-2">{{ labResult.laboratory_id }}</td>
            <td class="p-2">{{ labResult.result_details }}</td>
            <td class="p-2">
              <a v-if="labResult.attachment" :href="`http://127.0.0.1:8000/storage/${labResult.attachment}`" target="_blank" class="text-blue-600 hover:underline">View</a>
              <span v-else>None</span>
            </td>
            <td class="p-2">
              <router-link
                :to="{ name: 'LabResultView', params: { id: labResult.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'LabResultEdit', params: { id: labResult.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deleteLabResult(labResult.id)"
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
import { useLabResultStore } from '@/stores/labResultStore';
import UserLayout from '@/layout/UserLayout.vue';
const labResultStore = useLabResultStore();
const labResults = ref([]);

const fetchLabResults = async () => {
  const result = await labResultStore.getLabResults();
  if (result.success) {
    labResults.value = result.data;
  }
};

const deleteLabResult = async (id) => {
  if (confirm('Are you sure you want to delete this lab result?')) {
    const result = await labResultStore.deleteLabResult(id);
    if (result.success) {
      labResults.value = labResults.value.filter((res) => res.id !== id);
    }
  }
};

onMounted(fetchLabResults);
</script>