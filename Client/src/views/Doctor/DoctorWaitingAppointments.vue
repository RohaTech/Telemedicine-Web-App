<script setup>
import DoctorLayout from "@/layout/DoctorLayout.vue";
import Modal from "@/components/UI/Modal.vue";
import { ref, onMounted, computed } from "vue";
import { useAppointmentStore } from "@/stores/appointmentStore";
import { useConsultationStore } from "@/stores/consultationStore";
import { useToast } from "vue-toastification";

const appointmentStore = useAppointmentStore();
const consultationStore = useConsultationStore();
const toast = useToast();

const waitingAppointments = ref([]);
const isOpen = ref(false);
const selectedAppointment = ref(null);
const consultationDate = ref("");
const consultationTime = ref("");
const isLoading = ref(false);
const showCalendar = ref(false);

// Calendar state
const currentDate = ref(new Date());
const selectedDateObj = ref(new Date());

// Calendar computed properties
const currentMonth = computed(() => currentDate.value.getMonth());
const currentYear = computed(() => currentDate.value.getFullYear());
const monthNames = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

const daysInMonth = computed(() => {
  return new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
  return new Date(currentYear.value, currentMonth.value, 1).getDay();
});

const calendarDays = computed(() => {
  const days = [];
  const today = new Date();

  // Empty cells for days before month starts
  for (let i = 0; i < firstDayOfMonth.value; i++) {
    days.push(null);
  }

  // Days of the month
  for (let day = 1; day <= daysInMonth.value; day++) {
    const date = new Date(currentYear.value, currentMonth.value, day);
    const isToday = date.toDateString() === today.toDateString();
    const isSelected =
      consultationDate.value === date.toISOString().split("T")[0];
    const isPast = date < today && !isToday;

    days.push({
      day,
      date,
      isToday,
      isSelected,
      isPast,
      formatted: date.toISOString().split("T")[0],
    });
  }

  return days;
});

onMounted(async () => {
  waitingAppointments.value =
    await appointmentStore.getAppointmentsByStatus("waiting");
  console.log("unscheduled Consultations Data:", waitingAppointments.value);
});

const openModal = (appointment) => {
  selectedAppointment.value = appointment;
  // Set default date to today and time to current time
  const now = new Date();
  consultationDate.value = now.toISOString().split("T")[0];
  selectedDateObj.value = now;
  const hours = now.getHours().toString().padStart(2, "0");
  const minutes = now.getMinutes().toString().padStart(2, "0");
  consultationTime.value = `${hours}:${minutes}`;
  isOpen.value = true;
};

const closeModal = () => {
  isOpen.value = false;
  selectedAppointment.value = null;
  consultationDate.value = "";
  consultationTime.value = "";
  showCalendar.value = false;
};

const toggleCalendar = () => {
  showCalendar.value = !showCalendar.value;
};

const selectDate = (dayObj) => {
  if (dayObj && !dayObj.isPast) {
    consultationDate.value = dayObj.formatted;
    selectedDateObj.value = dayObj.date;
    showCalendar.value = false;
  }
};

const previousMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
};

const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
};

