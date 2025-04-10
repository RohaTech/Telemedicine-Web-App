<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Appointment\AppointmentList.vue -->
<template>
  <UserLayout>
    <div class="mx-auto max-w-4xl p-5">
      <h1 class="mb-6 text-2xl font-bold">Appointments List</h1>
      <div v-if="appointmentStore.errors.message" class="mb-4 text-red-600">
        {{ appointmentStore.errors.message }}
      </div>
      <div v-if="appointments.length === 0" class="text-gray-500">
        No appointments found.
      </div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <!-- <th class="p-2 text-left">ID</th> -->
            <th class="p-2 text-left">Patient ID</th>
            <th class="p-2 text-left">Doctor ID</th>
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="appointment in appointments"
            :key="appointment.id"
            class="border-b"
          >
            <!-- <td class="p-2">{{ appointment.id }}</td> -->
            <td class="p-2">{{ appointment.patient_id }}</td>
            <td class="p-2">{{ appointment.doctor_id }}</td>
            <td class="p-2">{{ appointment.appointment_date }}</td>
            <td class="p-2">{{ appointment.status }}</td>
            <td class="p-2">
              <router-link
                :to="{
                  name: 'AppointmentView',
                  params: { id: appointment.id },
                }"
                class="mr-2 text-blue-600 hover:underline"
              >
                View
              </router-link>
              <router-link
                :to="{
                  name: 'AppointmentEdit',
                  params: { id: appointment.id },
                }"
                class="mr-2 text-green-600 hover:underline"
              >
                Edit
              </router-link>
              <button
                @click="deleteAppointment(appointment.id)"
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
import { ref, onMounted } from "vue";
import { useAppointmentStore } from "@/stores/appointmentStore";
import { useRouter } from "vue-router";
import UserLayout from "@/layout/UserLayout.vue";
const appointmentStore = useAppointmentStore();
const router = useRouter();
const appointments = ref([]);

const fetchAppointments = async () => {
  const result = await appointmentStore.getAppointments();
  if (result.success) {
    appointments.value = result.data;
  }
};

const deleteAppointment = async (id) => {
  if (confirm("Are you sure you want to delete this appointment?")) {
    const result = await appointmentStore.deleteAppointment(id);
    if (result.success) {
      appointments.value = appointments.value.filter((appt) => appt.id !== id);
    }
  }
};

onMounted(fetchAppointments);
</script>
