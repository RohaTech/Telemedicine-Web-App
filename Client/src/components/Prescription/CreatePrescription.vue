<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Prescription\CreatePrescription.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Create Prescription</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="doctor_id" class="block mb-1 font-semibold">Doctor ID:</label>
          <input
            type="number"
            id="doctor_id"
            v-model="prescriptionStore.prescription.doctor_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="prescriptionStore.errors.doctor_id" class="text-red-600 text-sm mt-1">
            {{ prescriptionStore.errors.doctor_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
          <input
            type="number"
            id="patient_id"
            v-model="prescriptionStore.prescription.patient_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="prescriptionStore.errors.patient_id" class="text-red-600 text-sm mt-1">
            {{ prescriptionStore.errors.patient_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="consultation_id" class="block mb-1 font-semibold">Consultation ID:</label>
          <input
            type="number"
            id="consultation_id"
            v-model="prescriptionStore.prescription.consultation_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="prescriptionStore.errors.consultation_id" class="text-red-600 text-sm mt-1">
            {{ prescriptionStore.errors.consultation_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="medicine_name" class="block mb-1 font-semibold">Medicine Name:</label>
          <input
            type="text"
            id="medicine_name"
            v-model="prescriptionStore.prescription.medicine_name"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="prescriptionStore.errors.medicine_name" class="text-red-600 text-sm mt-1">
            {{ prescriptionStore.errors.medicine_name }}
          </p>
        </div>
        <div class="mb-4">
          <label for="directions" class="block mb-1 font-semibold">Directions:</label>
          <textarea
            id="directions"
            v-model="prescriptionStore.prescription.directions"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
            rows="4"
          ></textarea>
          <p v-if="prescriptionStore.errors.directions" class="text-red-600 text-sm mt-1">
            {{ prescriptionStore.errors.directions }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Create Prescription
        </button>
      </form>
      <div v-if="prescriptionStore.errors.message" class="mt-4 text-red-600">
        {{ prescriptionStore.errors.message }}
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { usePrescriptionStore } from '@/stores/prescriptionStore';
import UserLayout from '@/layout/UserLayout.vue';
const prescriptionStore = usePrescriptionStore();

const handleSubmit = async () => {
  const result = await prescriptionStore.createPrescription();
  if (result.success) {
    alert(result.message);
  }
};
</script>