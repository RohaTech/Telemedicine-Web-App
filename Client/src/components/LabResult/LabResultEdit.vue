<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabResult\LabResultEdit.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Edit Lab Result</h1>
      <form @submit.prevent="handleSubmit" v-if="labResult">
        <div class="mb-4">
          <label for="lab_request_id" class="block mb-1 font-semibold">Lab Request ID:</label>
          <input
            type="number"
            id="lab_request_id"
            v-model="labResult.lab_request_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labResultStore.errors.lab_request_id" class="text-red-600 text-sm mt-1">
            {{ labResultStore.errors.lab_request_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="laboratory_id" class="block mb-1 font-semibold">Laboratory ID:</label>
          <input
            type="number"
            id="laboratory_id"
            v-model="labResult.laboratory_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labResultStore.errors.laboratory_id" class="text-red-600 text-sm mt-1">
            {{ labResultStore.errors.laboratory_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="result_details" class="block mb-1 font-semibold">Result Details:</label>
          <textarea
            id="result_details"
            v-model="labResult.result_details"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
            rows="4"
          ></textarea>
          <p v-if="labResultStore.errors.result_details" class="text-red-600 text-sm mt-1">
            {{ labResultStore.errors.result_details }}
          </p>
        </div>
        <div class="mb-4">
          <label for="attachment" class="block mb-1 font-semibold">Attachment (optional):</label>
          <div v-if="labResult.attachment" class="mb-2">
            Current: <a :href="`/storage/${labResult.attachment}`" target="_blank" class="text-blue-600 hover:underline">{{ labResult.attachment }}</a>
          </div>
          <input
            type="file"
            id="attachment"
            @change="handleFileChange"
            accept=".pdf,.jpg,.jpeg,.png"
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="labResultStore.errors.attachment" class="text-red-600 text-sm mt-1">
            {{ labResultStore.errors.attachment }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors mr-2"
        >
          Update Lab Result
        </button>
        <router-link
          to="/lab-results"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Cancel
        </router-link>
      </form>
      <div v-if="labResultStore.errors.message" class="mt-4 text-red-600">
        <p>{{ labResultStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useLabResultStore } from '@/stores/labResultStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const router = useRouter();
const labResultStore = useLabResultStore();
const labResult = ref(null);

const fetchLabResult = async () => {
  const result = await labResultStore.getLabResult(route.params.id);
  if (result.success) {
    labResult.value = { ...result.data };
    labResultStore.labResult = { ...result.data, attachment: null }; // Reset attachment for new upload
  }
};

const handleFileChange = (event) => {
  labResultStore.labResult.attachment = event.target.files[0];
};

const handleSubmit = async () => {
  labResultStore.labResult.lab_request_id = labResult.value.lab_request_id;
  labResultStore.labResult.laboratory_id = labResult.value.laboratory_id;
  labResultStore.labResult.result_details = labResult.value.result_details;

  const result = await labResultStore.updateLabResult(route.params.id);
  if (result.success) {
    alert(result.message);
    labResult.value = { ...result.data }; // Update local state with response
  }
};

onMounted(fetchLabResult);
</script>