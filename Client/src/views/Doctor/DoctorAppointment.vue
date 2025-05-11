<template>
  <DoctorLayout>
    <div>
      <header
        class="flex items-center justify-between bg-white px-6 py-8 shadow-md"
      >
        <h1 class="text-xl font-bold text-gray-800">Appointments Calendar</h1>
      </header>
      <section class="mt-6">
        <div
          v-if="appointmentStore.isLoading"
          class="text-center text-gray-500"
        >
          Loading...
        </div>
        <div
          v-else-if="appointmentStore.errors.message"
          class="text-center text-red-500"
        >
          {{ appointmentStore.errors.message }}
        </div>
        <div v-else class="rounded-2xl border border-gray-200 bg-white">
          <div class="custom-calendar">
            <FullCalendar
              ref="calendarRef"
              class="min-h-screen"
              :options="calendarOptions"
            />
          </div>
        </div>
      </section>

      <!-- Modal -->
      <Modal v-if="isOpen" @close="closeModal">
        <template #body>
          <div
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 lg:p-11"
          >
            <h5
              class="modal-title mb-2 text-xl font-semibold text-gray-800 lg:text-2xl"
            >
              {{ selectedEvent ? "Appointment Details" : "Add Appointment" }}
            </h5>
            <p class="text-sm text-gray-500">
              {{
                selectedEvent
                  ? "View appointment details"
                  : "Schedule a new appointment"
              }}
            </p>

            <div class="mt-8">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Patient
                </label>
                <select
                  v-model="selectedPatientId"
                  :disabled="!!selectedEvent"
                  class="focus:ring-3 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-brand-300 focus:ring-brand-500/10 disabled:cursor-not-allowed disabled:bg-gray-100"
                >
                  <option value="" disabled>Select a patient</option>
                  <option
                    v-for="patient in appointmentStore.patients"
                    :key="patient.id"
                    :value="patient.id"
                  >
                    {{ patient.name }}
                  </option>
                </select>
              </div>

              <div class="mt-6" v-if="selectedEvent">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Appointment Status
                </label>
                <input
                  :value="eventStatus"
                  type="text"
                  disabled
                  class="h-11 w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-800 shadow-sm"
                />
              </div>

              <div class="mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Appointment Date
                </label>
                <input
                  v-model="eventStartDate"
                  type="date"
                  :disabled="!!selectedEvent"
                  class="focus:ring-3 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10 disabled:cursor-not-allowed disabled:bg-gray-100"
                />
              </div>

              <div class="mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                  Appointment Time
                </label>
                <input
                  v-model="eventStartTime"
                  type="time"
                  :disabled="!!selectedEvent"
                  class="focus:ring-3 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10 disabled:cursor-not-allowed disabled:bg-gray-100"
                  placeholder="Select a time"
                />
              </div>
            </div>

            <div
              class="modal-footer mt-6 flex items-center gap-3 sm:justify-end"
            >
              <button
                @click="closeModal"
                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
              >
                Close
              </button>

              <button
                v-if="!selectedEvent"
                @click="handleAddOrUpdateEvent"
                class="btn btn-success btn-update-event flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
              >
                Add Appointment
              </button>

              <button
                v-if="selectedEvent"
                @click="goToConsultation"
                :disabled="!isConsultationTime"
                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:cursor-not-allowed disabled:bg-gray-400 sm:w-auto"
              >
                Go to Consultation
              </button>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import { useAppointmentStore } from "@/stores/appointmentStore";
import DoctorLayout from "@/layout/DoctorLayout.vue";
import Modal from "@/components/UI/Modal.vue";

const appointmentStore = useAppointmentStore();
const router = useRouter();
const calendarRef = ref(null);
const isOpen = ref(false);
const selectedEvent = ref(null);
const selectedPatientId = ref("");
const eventTitle = ref("");
const eventStartDate = ref("");
const eventStartTime = ref("");
const eventStatus = ref("pending");
const isConsultationTime = ref(false);
const currentView = ref("timeGridWeek");

onMounted(async () => {
  await appointmentStore.fetchAppointments();
  await appointmentStore.fetchPatientsWithAppointments();
  console.log("Appointments after fetch:", appointmentStore.appointments);
  console.log("Patients after fetch:", appointmentStore.patients);
});

const calendarEvents = computed(() => appointmentStore.appointments);

const openModal = () => {
  isOpen.value = true;
};

const closeModal = () => {
  isOpen.value = false;
  resetModalFields();
};

