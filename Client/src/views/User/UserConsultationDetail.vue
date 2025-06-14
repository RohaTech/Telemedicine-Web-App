<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { useConsultationStore } from "@/stores/consultationStore";
import { ref, reactive, onMounted, onUnmounted, nextTick, watch } from "vue";
import { useRoute } from "vue-router";
import { useToast } from "vue-toastification";
import {
  TUICallKit,
  TUICallKitServer,
  TUICallType,
} from "@tencentcloud/call-uikit-vue";
import * as GenerateTestUserSig from "@/debug/GenerateTestUserSig-es"; 


const call = async () => {
  await TUICallKitServer.calls({
    userIDList: [calleeUserID.value],
    type: TUICallType.VIDEO_CALL,
  });
};

const SDKAppID = 40000719; // TODO: Replace with your SDKAppID (Notice: SDKAppID is of type number）
const SDKSecretKey =
  "17eb724f5184648ae6651277d82740750a9adb698cb23848a8c29939bbbaad82"; // TODO: Replace with your SDKSecretKey

const callerUserID = ref("2");
const calleeUserID = ref("");

//【2】Initialize the TUICallKit component
const init = async () => {
  const { userSig } = GenerateTestUserSig.genTestUserSig({
    userID: callerUserID.value,
    SDKAppID,
    SecretKey: SDKSecretKey,
  });
  await TUICallKitServer.init({
    userID: callerUserID.value,
    userSig,
    SDKAppID,
  });
  alert("TUICallKit init succeed");
};

// onMounted(() => {
//   callerUserID.value = "2";
//   init();
// });


const route = useRoute();
const toast = useToast();
const consultationStore = useConsultationStore();

// Reactive state
const isMuted = ref(false);
const isVideoOn = ref(true);
const message = ref("");
const newMessage = ref("");
const isCallActive = ref(false);
const consultation = ref([]);
const doctorLanguages = ref([]);
const isChatVisible = ref(true);
const chatContainer = ref(null);

// Poll for new messages
let pollInterval;

onMounted(async () => {
  try {
    // Get consultation data
    consultation.value = await consultationStore.getAConsultation(
      route.params.id,
    );

    // Parse languages and store in reactive variable
    const languagesString =
      consultation.value.data?.doctor.doctor?.languages || "[]";
      // callerUserID.value = JSON.stringify(consultation.value.data?.patient_id);

 
      
    try {
      doctorLanguages.value = JSON.parse(languagesString);
    } catch (error) {
      console.error("Error parsing languages:", error);
      doctorLanguages.value = [];
    }

    // Set consultation data for chat
    if (consultation.value.data) {
      consultationStore.consultation = consultation.value.data;

      // Load initial chat messages
      await consultationStore.fetchChatMessages();

      // Connect to real-time chat
      if (consultationStore.consultation?.id) {
        console.log(
          "Connecting to real-time chat for consultation:",
          consultationStore.consultation.id,
        );
        consultationStore.connectToChat(consultationStore.consultation.id);

        toast.info("Connecting to real-time chat...", {
          position: "bottom-right",
          timeout: 2000,
          hideProgressBar: true,
        });

        // Set up periodic polling as a fallback for real-time updates
        pollInterval = setInterval(async () => {
          try {
            const previousMessageCount = consultationStore.chatMessages.length;
            await consultationStore.fetchChatMessages();

            // If new messages were fetched, auto-scroll
            if (consultationStore.chatMessages.length > previousMessageCount) {
              await nextTick(() => {
                if (chatContainer.value) {
                  chatContainer.value.scrollTop =
                    chatContainer.value.scrollHeight;
                }
              });
            }
          } catch (error) {
            console.error("Error polling for messages:", error);
          }
        }, 5000); // Poll every 5 seconds
      }
    }
  } catch (error) {
    console.error("Error loading consultation:", error);
    toast.error("Failed to load consultation details.", {
      position: "top-right",
      timeout: 3000,
    });
  }
});

onUnmounted(() => {
  // Clear polling interval
  if (pollInterval) {
    clearInterval(pollInterval);
  }

  // Disconnect from real-time chat
  consultationStore.disconnectFromChat();
});

// Note: Static messages removed - now using consultationStore.chatMessages

// Patient data
const patientInfo = reactive({
  name: "John Doe",
  age: 45,
  gender: "Male",
  bloodType: "O+",
  mrn: "MRN-123456",
  phone: "(555) 123-4567",
  email: "john.doe@email.com",
});

