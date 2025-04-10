<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Appointment\AppointmentEdit.vue -->
<template>
    <UserLayout>
        <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50 mt-12">
          <h1 class="text-2xl font-bold mb-6">Edit Appointment</h1>
          <form @submit.prevent="handleSubmit" v-if="appointment">
            <div class="mb-4">
              <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
              <input
                type="number"
                id="patient_id"
                v-model="appointment.patient_id"
                required
                class="w-full p-2 border border-gray-300 rounded-md"
              />
              <p v-if="appointmentStore.errors.patient_id" class="text-red-600 text-sm mt-1">
                {{ appointmentStore.errors.patient_id }}
              </p>
            </div>
            <div class="mb-4">
              <label for="doctor_id" class="block mb-1 font-semibold">Doctor ID:</label>
              <input
                type="number"
                id="doctor_id"
                v-model="appointment.doctor_id"
                required
                class="w-full p-2 border border-gray-300 rounded-md"
              />
              <p v-if="appointmentStore.errors.doctor_id" class="text-red-600 text-sm mt-1">
                {{ appointmentStore.errors.doctor_id }}
              </p>
            </div>
            <div class="mb-4">
              <label for="appointment_date" class="block mb-1 font-semibold">Appointment Date:</label>
              <input
                type="date"
                id="appointment_date"
                v-model="appointment.appointment_date"
                required
                class="w-full p-2 border border-gray-300 rounded-md"
              />
              <p v-if="appointmentStore.errors.appointment_date" class="text-red-600 text-sm mt-1">
                {{ appointmentStore.errors.appointment_date }}
              </p>
            </div>
            <div class="mb-4">
              <label for="status" class="block mb-1 font-semibold">Status:</label>
              <select
                id="status"
                v-model="appointment.status"
                required
                class="w-full p-2 border border-gray-300 rounded-md"
              >
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
              </select>
              <p v-if="appointmentStore.errors.status" class="text-red-600 text-sm mt-1">
                {{ appointmentStore.errors.status }}
              </p>
            </div>
            <button
              type="submit"
              class="px-4 py-2 bg-[#048581] text-white rounded-md hover:bg-[#35d5d0] transition-colors mr-2"
            >
              Update Appointment
            </button>
            <router-link
              to="/appointments"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
            >
              Cancel
            </router-link>
          </form>
          <div v-if="appointmentStore.errors.message" class="mt-4 text-red-600">
            <p>{{ appointmentStore.errors.message }}</p>
          </div>
        </div>
    </UserLayout>
    
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import { useAppointmentStore } from '@/stores/appointmentStore';
  import UserLayout from "@/layout/UserLayout.vue";
  const route = useRoute();
  const appointmentStore = useAppointmentStore();
  const appointment = ref(null);
  
  const fetchAppointment = async () => {
    const result = await appointmentStore.getAppointment(route.params.id);
    if (result.success) {
      appointment.value = { ...result.data };
    }
  };
  
  const handleSubmit = async () => {
    const result = await appointmentStore.updateAppointment(route.params.id, appointment.value);
    if (result.success) {
      alert('Appointment updated successfully!');
      router.push({ name: 'Appointments' });
    }
  };
  
  onMounted(fetchAppointment);
  </script>