<template>
  <DoctorLayout>
    <div class="min-h-screen bg-gray-100 p-6">
      <!-- Header -->
      <header class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">
          Consultation with {{ consultationStore.patient?.name || "Patient" }}
        </h2>
        <button
          @click="router.push('/doctor/appointments')"
          class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        >
          Back to Appointments
        </button>
      </header>

      <!-- Loading State -->
      <div v-if="consultationStore.isLoading" class="text-center">
        <div
          class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"
        ></div>
        <p class="mt-2 text-gray-600">Loading consultation details...</p>
      </div>

      <!-- Error State -->
      <div
        v-else-if="consultationStore.errorMessage"
        class="rounded-lg bg-red-100 p-4 text-red-700"
      >
        {{ consultationStore.errorMessage }}
      </div>

      <!-- Main Content -->
      <div v-else class="grid gap-6 lg:grid-cols-3">
        <!-- Patient Information -->
        <div class="lg:col-span-1">
          <div class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">
              Patient Information
            </h3>
            <div class="space-y-3">
              <p>
                <strong>Name:</strong>
                {{ consultationStore.patient?.name || "N/A" }}
              </p>
              <p>
                <strong>Email:</strong>
                {{ consultationStore.patient?.email || "N/A" }}
              </p>
              <p>
                <strong>Phone:</strong>
                {{ consultationStore.patient?.phone || "N/A" }}
              </p>
              <p>
                <strong>Age:</strong>
                {{ consultationStore.patient?.age || "N/A" }}
              </p>
              <p>
                <strong>Gender:</strong>
                {{ consultationStore.patient?.gender || "N/A" }}
              </p>
              <p>
                <strong>Medical History:</strong>
                {{
                  consultationStore.patient?.medical_history ||
                  "No medical history provided"
                }}
              </p>
            </div>
          </div>
        </div>

        <!-- Consultation Details & Chat -->
        <div class="space-y-6 lg:col-span-2">
          <!-- Consultation Notes -->
          <div class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">
              Consultation Notes
            </h3>
            <textarea
              v-model="consultationNotes"
              placeholder="Enter consultation notes..."
              class="h-32 w-full rounded-lg border border-gray-300 p-3 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
              :disabled="consultationStore.isSavingNotes"
            ></textarea>
            <div class="mt-4 flex justify-end gap-3">
              <button
                @click="consultationStore.saveNotes(consultationNotes)"
                :disabled="consultationStore.isSavingNotes"
                class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:bg-gray-400"
              >
                {{
                  consultationStore.isSavingNotes ? "Saving..." : "Save Notes"
                }}
              </button>
            </div>
          </div>

          <!-- Chat Interface -->
          <div class="rounded-lg border bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">
              Chat with Patient
            </h3>
            <div
              ref="chatContainer"
              class="mb-4 h-80 overflow-y-auto rounded-lg border bg-gray-50 p-4"
            >
              <div
                v-if="consultationStore.chatMessages.length === 0"
                class="text-center text-gray-500"
              >
                No messages yet. Start the conversation!
              </div>
              <div
                v-for="(msg, index) in consultationStore.chatMessages"
                :key="index"
                :class="[
                  'mb-3 flex',
                  msg.sender === 'Doctor' ? 'justify-end' : 'justify-start',
                ]"
              >
                <div
                  :class="[
                    'max-w-xs rounded-lg p-3 text-sm',
                    msg.sender === 'Doctor'
                      ? 'bg-brand-500 text-white'
                      : 'bg-gray-200 text-gray-800',
                  ]"
                >
                  <p class="font-medium">{{ msg.sender }}</p>
                  <p>{{ msg.text }}</p>
                  <p class="mt-1 text-xs opacity-75">
                    {{ formatDate(msg.timestamp) }}
                  </p>
                </div>
              </div>
            </div>
            <div class="flex gap-2">
              <input
                v-model="newMessage"
                type="text"
                placeholder="Type a message..."
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                @keyup.enter="sendMessage"
                :disabled="consultationStore.isSendingMessage"
              />
              <button
                @click="sendMessage"
                :disabled="consultationStore.isSendingMessage"
                class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:bg-gray-400"
              >
                {{ consultationStore.isSendingMessage ? "Sending..." : "Send" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useConsultationStore } from "@/stores/consultationStore";
import DoctorLayout from "@/layout/DoctorLayout.vue";

const route = useRoute();
const router = useRouter();
const consultationStore = useConsultationStore();

const appointmentId = route.params.appointmentId;
const consultationNotes = ref("");
const newMessage = ref("");
const chatContainer = ref(null);

// Format timestamp
const formatDate = (timestamp) => {
  return new Date(timestamp).toLocaleString("en-US", {
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  });
};

// Send chat message
const sendMessage = async () => {
  if (!newMessage.value.trim()) return;
  await consultationStore.sendMessage(newMessage.value);
  newMessage.value = "";
  await nextTick(() => {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  });
};

// Poll for new messages
let pollInterval;
onMounted(async () => {
  await consultationStore.fetchConsultationData(appointmentId);
  consultationNotes.value = consultationStore.consultation?.notes || "";
  pollInterval = setInterval(() => consultationStore.fetchChatMessages(), 5000);
});

onUnmounted(() => {
  clearInterval(pollInterval);
  consultationStore.clearData();
});
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