// Appointment details
const appointmentInfo = reactive({
  doctor: "Dr. Sarah Smith",
  specialty: "Cardiologist",
  date: "Today, June 2, 2025",
  time: "10:30 AM - 11:00 AM",
  type: "Follow-up Consultation",
  reason: "Headache follow-up and blood pressure monitoring",
  location: "Virtual Visit",
});

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

const medicalHistory = ref([
  {
    date: "2024-01-15",
    type: "Consultation",
    description: "Annual physical examination - All vitals normal",
    doctor: "Dr. Sarah Smith",
  },
  {
    date: "2023-11-20",
    type: "Lab Results",
    description: "Blood work - Cholesterol levels slightly elevated",
    doctor: "Dr. Michael Johnson",
  },
  {
    date: "2023-09-10",
    type: "Consultation",
    description: "Follow-up for hypertension management",
    doctor: "Dr. Sarah Smith",
  },
]);

const medications = ref([
  {
    name: "Lisinopril",
    dosage: "10mg",
    frequency: "Once daily",
    prescriber: "Dr. Sarah Smith",
  },
  {
    name: "Metformin",
    dosage: "500mg",
    frequency: "Twice daily",
    prescriber: "Dr. Michael Johnson",
  },
  {
    name: "Vitamin D3",
    dosage: "1000 IU",
    frequency: "Once daily",
    prescriber: "Dr. Sarah Smith",
  },
]);

const allergies = ref([
  { allergen: "Penicillin", reaction: "Rash", severity: "Moderate" },
  { allergen: "Shellfish", reaction: "Swelling", severity: "Severe" },
]);

// Methods
const handleSendMessage = async () => {
  if (!newMessage.value.trim()) return;

  try {
    await consultationStore.sendMessage(newMessage.value, "Patient");
    newMessage.value = "";

    // Auto-scroll to bottom after sending
    await nextTick(() => {
      if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
      }
    });
  } catch (error) {
    console.error("Error sending message:", error);
    toast.error("Failed to send message. Please try again.", {
      position: "top-right",
      timeout: 3000,
    });
  }
};

const handleKeyPress = (e) => {
  if (e.key === "Enter") {
    handleSendMessage();
  }
};

// Format timestamp
const formatDate = (timestamp) => {
  return new Date(timestamp).toLocaleString("en-US", {
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  });
};

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

const handleJoinCall = () => {
  isCallActive.value = true;
};

const handleEndCall = () => {
  isCallActive.value = false;
};

const toggleChat = () => {
  isChatVisible.value = !isChatVisible.value;
};

// Watch for new chat messages to auto-scroll
watch(
  () => consultationStore.chatMessages,
  () => {
    nextTick(() => {
      if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
      }
    });
  },
  { deep: true },
);

// Watch for connection status changes
watch(
  () => consultationStore.isConnectedToChat,
  (newStatus) => {
    if (newStatus) {
      toast.success("Real-time chat connected", {
        position: "bottom-right",
        timeout: 2000,
        hideProgressBar: true,
      });
    } else {
      toast.warning("Chat connection lost, trying to reconnect...", {
        position: "bottom-right",
        timeout: 3000,
        hideProgressBar: true,
      });
    }
  },
);

