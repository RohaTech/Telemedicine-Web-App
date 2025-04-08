<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Consultation\ConsultationList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Consultations List</h1>
      <div v-if="consultationStore.errors.message" class="mb-4 text-red-600">
        {{ consultationStore.errors.message }}
      </div>
      <div v-if="consultations.length === 0" class="text-gray-500">No consultations found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Patient ID</th>
            <th class="p-2 text-left">Doctor ID</th>
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="consultation in consultations" :key="consultation.id" class="border-b">
            <td class="p-2">{{ consultation.id }}</td>
            <td class="p-2">{{ consultation.patient_id }}</td>
            <td class="p-2">{{ consultation.doctor_id }}</td>
            <td class="p-2">{{ consultation.consultation_date }}</td>
            <td class="p-2">
              <router-link
                :to="{ name: 'ConsultationView', params: { id: consultation.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'ConsultationEdit', params: { id: consultation.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deleteConsultation(consultation.id)"
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
import { useConsultationStore } from '@/stores/consultationStore';
import UserLayout from "@/layout/UserLayout.vue";

const consultationStore = useConsultationStore();
const consultations = ref([]);

const fetchConsultations = async () => {
  const result = await consultationStore.getConsultations();
  if (result.success) {
    consultations.value = result.data;
  }
};

const deleteConsultation = async (id) => {
  if (confirm('Are you sure you want to delete this consultation?')) {
    const result = await consultationStore.deleteConsultation(id);
    if (result.success) {
      consultations.value = consultations.value.filter((consult) => consult.id !== id);
    }
  }
};

onMounted(fetchConsultations);
</script>