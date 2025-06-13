<template>
  <DoctorLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="flex p-4 border-b border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-900">
          Consultation with {{ consultationStore.patient?.name || "Patient" }}
        </h2>
        <button
          @click="router.push('/doctor/appointments')"
          class="ml-auto rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        >
          Back to Appointments
        </button>
      </div>

      <div class="flex h-[calc(100vh-140px)]">
        <!-- Left Sidebar - Patient Information -->
        <div class="flex w-80 flex-col border-r border-gray-200 bg-white">
          <div class="h-full overflow-y-auto">
            <!-- Sidebar Header -->
            <div class="border-b border-gray-200 p-4">
              <h2 class="text-lg font-semibold text-gray-900">
                Patient Information
              </h2>
            </div>

            <!-- Patient Info -->
            <div class="border-b border-gray-200 p-4">
              <div class="mb-4 flex items-center space-x-3">
                <div
                  class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600"
                >
                  <img
                    v-if="consultationStore.patient?.profile_picture"
                    :src="consultationStore.patient?.profile_picture"
                    alt=""
                    class="h-full w-full rounded-full object-cover"
                  />
                  <svg
                    v-else
                    class="h-6 w-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">
                    {{ consultationStore.patient?.name || "Patient" }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ consultationStore.patient?.age || "N/A" }} years old •
                    {{ consultationStore.patient?.gender || "N/A" }}
                  </p>
                </div>
              </div>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">Email:</span>
                  <span class="font-medium">{{ consultationStore.patient?.email || "N/A" }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Phone:</span>
                  <span class="font-medium">{{ consultationStore.patient?.phone || "N/A" }}</span>
                </div>
              </div>
            </div>

            <!-- Patient Vitals -->
                  <!-- Previous Consultations -->
            <div class="border-b border-gray-200 p-4">
              <h3 class="mb-3 font-semibold text-gray-900">Previous Consultations</h3>
              <div class="space-y-3">                <!-- Loading State -->
                <div v-if="consultationStore.isLoadingConsultations" class="text-center py-4">
                  <div class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"></div>
                  <p class="text-sm text-gray-600 mt-2">Loading consultations...</p>
                </div>
                
                <!-- No consultations -->
                <div v-else-if="consultationStore.patientConsultations.length === 0" class="text-sm text-gray-600">
                  No previous consultations found
                </div>
                
                <!-- Consultations list -->
                <div
                  v-else
                  v-for="consultation in consultationStore.patientConsultations"
                  :key="consultation.id"
                  @click="openConsultationModal(consultation)"
                  class="cursor-pointer rounded-lg border border-gray-200 bg-gray-50 p-3 transition-colors hover:bg-gray-100 hover:border-gray-300"
                >
                  <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-medium text-blue-600">{{ consultation.specialty }}</div>
                    <div class="text-xs text-gray-500">{{ consultation.date }}</div>
                  </div>
                  <div class="text-sm font-medium text-gray-900 mb-1">{{ consultation.type }}</div>
                  <div class="text-xs text-gray-600 mb-2">{{ consultation.doctorName }}</div>
                  <div class="text-xs text-gray-600 line-clamp-2">
                    {{ consultation.notes.substring(0, 100) }}{{ consultation.notes.length > 100 ? '...' : '' }}
                  </div>
                  <div class="mt-2 flex items-center text-xs text-blue-500">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Click to view details
                  </div>
                </div>
              </div>
            </div>

            <!-- Allergies -->
            
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1">
          <!-- Loading State -->
          <div v-if="consultationStore.isLoading" class="flex h-full items-center justify-center">
            <div class="text-center">
              <div
                class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"
              ></div>
              <p class="mt-2 text-gray-600">Loading consultation details...</p>
            </div>
          </div>

          <!-- Error State -->
          <div
            v-else-if="consultationStore.errorMessage"
            class="flex h-full items-center justify-center p-6"
          >
            <div class="rounded-lg bg-red-100 p-4 text-red-700">
              {{ consultationStore.errorMessage }}
            </div>
          </div>

          <!-- Main Content -->
          <div v-else class="flex h-full flex-col">
            <!-- Video Call Section -->
            <div class="relative flex-1 bg-gray-50 p-4">
              <div class="flex h-full flex-col space-y-4">
                <!-- Video Call Card -->
                <div class="flex-1 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                  <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    Video Consultation
                  </h3>
                  
                  <!-- Pre-Call State -->
                  <div v-if="!isCallActive" class="flex h-[calc(100%-4rem)] flex-col items-center justify-center">
                    <div class="mb-6 py-6 text-center">
                      <div class="mx-auto mb-4 h-24 w-24 rounded-full bg-blue-100 p-6">
                        <svg class="h-full w-full text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <h3 class="text-lg font-medium text-gray-900">Ready to start the consultation?</h3>
                      <p class="mt-1 text-gray-500">Click the button below to initiate the video call with your patient</p>
                      <button
                        @click="startCall"
                        class="mt-6 rounded-lg bg-second-accent px-6 py-3 text-center font-medium text-white hover:bg-second-accent/90 focus:outline-none focus:ring-2 focus:ring-second-accent focus:ring-offset-2"
                      >
                        Start Video Call
                      </button>
                    </div>
                  </div>
                  
                  <!-- Active Call State -->
                  <div v-else class="relative aspect-video h-[calc(100%-4rem)] overflow-hidden rounded-lg bg-gray-900">
                    <!-- Video Content -->
                    <div class="flex h-full w-full items-center justify-center">
                      <div class="text-center">
                        <div class="mx-auto mb-4 h-24 w-24 rounded-full bg-blue-100 p-6">
                          <svg class="h-full w-full text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                          </svg>
                        </div>
                        <h3 class="text-lg font-medium text-white">Video call with Patient</h3>
                        <p class="text-gray-300">
                          The video consultation will appear here
                        </p>
                      </div>

                      <!-- Patient Video (Inset) -->
                      <div class="absolute right-4 top-4 h-36 w-48 overflow-hidden rounded-lg border-2 border-gray-600 bg-gray-700">
                        <div class="flex h-full w-full items-center justify-center">
                          <svg class="h-12 w-12 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                        </div>
                      </div>

                      <!-- Video Controls -->
                      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 transform">
                        <div class="flex items-center space-x-4 rounded-full bg-white bg-opacity-10 px-6 py-3 backdrop-blur-sm">
                          <button
                            @click="isMuted = !isMuted"
                            :class="`rounded-full p-3 transition-colors ${
                              isMuted ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-900'
                            }`"
                          >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path v-if="isMuted" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M5.586 15.414a8 8 0 1011.314 0M17 20h3m-3-3h3M3 3l18 18" />
                              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0-11V3" />
                            </svg>
                          </button>

                          <button
                            @click="isVideoOn = !isVideoOn"
                            :class="`rounded-full p-3 transition-colors ${
                              !isVideoOn ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-900'
                            }`"
                          >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path v-if="!isVideoOn" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M7.5 3.186c-.259.032-.516.07-.77.114M4.5 3.75v16.5a2.25 2.25 0 002.25 2.25h6.055c.749 0 1.471-.29 2.01-.804l4.5-4.5a2.25 2.25 0 00.659-1.591V7.5a2.25 2.25 0 00-2.25-2.25h-2.25l-2.1-2.1a2.25 2.25 0 00-1.591-.659h-4.5a2.25 2.25 0 00-2.25 2.25v.5" />
                              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                          </button>

                          <button
                            @click="endCall"
                            class="rounded-full bg-red-600 p-3 transition-colors hover:bg-red-700"
                          >
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Consultation Notes -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                  <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    Consultation Notes
                  </h3>                  <textarea
                    v-model="consultationNotes"
                    placeholder="Enter consultation notes..."
                    class="h-32 w-full rounded-lg border border-gray-300 p-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors"
                    :disabled="consultationStore.isSavingNotes"
                    @keydown.ctrl.s.prevent="saveConsultationNotes"
                    @keydown.meta.s.prevent="saveConsultationNotes"
                  ></textarea>
                  <div class="mt-2 text-xs text-gray-500">
                    Tip: Use Ctrl+S (or Cmd+S on Mac) to save notes quickly
                  </div><div class="mt-4 flex justify-end gap-3">                    <button
                      @click="requestLabTest"
                      class="rounded-lg bg-first-accent px-4 py-2 text-sm font-medium text-white hover:bg-first-accent/90 focus:outline-none focus:ring-2 focus:ring-first-accent focus:ring-offset-2"
                    >
                      <svg class="mr-2 h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                      </svg>
                      Request Lab Test
                    </button>                    <button
                      @click="saveConsultationNotes"
                      :disabled="consultationStore.isSavingNotes"
                      class="rounded-lg bg-second-accent px-4 py-2 text-sm font-medium text-white hover:bg-second-accent/90 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                    >
                      {{
                        consultationStore.isSavingNotes ? "Saving..." : "Save Notes"
                      }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat Interface -->
            <div class="h-64 border-t border-gray-200 bg-white">
              <div class="flex h-full flex-col">
                <div class="border-b border-gray-200 px-4 py-3">
                  <h3 class="text-md font-semibold text-gray-800">
                    Chat with Patient
                  </h3>
                </div>
                <div
                  ref="chatContainer"
                  class="flex-1 overflow-y-auto p-4"
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
                    :class="
                      msg.sender === 'Doctor'
                        ? 'mb-3 flex justify-end'
                        : 'mb-3 flex justify-start'
                    "
                  >
                    <div                      :class="
                        msg.sender === 'Doctor'
                          ? 'max-w-xs rounded-lg bg-second-accent p-3 text-sm text-white'
                          : 'max-w-xs rounded-lg bg-gray-200 p-3 text-sm text-gray-800'
                      "
                    >
                      <p class="font-medium">{{ msg.sender }}</p>
                      <p>{{ msg.text }}</p>
                      <p class="mt-1 text-xs opacity-75">
                        {{ formatDate(msg.timestamp) }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="border-t border-gray-200 p-4">
                  <div class="flex gap-2">
                    <input
                      v-model="newMessage"
                      type="text"
                      placeholder="Type a message..."
                      class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                      @keyup.enter="sendMessage"
                      :disabled="consultationStore.isSendingMessage"
                    />                    <button
                      @click="sendMessage"
                      :disabled="consultationStore.isSendingMessage"
                      class="rounded-lg bg-second-accent px-4 py-2 text-sm font-medium text-white hover:bg-second-accent/90 disabled:bg-gray-400"
                    >
                      {{ consultationStore.isSendingMessage ? "Sending..." : "Send" }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>        </div>
      </div>
    </div>

    <!-- Consultation Details Modal -->
    <div
      v-if="showConsultationModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click="closeConsultationModal"
    >
      <div
        class="relative max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Consultation Details</h2>
            <p class="text-sm text-gray-600">{{ selectedConsultation?.doctorName }} • {{ selectedConsultation?.specialty }}</p>
          </div>
          <button
            @click="closeConsultationModal"
            class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
              <!-- Consultation Info -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Consultation Information</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ selectedConsultation?.date }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-medium">{{ selectedConsultation?.type }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Doctor:</span>
                    <span class="font-medium">{{ selectedConsultation?.doctorName }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Specialty:</span>
                    <span class="font-medium">{{ selectedConsultation?.specialty }}</span>
                  </div>
                </div>
              </div>

              <!-- Diagnosis -->
              <div class="bg-green-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Diagnosis</h3>
                <p class="text-gray-700">{{ selectedConsultation?.diagnosis }}</p>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
              <!-- Notes -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Consultation Notes</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-gray-700 leading-relaxed">{{ selectedConsultation?.notes }}</p>
                </div>
              </div>

              <!-- Prescriptions -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Prescriptions</h3>
                <div class="space-y-2">
                  <div
                    v-for="(prescription, index) in selectedConsultation?.prescriptions"
                    :key="index"
                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-3"
                  >
                    <div class="flex items-center">
                      <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                      </svg>
                      <span class="text-gray-700">{{ prescription }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end">
          <button
            @click="closeConsultationModal"
            class="px-4 py-2 bg-second-accent text-white rounded-lg hover:bg-second-accent/90 focus:outline-none focus:ring-2 focus:ring-second-accent focus:ring-offset-2"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, nextTick, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useConsultationStore } from "@/stores/consultationStore";
import { useToast } from "vue-toastification";
import DoctorLayout from "@/layout/DoctorLayout.vue";

const route = useRoute();
const router = useRouter();
const consultationStore = useConsultationStore();
const toast = useToast();

const appointmentId = route.params.appointmentId;
const consultationNotes = ref("");
const newMessage = ref("");
const chatContainer = ref(null);

// Additional UI state
const isMuted = ref(false);
const isVideoOn = ref(true);
const isCallActive = ref(false);
const showConsultationModal = ref(false);
const selectedConsultation = ref(null);// Added for call state management

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

// Call management functions
const startCall = () => {
  isCallActive.value = true;
  console.log("Starting call with patient");
};

const endCall = () => {
  isCallActive.value = false;
  console.log("Ending call with patient");
};

// Lab request function
const requestLabTest = () => {
  // Here you can add logic to actually send the lab request to your backend
  // For now, we'll just show a success toast
  toast.success("Lab test request has been sent successfully!", {
    position: "top-right",
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false
  });
  
  console.log("Lab test requested for patient:", consultationStore.patient?.name);
};

// Save consultation notes function
const saveConsultationNotes = async () => {
  if (!consultationNotes.value.trim()) {
    toast.warning("Please enter some notes before saving.", {
      position: "top-right",
      timeout: 3000
    });
    return;
  }

  // Check if consultation exists
  if (!consultationStore.consultation?.id) {
    toast.error("No consultation found. Please refresh the page and try again.", {
      position: "top-right",
      timeout: 5000
    });
    return;
  }

  try {
    console.log('Attempting to save notes for consultation:', consultationStore.consultation.id);
    const result = await consultationStore.saveNotes(consultationNotes.value);
    
    if (result.success) {
      lastSavedNotes.value = consultationNotes.value;
      toast.success("Consultation notes saved successfully!", {
        position: "top-right",
        timeout: 3000,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
        closeButton: "button",
        icon: true,
        rtl: false
      });
      console.log("Notes saved successfully for consultation:", consultationStore.consultation?.id);
    } else {
      toast.error(`Failed to save notes: ${result.error}`, {
        position: "top-right",
        timeout: 5000
      });
      console.error('Save failed:', result.error);
    }
  } catch (error) {
    console.error("Error saving consultation notes:", error);
    toast.error("An unexpected error occurred while saving notes.", {
      position: "top-right",
      timeout: 5000
    });
  }
};

// Auto-save functionality
const lastSavedNotes = ref("");
const autoSaveTimer = ref(null);

const triggerAutoSave = () => {
  // Clear existing timer
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value);
  }
  
  // Set new timer for auto-save after 10 seconds of inactivity
  autoSaveTimer.value = setTimeout(async () => {
    // Check if consultation exists and notes have changed
    if (!consultationStore.consultation?.id) {
      console.log('Auto-save skipped: No consultation found');
      return;
    }
    
    if (consultationNotes.value !== lastSavedNotes.value && consultationNotes.value.trim()) {
      console.log("Auto-saving notes...");
      const result = await consultationStore.saveNotes(consultationNotes.value);
      if (result.success) {
        lastSavedNotes.value = consultationNotes.value;
        toast.info("Notes auto-saved", {
          position: "bottom-right",
          timeout: 2000,
          hideProgressBar: true
        });
      } else {
        console.error('Auto-save failed:', result.error);
      }
    }
  }, 10000); // 10 seconds delay
};

// Watch for changes in consultation notes to trigger auto-save
watch(consultationNotes, () => {
  triggerAutoSave();
});

// Modal functions
const openConsultationModal = (consultation) => {
  selectedConsultation.value = consultation;
  showConsultationModal.value = true;
};

const closeConsultationModal = () => {
  showConsultationModal.value = false;
  selectedConsultation.value = null;
};

// Vitals data (placeholder)
const vitals = ref([
  {
    label: "Blood Pressure",
    value: "120/80",
    unit: "mmHg",
    status: "normal",
    icon: "heart",
  },
  {
    label: "Heart Rate",
    value: "72",
    unit: "bpm",
    status: "normal",
    icon: "activity",
  },
  {
    label: "Temperature",
    value: "98.6",
    unit: "°F",
    status: "normal",
    icon: "thermometer",
  },
  {
    label: "Oxygen Saturation",
    value: "98",
    unit: "%",
    status: "normal",
    icon: "activity",
  },
]);

// Previous consultations data - now comes from store
// const previousConsultations = ref([]);
// const isLoadingConsultations = ref(false);

// Fetch patient consultations - now handled by store
// const fetchPatientConsultations = async (patientId) => { ... }

// Load sample consultation data - now handled by store
// const loadSampleConsultationData = () => { ... }

const getVitalStatusColor = (status) => {
  switch (status) {
    case "high":
      return "text-red-600";
    case "low":
      return "text-yellow-600";
    default:
      return "text-green-600";
  }
};

const getVitalIcon = (iconType) => {
  const icons = {
    heart: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>`,
    activity: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/>`,
    thermometer: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/>`,
  };
  return icons[iconType] || icons.heart;
};

