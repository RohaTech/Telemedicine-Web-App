<template>
  <UserLayout>
    <div
      class="mx-auto max-w-2xl rounded-md border border-gray-300 bg-gray-50 p-5"
    >
      <h1 class="mb-6 text-2xl font-bold">Create Lab Request</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="consultation_id" class="mb-1 block font-semibold"
            >Consultation ID:</label
          >
          <input
            type="number"
            id="consultation_id"
            v-model="labRequestStore.labRequest.consultation_id"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="labRequestStore.errors.consultation_id"
            class="mt-1 text-sm text-red-600"
          >
            {{ labRequestStore.errors.consultation_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="doctor_id" class="mb-1 block font-semibold"
            >Doctor ID:</label
          >
          <input
            type="number"
            id="doctor_id"
            v-model="labRequestStore.labRequest.doctor_id"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="labRequestStore.errors.doctor_id"
            class="mt-1 text-sm text-red-600"
          >
            {{ labRequestStore.errors.doctor_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="patient_id" class="mb-1 block font-semibold"
            >Patient ID:</label
          >
          <input
            type="number"
            id="patient_id"
            v-model="labRequestStore.labRequest.patient_id"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="labRequestStore.errors.patient_id"
            class="mt-1 text-sm text-red-600"
          >
            {{ labRequestStore.errors.patient_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="laboratory_id" class="mb-1 block font-semibold"
            >Laboratory ID:</label
          >
          <input
            type="number"
            id="laboratory_id"
            v-model="labRequestStore.labRequest.laboratory_id"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="labRequestStore.errors.laboratory_id"
            class="mt-1 text-sm text-red-600"
          >
            {{ labRequestStore.errors.laboratory_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="test_type" class="mb-1 block font-semibold"
            >Test Type:</label
          >
          <input
            type="text"
            id="test_type"
            v-model="labRequestStore.labRequest.test_type"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="labRequestStore.errors.test_type"
            class="mt-1 text-sm text-red-600"
          >
            {{ labRequestStore.errors.test_type }}
          </p>
        </div>
        <div class="mb-4">
          <label for="status" class="mb-1 block font-semibold">Status:</label>
          <select
            id="status"
            v-model="labRequestStore.labRequest.status"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          >
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <p
            v-if="labRequestStore.errors.status"
            class="mt-1 text-sm text-red-600"
          >
            {{ labRequestStore.errors.status }}
          </p>
        </div>
        <button
          type="submit"
          class="rounded-md bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
        >
          Create Lab Request
        </button>
      </form>
      <div v-if="labRequestStore.errors.message" class="mt-4 text-red-600">
        <p>{{ labRequestStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { useLabRequestStore } from "@/stores/labRequestStore";
import UserLayout from "@/layout/UserLayout.vue";
const labRequestStore = useLabRequestStore();

const handleSubmit = async () => {
  const result = await labRequestStore.createLabRequest();
  if (result.success) {
    alert(result.message);
  }
};
</script>
