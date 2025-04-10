<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Consultation\ConsultationEdit.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Edit Consultation</h1>
      <form @submit.prevent="handleSubmit" v-if="consultation">
        <div class="mb-4">
          <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
          <input
            type="number"
            id="patient_id"
            v-model="consultation.patient_id"
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
            v-model="consultation.doctor_id"
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
            v-model="consultation.prescription_id"
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
            v-model="consultation.consultation_date"
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
            v-model="consultation.notes"
            class="w-full p-2 border border-gray-300 rounded-md"
            rows="4"
          ></textarea>
          <p v-if="consultationStore.errors.notes" class="text-red-600 text-sm mt-1">
            {{ consultationStore.errors.notes }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors mr-2"
        >
          Update Consultation
        </button>
        <router-link
          to="/consultations"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Cancel
        </router-link>
      </form>
      <div v-if="consultationStore.errors.message" class="mt-4 text-red-600">
        <p>{{ consultationStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useConsultationStore } from '@/stores/consultationStore';
import UserLayout from "@/layout/UserLayout.vue";

const route = useRoute();
const consultationStore = useConsultationStore();
const consultation = ref(null);

const fetchConsultation = async () => {
  const result = await consultationStore.getConsultation(route.params.id);
  if (result.success) {
    consultation.value = { ...result.data };
  }
};

const handleSubmit = async () => {
  const result = await consultationStore.updateConsultation(route.params.id, consultation.value);
  if (result.success) {
    alert(result.message);
    router.push({ name: 'Consultations' });
  }
};

onMounted(fetchConsultation);
</script>