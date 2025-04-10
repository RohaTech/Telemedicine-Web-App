<template>
    <UserLayout>
        <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50 mt-20">
          <h1 class="text-2xl font-bold mb-6">Create Appointment</h1>
          <form @submit.prevent="handleSubmit">
            <div class="mb-4">
              <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
              <input
                type="number"
                id="patient_id"
                v-model="appointmentStore.appointment.patient_id"
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
                v-model="appointmentStore.appointment.doctor_id"
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
                v-model="appointmentStore.appointment.appointment_date"
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
                v-model="appointmentStore.appointment.status"
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
              class="px-4 py-2 bg-[#048581] text-white rounded-md hover:bg-[#76e2de] transition-colors"
            >
              Create Appointment
            </button>
          </form>
          <div v-if="appointmentStore.errors.message" class="mt-4 text-red-600">
            <p>{{ appointmentStore.errors.message }}</p>
          </div>
        </div>
    </UserLayout>
  </template>
  
  <script setup>
  import { useAppointmentStore } from '@/stores/appointmentStore';
  import UserLayout from "@/layout/UserLayout.vue";

  const appointmentStore = useAppointmentStore();
  
  const handleSubmit = async () => {
    const result = await appointmentStore.createAppointment();
    if (result?.success) {
      alert(result.message);
    }
  };
  </script>