<script setup>
import { onMounted, ref, computed } from "vue";
import Modal from "@/components/UI/Modal.vue";
import UserLayout from "@/layout/UserLayout.vue";
import { useAppointmentStore } from "@/stores/appointmentStore";

const { getUserAppointments } = useAppointmentStore();

const appointments = ref([]);

const statusOptions = ["confirmed", "pending", "cancelled", "waiting"];
const selectedStatus = ref("all"); // Default to 'all'

onMounted(async () => {
  appointments.value = await getUserAppointments();
  console.log("appointments.value", appointments.value);
});

const filteredAppointments = computed(() => {
  if (selectedStatus.value === "all") {
    return appointments.value?.data;
  }
  return appointments.value?.data.filter(
    (lab) => lab.status === selectedStatus.value,
  );
});

// Function to change the selected status
const changeStatus = (status) => {
  selectedStatus.value = status;
};
</script>

<template>
  <UserLayout>
    <div
      class="ark:border-gray-800 ark:bg-white/[0.03] bg- -400 mx-auto mt-16 min-h-fit max-w-[1600px] overflow-hidden rounded-xl border border-gray-200 bg-white pb-10 shadow-sm"
    >
      <!-- Status Filter -->
      <div class="border-b border-gray-200 p-4">
        <h3 class="my-4 mb-2 border-b py-8 text-2xl font-semibold">
          All Your Appointments
        </h3>
        <div class="flex flex-wrap gap-3 py-3">
          <!-- "All" option -->
          <div class="flex items-center">
            <input
              type="radio"
              id="status-all"
              name="status-filter"
              :checked="selectedStatus === 'all'"
              @change="changeStatus('all')"
              class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <label
              for="status-all"
              class="ml-2 cursor-pointer text-sm capitalize text-gray-700"
              >All</label
            >
          </div>

          <!-- Individual status options -->
          <div
            v-for="status in statusOptions"
            :key="status"
            class="flex items-center"
          >
            <input
              type="radio"
              :id="'status-' + status"
              name="status-filter"
              :checked="selectedStatus === status"
              @change="changeStatus(status)"
              class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <label
              :for="'status-' + status"
              class="ml-2 cursor-pointer text-sm capitalize text-gray-700"
              >{{ status }}</label
            >
          </div>
        </div>
      </div>

      <div class="custom-scrollbar max-w-full overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="ark:border-gray-70 0 border-b border-gray-200">
              <th class="w-2/11 px-5 py-3 text-left sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Name
                </p>
              </th>

              <th class="w-2/11 px-5 py-3 text-left sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Email
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 text-left sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Phone
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 text-left sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Booked Date
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 sm:px-6">
                <p class="text-left text-theme-xs font-medium text-gray-500">
                  Status
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 text-center sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">Action</p>
              </th>
            </tr>
          </thead>
          <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
            <!-- No results message -->
            <tr v-if="filteredAppointments?.length === 0">
              <td colspan="7" class="px-5 py-8 text-center sm:px-6">
                <div class="flex flex-col items-center justify-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <p class="mt-2 text-base text-gray-500">
                    No appointments found with status:
                    <span class="font-medium capitalize">{{
                      selectedStatus
                    }}</span>
                  </p>
                  <button
                    @click="changeStatus('all')"
                    class="mt-4 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Show All appointments
                  </button>
                </div>
              </td>
            </tr>
            <tr
              v-else
              v-for="(appointment, index) in filteredAppointments"
              :key="index"
              class="ark:border-gray-800 border-t border-gray-100"
            >
              <td class="px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <span class="block text-theme-sm font-medium text-gray-800">
                      {{ appointment.doctor?.name }}
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-theme-sm text-gray-500">
                  {{ appointment.doctor?.email }}
                </p>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <div class="flex -space-x-2">
                  {{ appointment.doctor?.phone }}
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <span>
                  {{
                    appointment.appointment_date
                      ? appointment.appointment_date.split("T")[0]
                      : ""
                  }}
                </span>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-theme-sm text-gray-500">
                  <span
                    :class="[
                      'rounded-lg px-2 py-1 text-sm font-medium',
                      {
                        'ark:bg-success-500/15 ark:text-success-500 bg-success-50 text-success-700':
                          appointment.status === 'confirmed',
                        'ark:bg-warning-500/15 ark:text-warning-400 bg-blue-50 text-blue-700':
                          appointment.status === 'pending',
                        'ark:bg-error-500/15 ark:text-error-500 bg-error-50 text-error-700':
                          appointment.status === 'cancelled',
                        'ark:bg-error-500/15 ark:text-error-500 bg-gray-100 text-gray-600':
                          appointment.status === 'waiting',
                      },
                    ]"
                  >
                    {{ appointment.status }}</span
                  >
                </p>
              </td>
              <td class="flex justify-center px-5 py-4 sm:px-6">
                <div
                  v-if="appointment.status === 'waiting'"
                  class="ml-2 flex w-[200px] cursor-wait justify-center gap-x-2 bg-gray-100 p-2"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-5 fill-gray-600"
                    viewBox="0 0 256 256"
                  >
                    <path
                      d="M200,75.64V40a16,16,0,0,0-16-16H72A16,16,0,0,0,56,40V76a16.07,16.07,0,0,0,6.4,12.8L114.67,128,62.4,167.2A16.07,16.07,0,0,0,56,180v36a16,16,0,0,0,16,16H184a16,16,0,0,0,16-16V180.36a16.08,16.08,0,0,0-6.35-12.76L141.27,128l52.38-39.6A16.05,16.05,0,0,0,200,75.64ZM178.23,176H77.33L128,138ZM72,216V192H184v24ZM184,75.64,128,118,72,76V40H184Z"
                    ></path>
                  </svg>

                  <span class="text-sm text-gray-600"
                    >Waiting Confirmation</span
                  >
                </div>
                <div
                  v-if="appointment.status === 'pending'"
                  class="ml-2 flex w-[200px] cursor-pointer justify-center gap-x-2 bg-blue-50 p-2"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-5 fill-blue-500"
                    viewBox="0 0 256 256"
                  >
                    <path
                      d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-96-88v64a8,8,0,0,1-16,0V132.94l-4.42,2.22a8,8,0,0,1-7.16-14.32l16-8A8,8,0,0,1,112,120Zm59.16,30.45L152,176h16a8,8,0,0,1,0,16H136a8,8,0,0,1-6.4-12.8l28.78-38.37A8,8,0,1,0,145.07,132a8,8,0,1,1-13.85-8A24,24,0,0,1,176,136,23.76,23.76,0,0,1,171.16,150.45Z"
                    ></path>
                  </svg>
                  <span class="text-sm text-blue-600"
                    >Consultation Reserved</span
                  >
                </div>
                <div
                  v-if="appointment.status === 'confirmed'"
                  class="ml-2 flex w-[200px] cursor-pointer justify-center gap-x-2 bg-success-50 p-2"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-5 fill-green-600"
                    viewBox="0 0 256 256"
                  >
                    <path
                      d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM224,48V208a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32H208A16,16,0,0,1,224,48ZM208,208V48H48V208H208Z"
                    ></path>
                  </svg>
                  <span class="text-sm text-green-600"
                    >Consultation Completed</span
                  >
                </div>
                <div
                  v-if="appointment.status === 'cancelled'"
                  class="ml-2 flex w-[200px] cursor-pointer justify-center gap-x-2 bg-error-50 p-2"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-5 fill-red-600"
                    viewBox="0 0 256 256"
                  >
                    <path
                      d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM224,48V208a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32H208A16,16,0,0,1,224,48ZM208,208V48H48V208H208Z"
                    ></path>
                  </svg>
                  <span class="text-sm text-red-600">Canceled</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Using the Modal component -->
    </div>
  </UserLayout>
</template>