const resetModalFields = () => {
  selectedPatientId.value = "";
  eventTitle.value = "";
  eventStartDate.value = "";
  eventStartTime.value = "";
  eventStatus.value = "pending";
  selectedEvent.value = null;
  isConsultationTime.value = false;
};

const handleDateSelect = (selectInfo) => {
  resetModalFields();
  eventStartDate.value = selectInfo.startStr.split("T")[0];

  // Check the current calendar view
  if (calendarRef.value) {
    const calendarApi = calendarRef.value.getApi();
    currentView.value = calendarApi.view.type;

    // Extract the time from the clicked slot if available
    const clickedTime = new Date(selectInfo.startStr);
    const hours = clickedTime.getHours().toString().padStart(2, "0");
    const minutes = clickedTime.getMinutes().toString().padStart(2, "0");
    const timeString = `${hours}:${minutes}`;

    if (
      currentView.value === "timeGridDay" ||
      currentView.value === "timeGridWeek"
    ) {
      // In timeGridDay or timeGridWeek view, set the time to the clicked slot
      eventStartTime.value = timeString;
    } else {
      // In dayGridMonth view, leave the time field empty to prompt user selection
      eventStartTime.value = "";
    }
  }

  openModal();
};

const handleEventClick = (clickInfo) => {
  const event = clickInfo.event;
  selectedEvent.value = event;
  eventTitle.value = event.title;
  const startDateTime = new Date(event.start);
  eventStartDate.value = startDateTime.toISOString().split("T")[0];
  eventStartTime.value = startDateTime.toISOString().split("T")[1].slice(0, 5);
  eventStatus.value = event.extendedProps.description || "pending";

  // Set the selected patient ID based on the appointment
  const patient = appointmentStore.patients.find((p) => p.name === event.title);
  selectedPatientId.value = patient ? patient.id : "";

  checkConsultationTime();
  openModal();
};

const handleAddOrUpdateEvent = async () => {
  if (
    !selectedPatientId.value ||
    !eventStartDate.value ||
    !eventStartTime.value
  ) {
    alert("Please fill in all required fields: Patient, Date, and Time");
    return;
  }

  // Only add new appointments
  await appointmentStore.createAppointment({
    patient_id: selectedPatientId.value,
    doctor_id: "",
    date: eventStartDate.value,
    time: `${eventStartTime.value}:00`,
    status: "pending",
  });
  closeModal();
};

const checkConsultationTime = () => {
  if (!selectedEvent.value || !eventStartDate.value || !eventStartTime.value) {
    isConsultationTime.value = false;
    return;
  }

  const now = new Date();
  const appointmentDateTime = new Date(
    `${eventStartDate.value}T${eventStartTime.value}:00`,
  );

  const timeWindow = 5 * 60 * 1000; // 5 minutes
  const timeDiff = Math.abs(appointmentDateTime.getTime() - now.getTime());
  const isSameDate = now.toISOString().split("T")[0] === eventStartDate.value;

  isConsultationTime.value = isSameDate && timeDiff <= timeWindow;
};

const goToConsultation = () => {
  if (selectedEvent.value && isConsultationTime.value) {
    const appointmentId = selectedEvent.value.id;
    router.push(`/consultation/${appointmentId}`);
  }
};

const renderEventContent = (eventInfo) => {
  const colorClass = `fc-bg-${eventInfo.event.extendedProps.calendar?.toLowerCase() || "primary"}`;
  return {
    html: `
      <div class="event-fc-color flex fc-event-main ${colorClass} p-1 rounded-sm">
        <div class="fc-daygrid-event-dot"></div>
        <div class="fc-event-time">${eventInfo.timeText}</div>
        <div class="fc-event-title">${eventInfo.event.title}</div>
      </div>
    `,
  };
};

const calendarOptions = reactive({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: "timeGridWeek",
  headerToolbar: {
    left: "prev,next",
    center: "title",
    right: "dayGridMonth,timeGridWeek,timeGridDay",
  },
  events: calendarEvents,
  selectable: true,
  select: handleDateSelect,
  eventClick: handleEventClick,
  eventContent: renderEventContent,
  // customButtons: {
  //  addEventButton: {
  // text: "Add Appointment +",
  // click: openModal,
  // },
  // },
  slotMinTime: "08:00:00",
  slotMaxTime: "20:00:00",
});
</script>

<style scoped>
.fc-bg-danger {
  background-color: #f87171; /* Red for cancelled */
}
.fc-bg-success {
  background-color: #34d399; /* Green for confirmed */
}
.fc-bg-primary {
  background-color: #60a5fa; /* Blue for default */
}
.fc-bg-warning {
  background-color: #facc15; /* Yellow for pending */
}
</style>
