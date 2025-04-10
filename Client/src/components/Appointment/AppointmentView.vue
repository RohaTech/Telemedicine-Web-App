<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Appointment\AppointmentView.vue -->
<template>
  <UserLayout>
    <div
      class="mx-auto max-w-2xl rounded-md border border-gray-300 bg-gray-50 p-5"
    >
      <h1 class="mb-6 text-2xl font-bold">Appointment Details</h1>
      <div v-if="appointmentStore.errors.message" class="mb-4 text-red-600">
        {{ appointmentStore.errors.message }}
      </div>
      <div v-if="appointment">
        <div class="mb-4"><strong>ID:</strong> {{ appointment.id }}</div>
        <div class="mb-4">
          <strong>Patient ID:</strong> {{ appointment.patient_id }}
        </div>
        <div class="mb-4">
          <strong>Doctor ID:</strong> {{ appointment.doctor_id }}
        </div>
        <div class="mb-4">
          <strong>Date:</strong> {{ appointment.appointment_date }}
        </div>
        <div class="mb-4">
          <strong>Status:</strong> {{ appointment.status }}
        </div>
        <router-link
          to="/appointments"
          class="rounded-md bg-gray-600 px-4 py-2 text-white transition-colors hover:bg-gray-700"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading appointment...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useAppointmentStore } from "@/stores/appointmentStore";
import UserLayout from "@/layout/UserLayout.vue";
const route = useRoute();
const appointmentStore = useAppointmentStore();
const appointment = ref(null);

const fetchAppointment = async () => {
  const result = await appointmentStore.getAppointment(route.params.id);
  if (result.success) {
    appointment.value = result.data;
  }
};

onMounted(fetchAppointment);
</script>
