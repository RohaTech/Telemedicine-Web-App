<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\LabResult\CreateLabResult.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Create Lab Result</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="lab_request_id" class="block mb-1 font-semibold">Lab Request ID:</label>
          <input
            type="number"
            id="lab_request_id"
            v-model="labResultStore.labResult.lab_request_id"
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
            v-model="labResultStore.labResult.laboratory_id"
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
            v-model="labResultStore.labResult.result_details"
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
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Create Lab Result
        </button>
      </form>
      <div v-if="labResultStore.errors.message" class="mt-4 text-red-600">
        <p>{{ labResultStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { useLabResultStore } from '@/stores/labResultStore';
import UserLayout from '@/layout/UserLayout.vue';
const labResultStore = useLabResultStore();

const handleFileChange = (event) => {
  labResultStore.labResult.attachment = event.target.files[0];
};

const handleSubmit = async () => {
  const result = await labResultStore.createLabResult();
  if (result.success) {
    alert(result.message);
  }
};
</script>