// Poll for new messages
let pollInterval;
onMounted(async () => {
  try {
    await consultationStore.fetchConsultationData(appointmentId);
    
    // Load existing notes if available
    consultationNotes.value = consultationStore.consultation?.notes || "";
    // Initialize lastSavedNotes with current notes
    lastSavedNotes.value = consultationNotes.value;
    
    console.log('Consultation loaded:', consultationStore.consultation);
    console.log('Initial notes:', consultationNotes.value);
    
    // Show success message if consultation was created
    if (consultationStore.consultation) {
      if (!consultationNotes.value) {
        toast.info('New consultation created. You can now start adding notes.', {
          position: "top-right",
          timeout: 4000
        });
      }
    }
    
    console.log('Patient data:', consultationStore.patient);
    
    // Fetch patient consultation history using store
    if (consultationStore.patient?.id) {
      console.log('Fetching consultations for patient ID:', consultationStore.patient.id);
      const result = await consultationStore.fetchPatientConsultations(consultationStore.patient.id);
      
      if (!result.success) {
        if (result.usedFallback) {
          toast.info('Using sample data due to API issues');
        } else {
          toast.error('Failed to load consultation history');
        }
      }
    } else {
      console.warn('No patient ID found, loading sample consultation data for demo');
      // Load sample data for demonstration
      consultationStore.loadSampleConsultationData();
    }
    
    pollInterval = setInterval(() => consultationStore.fetchChatMessages(), 5000);
  } catch (error) {
    console.error('Error during component initialization:', error);
    toast.error('Failed to initialize consultation. Please refresh the page.', {
      position: "top-right",
      timeout: 5000
    });
  }
});

onUnmounted(() => {
  clearInterval(pollInterval);
  // Clear auto-save timer
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value);
  }
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

/* Additional styling for better UI experience */
/* Message transition effects */
.message-enter-active, .message-leave-active {
  transition: all 0.3s ease;
}
.message-enter-from, .message-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Video controls hover effect */
button {
  transition: all 0.2s ease;
}
button:hover {
  transform: translateY(-1px);
}
button:active {
  transform: translateY(1px);
}

/* Custom scrollbar for chat */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #aaa;
}
</style>