const getVitalIcon = (iconType) => {
  const icons = {
    heart: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>`,
    activity: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/>`,
    thermometer: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/>`,
  };
  return icons[iconType] || icons.heart;
};
</script>

<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
 <TUICallKit style="width: 650px; height: 500px" />
      <div class="flex h-[calc(100vh-80px)]">
        <!-- Static Sidebar -->
        <div class="flex w-80 flex-col border-r border-gray-200 bg-white">
          <div class="h-full overflow-y-auto">
            <!-- Sidebar Header -->
            <div class="border-b border-gray-200 p-4">
              <h2 class="text-lg font-semibold text-gray-900">
                Doctor's Information
              </h2>
            </div>

            <!-- Doctor Info -->
            <div class="border-b border-gray-200 p-4">
              <div class="mb-4 flex items-center space-x-3">
                <div
                  class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600"
                >
                  <img
                    v-if="consultation.data?.doctor.profile_picture"
                    :src="consultation.data?.doctor.profile_picture"
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
                    {{ consultation.data?.doctor.name }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ consultation.data?.doctor.birth_date }} years old •
                    {{ consultation.data?.doctor.gender }}
                  </p>
                </div>
              </div>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">License Number:</span>
                  <span class="font-medium">{{
                    consultation.data?.doctor.doctor.medical_license_number
                  }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Languages:</span>
                  <div class="flex gap-x-2">
                    <span
                      v-for="(language, index) in doctorLanguages"
                      :key="index"
                      class="w-fit rounded-full bg-blue-600 px-2 py-1 text-xs text-white"
                      >{{ language }}</span
                    >
                  </div>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Phone:</span>
                  <span class="font-medium">{{
                    consultation.data?.doctor.phone
                  }}</span>
                </div>
              </div>
            </div>

            <!-- Recent History -->
            <!-- <div class="p-4">
              <h3 class="mb-3 flex items-center font-semibold text-gray-900">
                <svg
                  class="mr-2 h-4 w-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
                Recent History
              </h3>
              <div class="space-y-3">
                <div
                  v-for="(record, index) in medicalHistory.slice(0, 2)"
                  :key="index"
                  class="border-l-2 border-blue-200 pl-3"
                >
                  <div class="text-xs text-gray-500">{{ record.date }}</div>
                  <div class="text-sm font-medium">{{ record.type }}</div>
                  <div class="mt-1 text-xs text-gray-600">
                    {{ record.description }}
                  </div>
                </div>
              </div>
            </div> -->
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="relative flex flex-1 pb-10">
          <!-- Pre-Call Page -->
          <div v-if" class="flex flex-1 flex-col">
            <div class="flex-1 p-8">
              <div class="mx-auto max-w-4xl">
                <div class="mb-8 text-center">
                  <h1 class="mb-2 text-3xl font-bold text-gray-900">
                    Ready for your appointment?
                  </h1>
                  <p class="text-gray-600">
                    click join video call button and wait for the doctor to start the call
                  </p>

                </div>

                <!-- Appointment Card -->
                <div
                  class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                >
                  <div class="flex items-start space-x-4">
                    <div
                      class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-600"
                    >
                      <span class="text-2xl font-semibold text-white">DS</span>
                    </div>
                    <div class="flex-1">
                      <h2 class="text-xl font-semibold text-gray-900">
                        {{
                          consultation.data?.doctor.name ||
                          appointmentInfo.doctor
                        }}
                      </h2>
                      <p class="mb-4 text-gray-600">
                        {{
                          consultation.data?.doctor.doctor?.specialization ||
                          appointmentInfo.specialty
                        }}
                      </p>

                      <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center space-x-2">
                          <svg
                            class="h-4 w-4 text-gray-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                          </svg>
                          <span>{{
                            consultation.data?.consultation_date ||
                            appointmentInfo.date
                          }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                          <svg
                            class="h-4 w-4 text-gray-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                          </svg>
                          <span>{{ appointmentInfo.type }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                          <svg
                            class="h-4 w-4 text-gray-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                            />
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                          </svg>
                          <span>{{  consultation.data?.doctor.city }}</span>
                        </div>
                      </div>

                      <div class="mt-4 rounded-lg bg-blue-50 p-3">
                        <p class="text-sm text-blue-800">
                          <strong>Reason for visit:</strong>
                          {{
                            consultation.data?.notes || appointmentInfo.reason
                          }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Pre-visit Instructions -->
                <div
                  class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                >
                  <h3 class="mb-4 text-lg font-semibold text-gray-900">
                    Before we begin
                  </h3>
                  <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start space-x-2">
                      <svg
                        class="mt-0.5 h-4 w-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span>Ensure you're in a quiet, private space</span>
                    </li>
                    <li class="flex items-start space-x-2">
                      <svg
                        class="mt-0.5 h-4 w-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span>Have your current medications list ready</span>
                    </li>
                    <li class="flex items-start space-x-2">
                      <svg
                        class="mt-0.5 h-4 w-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span>Prepare any questions you'd like to discuss</span>
                    </li>
                    <li class="flex items-start space-x-2">
                      <svg
                        class="mt-0.5 h-4 w-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span>Have good lighting on your face</span>
                    </li>
                  </ul>
                </div>
              
                <!-- Join Call Button -->
                <div class="text-center">
               
                  <button
                    @click="init"
                    class="rounded-lg bg-blue-600 px-8 py-4 text-lg font-semibold text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  >
                    Join Video Call
                  </button>
                  <p class="mt-2 text-sm text-gray-500">
                    Your doctor will be notified when you join
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Floating Chat Button (when chat is hidden) -->
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-x-4"
            enter-to-class="opacity-100 scale-100 translate-x-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-x-0"
            leave-to-class="opacity-0 scale-95 translate-x-4"
          >
            <button
              v-if="!isChatVisible"
              @click="toggleChat"
              class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50"
              title="Open Chat"
            >
              <!-- Chat Icon -->
              <svg
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-2.345-.306c-.52.263-1.5.634-3.345.905-1.64.24-2.577-.747-2.336-2.388.24-1.644.643-2.775.906-3.345A8.955 8.955 0 014 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"
                />
              </svg>
              <!-- Notification Badge (shows number of messages) -->
              <div
                v-if="consultationStore.chatMessages.length > 0"
                class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"
              >
                {{
                  consultationStore.chatMessages.length > 9
                    ? "9+"
                    : consultationStore.chatMessages.length
                }}
              </div>
            </button>
          </Transition>

          <!-- Chat Section - Toggleable with Transitions -->
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
          >
            <div
              v-if="isChatVisible"
              class="flex w-96 flex-col border-l border-gray-200 bg-white"
            >
              <!-- Chat Header -->
              <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                  <h2
                    class="flex items-center text-lg font-semibold text-gray-900"
                  >
                    <svg
                      class="mr-2 h-5 w-5 text-blue-600"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                      />
                    </svg>
                    Chat with Doctor
                    <span
                      v-if="consultationStore.isConnectedToChat"
                      class="ml-2 flex items-center text-sm text-green-600"
                    >
                      <div
                        class="mr-1 h-2 w-2 animate-pulse rounded-full bg-green-500"
                      ></div>
                      Live
                    </span>
                    <span
                      v-else
                      class="ml-2 flex items-center text-sm text-gray-500"
                    >
                      <div class="mr-1 h-2 w-2 rounded-full bg-gray-400"></div>
                      Connecting...
                    </span>
                  </h2>
                  <!-- Close Chat Button -->
                  <button
                    @click="toggleChat"
                    class="rounded-lg p-2 text-gray-400 transition-all duration-200 hover:rotate-90 hover:bg-gray-100 hover:text-gray-600"
                    title="Close Chat"
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
                        d="M6 18L18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </div>
              </div>
              <!-- Messages -->
              <div
                ref="chatContainer"
                class="flex-1 space-y-4 overflow-y-auto px-6 py-4"
              >
                <div
                  v-if="consultationStore.chatMessages.length === 0"
                  class="py-8 text-center text-gray-500"
                >
                  <svg
                    class="mx-auto mb-3 h-12 w-12 text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                    />
                  </svg>
                  <p>No messages yet. Start the conversation!</p>
                </div>

                <div
                  v-for="(msg, index) in consultationStore.chatMessages"
                  :key="index"
                  :class="`flex ${msg.sender === 'Patient' ? 'justify-end' : 'justify-start'}`"
                >
                  <div
                    :class="`max-w-xs rounded-lg px-4 py-2 transition-all duration-200 hover:shadow-md ${
                      msg.sender === 'Patient'
                        ? 'bg-blue-600 text-white hover:bg-blue-700'
                        : 'bg-gray-100 text-gray-900 hover:bg-gray-200'
                    }`"
                  >
                    <p class="text-sm">{{ msg.text }}</p>
                    <p
                      :class="`mt-1 text-xs ${msg.sender === 'Patient' ? 'text-blue-100' : 'text-gray-500'}`"
                    >
                      {{ msg.sender }} • {{ formatDate(msg.timestamp) }}
                    </p>
                  </div>
                </div>
              </div>
              <!-- Message Input -->
              <div class="border-t border-gray-200 px-6 py-4">
                <div class="flex space-x-2">
                  <input
                    v-model="newMessage"
                    @keypress="handleKeyPress"
                    type="text"
                    placeholder="Type your message..."
                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 transition-all duration-200 focus:border-transparent focus:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100"
                    :disabled="
                      consultationStore.isSendingMessage ||
                      !consultationStore.isConnectedToChat
                    "
                  />
                  <button
                    @click="handleSendMessage"
                    :disabled="
                      consultationStore.isSendingMessage ||
                      !newMessage.trim() ||
                      !consultationStore.isConnectedToChat
                    "
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-all duration-200 hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-95 disabled:cursor-not-allowed disabled:bg-gray-400"
                  >
                    <!-- Send Icon with Loading State -->
                    <span
                      v-if="consultationStore.isSendingMessage"
                      class="flex items-center"
                    >
                      <svg
                        class="mr-1 h-4 w-4 animate-spin"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                      </svg>
                      Sending...
                    </span>
                    <span v-else>
                      <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                        />
                      </svg>
                    </span>
                  </button>
                </div>
                <p
                  v-if="!consultationStore.isConnectedToChat"
                  class="mt-2 text-xs text-gray-500"
                >
                  Connecting to chat... Messages will be sent once connected.
                </p>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<style scoped>
/* Additional smooth animations for better UX */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Floating button pulse animation */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.floating-chat-btn {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Message hover effects */
.message-hover {
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.message-hover:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
