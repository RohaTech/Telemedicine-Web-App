<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Prescription\PrescriptionView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Prescription Details</h1>
      <div v-if="prescriptionStore.errors.message" class="mb-4 text-red-600">
        {{ prescriptionStore.errors.message }}
      </div>
      <div v-if="prescription">
        <div class="mb-4"><strong>ID:</strong> {{ prescription.id }}</div>
        <div class="mb-4"><strong>Doctor:</strong> {{ prescription.doctor ? prescription.doctor.name : 'N/A' }}</div>
        <div class="mb-4"><strong>Patient:</strong> {{ prescription.patient ? prescription.patient.name : 'N/A' }}</div>
        <div class="mb-4"><strong>Consultation ID:</strong> {{ prescription.consultation_id }}</div>
        <div class="mb-4"><strong>Medicine:</strong> {{ prescription.medicine_name }}</div>
        <div class="mb-4"><strong>Directions:</strong> {{ prescription.directions }}</div>
        <router-link
          to="/prescriptions"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading prescription...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePrescriptionStore } from '@/stores/prescriptionStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const prescriptionStore = usePrescriptionStore();
const prescription = ref(null);

const fetchPrescription = async () => {
  const result = await prescriptionStore.getPrescription(route.params.id);
  if (result.success) {
    prescription.value = result.data;
  }
};

onMounted(fetchPrescription);
</script>