const formatDisplayDate = (dateStr) => {
  if (!dateStr) return "Select date";
  const date = new Date(dateStr);
  return date.toLocaleDateString("en-US", {
    weekday: "short",
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const handleCreateConsultation = async () => {
  if (
    !consultationDate.value ||
    !consultationTime.value ||
    !selectedAppointment.value
  ) {
    toast.error("Please fill in all required fields: Date and Time");
    return;
  }

  isLoading.value = true;

  try {
    // 1. Create consultation
    const consultationResponse = await fetch("/api/consultations", {
      method: "POST",
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        appointment_id: selectedAppointment.value.id,
        patient_id: selectedAppointment.value.patient_id,
        doctor_id: selectedAppointment.value.doctor_id,
        consultation_date: `${consultationDate.value}T${consultationTime.value}:00`,
        notes: "",
      }),
    });

    if (!consultationResponse.ok) {
      throw new Error("Failed to create consultation");
    }

    // 2. Update appointment status from "waiting" to "pending" with new date and time
    const updateResponse = await appointmentStore.updateAppointment(
      selectedAppointment.value.id,
      {
        appointment_date: consultationDate.value,
        time: `${consultationTime.value}:00`,
        status: "pending",
      },
    );

    if (updateResponse.success) {
      toast.success("Consultation scheduled successfully!  .");
      closeModal();
      // Refresh the waiting appointments list
      waitingAppointments.value =
        await appointmentStore.getAppointmentsByStatus("waiting");
    } else {
      throw new Error("Failed to update appointment");
    }
  } catch (error) {
    console.error("Error creating consultation:", error);
    toast.error("Failed to schedule consultation. Please try again.");
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <DoctorLayout>
    <div
      class="bg- -400 mx-auto mt-16 min-h-fit max-w-[1600px] overflow-hidden rounded-xl border border-gray-200 bg-white pb-16 shadow-sm"
    >
      <div class="border-b border-gray-200 p-4">
        <h3 class="my-4 mb-2 py-8 text-2xl font-semibold">
          Appointments Waiting for Confirmation
        </h3>
      </div>

      <div class="custom-scrollbar max-w-full overflow-x-auto">
        <table class="min-w-full text-center">
          <thead>
            <tr class="ark:border-gray-70 0 border-b border-gray-200">
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Patient Name
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Patient Email
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Patient Phone
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Issued Date
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 text-center sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">Action</p>
              </th>
            </tr>
          </thead>
          <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
            <!-- No results message -->
            <tr
              v-if="
                !waitingAppointments.data ||
                waitingAppointments.data.length === 0
              "
            >
              <td colspan="5" class="px-5 py-8 text-center sm:px-6">
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
                    No Waiting Appointments found
                  </p>
                </div>
              </td>
            </tr>
            <tr
              v-else
              v-for="(appointment, index) in waitingAppointments.data"
              :key="index"
              class="border-t border-gray-100"
            >
              <td class="px-5 py-4 sm:px-6">
                <div class="gap-3">
                  <span class="block text-theme-sm font-medium text-gray-800">
                    {{ appointment.patient?.name }}
                  </span>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-theme-sm text-gray-500">
                  {{ appointment.patient?.email }}
                </p>
              </td>
              <td class="px-5 py-4 text-center sm:px-6">
                <div class=" ">
                  {{ appointment.patient?.phone }}
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                {{ appointment.appointment_date.split("T")[0] }}
              </td>
              <td class="px-5 py-4 sm:px-6">
                <button
                  @click="openModal(appointment)"
                  class="ark:bg-gray-800 ark:text-white cursor-pointer rounded-md bg-first-accent px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                  Reserve Consultation
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <Modal v-if="isOpen" @close="closeModal">
        <template #body>
          <div
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 lg:p-11"
          >
            <h5
              class="modal-title mb-2 text-xl font-semibold text-gray-800 lg:text-2xl"
            >
              Schedule Consultation
            </h5>
            <p class="text-sm text-gray-500">
              Schedule consultation for {{ selectedAppointment?.patient?.name }}
            </p>

            <div class="mt-8">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Patient
                </label>
                <input
                  :value="selectedAppointment?.patient?.name"
                  type="text"
                  disabled
                  class="h-11 w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-800 shadow-sm"
                />
              </div>

              <!-- Date Picker with Mini Calendar -->
              <div class="relative mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Consultation Date *
                </label>
                <div class="relative">
                  <input
                    :value="formatDisplayDate(consultationDate)"
                    @click="toggleCalendar"
                    type="text"
                    readonly
                    class="focus:ring-3 h-11 w-full cursor-pointer appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10"
                    placeholder="Select date"
                  />
                  <div
                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                  >
                    <svg
                      class="h-5 w-5 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      ></path>
                    </svg>
                  </div>
                </div>

                <!-- Mini Calendar -->
                <div
                  v-if="showCalendar"
                  class="absolute z-50 mt-1 w-80 rounded-lg border border-gray-200 bg-white p-4 shadow-lg"
                >
                  <!-- Calendar Header -->
                  <div class="mb-4 flex items-center justify-between">
                    <button
                      @click="previousMonth"
                      class="rounded p-1 hover:bg-gray-100"
                    >
                      <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 19l-7-7 7-7"
                        ></path>
                      </svg>
                    </button>
                    <h3 class="text-lg font-semibold">
                      {{ monthNames[currentMonth] }} {{ currentYear }}
                    </h3>
                    <button
                      @click="nextMonth"
                      class="rounded p-1 hover:bg-gray-100"
                    >
                      <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        ></path>
                      </svg>
                    </button>
                  </div>

                  <!-- Days of Week -->
                  <div class="mb-2 grid grid-cols-7 gap-1">
                    <div
                      v-for="day in [
                        'Sun',
                        'Mon',
                        'Tue',
                        'Wed',
                        'Thu',
                        'Fri',
                        'Sat',
                      ]"
                      :key="day"
                      class="p-2 text-center text-xs font-medium text-gray-500"
                    >
                      {{ day }}
                    </div>
                  </div>

                  <!-- Calendar Days -->
                  <div class="grid grid-cols-7 gap-1">
                    <button
                      v-for="(dayObj, index) in calendarDays"
                      :key="index"
                      @click="selectDate(dayObj)"
                      :disabled="!dayObj || dayObj.isPast"
                      :class="{
                        'bg-brand-500 text-white': dayObj?.isSelected,
                        'bg-blue-100 text-blue-600':
                          dayObj?.isToday && !dayObj?.isSelected,
                        'cursor-not-allowed text-gray-400': dayObj?.isPast,
                        'hover:bg-gray-100':
                          dayObj && !dayObj.isPast && !dayObj.isSelected,
                        invisible: !dayObj,
                      }"
                      class="h-8 w-8 rounded text-sm font-medium transition-colors"
                    >
                      {{ dayObj?.day }}
                    </button>
                  </div>

                  <!-- Today Button -->
                  <div class="mt-4 border-t pt-3">
                    <button
                      @click="
                        selectDate({
                          date: new Date(),
                          formatted: new Date().toISOString().split('T')[0],
                          isPast: false,
                        })
                      "
                      class="w-full rounded bg-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-gray-200"
                    >
                      Today
                    </button>
                  </div>
                </div>
              </div>

              <div class="mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Consultation Time *
                </label>
                <input
                  v-model="consultationTime"
                  type="time"
                  class="focus:ring-3 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10"
                  placeholder="Select a time"
                />
              </div>
            </div>

            <div
              class="modal-footer mt-6 flex items-center gap-3 sm:justify-end"
            >
              <button
                @click="closeModal"
                :disabled="isLoading"
                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
              >
                Cancel
              </button>

              <button
                @click="handleCreateConsultation"
                :disabled="isLoading || !consultationDate || !consultationTime"
                class="btn btn-success btn-update-event flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:cursor-not-allowed disabled:bg-gray-400 sm:w-auto"
              >
                {{ isLoading ? "Scheduling..." : "Schedule Consultation" }}
              </button>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </DoctorLayout>
</template>

<style scoped>
/* Add click outside functionality */
.calendar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 40;
}
</style>
