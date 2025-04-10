<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Prescription\PrescriptionList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Prescriptions List</h1>
      <div v-if="prescriptionStore.errors.message" class="mb-4 text-red-600">
        {{ prescriptionStore.errors.message }}
      </div>
      <div v-if="prescriptions.length === 0" class="text-gray-500">No prescriptions found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Doctor</th>
            <th class="p-2 text-left">Patient</th>
            <th class="p-2 text-left">Consultation ID</th>
            <th class="p-2 text-left">Medicine</th>
            <th class="p-2 text-left">Directions</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="prescription in prescriptions" :key="prescription.id" class="border-b">
            <td class="p-2">{{ prescription.id }}</td>
            <td class="p-2">{{ prescription.doctor ? prescription.doctor.name : 'N/A' }}</td>
            <td class="p-2">{{ prescription.patient ? prescription.patient.name : 'N/A' }}</td>
            <td class="p-2">{{ prescription.consultation_id }}</td>
            <td class="p-2">{{ prescription.medicine_name }}</td>
            <td class="p-2">{{ prescription.directions }}</td>
            <td class="p-2">
              <router-link
                :to="{ name: 'PrescriptionView', params: { id: prescription.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'PrescriptionEdit', params: { id: prescription.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deletePrescription(prescription.id)"
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
import { usePrescriptionStore } from '@/stores/prescriptionStore';
import UserLayout from '@/layout/UserLayout.vue';
const prescriptionStore = usePrescriptionStore();
const prescriptions = ref([]);

const fetchPrescriptions = async () => {
  const result = await prescriptionStore.getPrescriptions();
  if (result.success) {
    prescriptions.value = result.data;
  }
};

const deletePrescription = async (id) => {
  if (confirm('Are you sure you want to delete this prescription?')) {
    const result = await prescriptionStore.deletePrescription(id);
    if (result.success) {
      prescriptions.value = prescriptions.value.filter((p) => p.id !== id);
    }
  }
};

onMounted(fetchPrescriptions);
</script>