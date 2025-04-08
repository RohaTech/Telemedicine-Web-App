<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Consultation\ConsultationView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Consultation Details</h1>
      <div v-if="consultationStore.errors.message" class="mb-4 text-red-600">
        {{ consultationStore.errors.message }}
      </div>
      <div v-if="consultation">
        <div class="mb-4">
          <strong>ID:</strong> {{ consultation.id }}
        </div>
        <div class="mb-4">
          <strong>Patient ID:</strong> {{ consultation.patient_id }}
        </div>
        <div class="mb-4">
          <strong>Doctor ID:</strong> {{ consultation.doctor_id }}
        </div>
        <div class="mb-4">
          <strong>Prescription ID:</strong> {{ consultation.prescription_id || 'None' }}
        </div>
        <div class="mb-4">
          <strong>Date:</strong> {{ consultation.consultation_date }}
        </div>
        <div class="mb-4">
          <strong>Notes:</strong> {{ consultation.notes || 'None' }}
        </div>
        <router-link
          to="/consultations"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading consultation...</div>
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
    consultation.value = result.data;
  }
};

onMounted(fetchConsultation);
</script>