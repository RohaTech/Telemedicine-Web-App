<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Consultation\CreateConsultation.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Create Consultation</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
          <input
            type="number"
            id="patient_id"
            v-model="consultationStore.consultation.patient_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="consultationStore.errors.patient_id" class="text-red-600 text-sm mt-1">
            {{ consultationStore.errors.patient_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="doctor_id" class="block mb-1 font-semibold">Doctor ID:</label>
          <input
            type="number"
            id="doctor_id"
            v-model="consultationStore.consultation.doctor_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="consultationStore.errors.doctor_id" class="text-red-600 text-sm mt-1">
            {{ consultationStore.errors.doctor_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="prescription_id" class="block mb-1 font-semibold">Prescription ID (optional):</label>
          <input
            type="number"
            id="prescription_id"
            v-model="consultationStore.consultation.prescription_id"
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="consultationStore.errors.prescription_id" class="text-red-600 text-sm mt-1">
            {{ consultationStore.errors.prescription_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="consultation_date" class="block mb-1 font-semibold">Consultation Date:</label>
          <input
            type="date"
            id="consultation_date"
            v-model="consultationStore.consultation.consultation_date"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="consultationStore.errors.consultation_date" class="text-red-600 text-sm mt-1">
            {{ consultationStore.errors.consultation_date }}
          </p>
        </div>
        <div class="mb-4">
          <label for="notes" class="block mb-1 font-semibold">Notes (optional):</label>
          <textarea
            id="notes"
            v-model="consultationStore.consultation.notes"
            class="w-full p-2 border border-gray-300 rounded-md"
            rows="4"
          ></textarea>
          <p v-if="consultationStore.errors.notes" class="text-red-600 text-sm mt-1">
            {{ consultationStore.errors.notes }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Create Consultation
        </button>
      </form>
      <div v-if="consultationStore.errors.message" class="mt-4 text-red-600">
        <p>{{ consultationStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { useConsultationStore } from '@/stores/consultationStore';
import UserLayout from "@/layout/UserLayout.vue";

const consultationStore = useConsultationStore();

const handleSubmit = async () => {
  const result = await consultationStore.createConsultation();
  if (result.success) {
    alert(result.message);
  }
};
</script>