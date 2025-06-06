<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import Peer from "peerjs";
import UserLayout from "@/layout/UserLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { usePatientStore } from "@/stores/patientStore";
import { useDoctorStore } from "@/stores/doctorStore";

const { getAllPatients } = usePatientStore();
const { getAllUsers } = useAuthStore();
const authStore = useAuthStore();

const auth = ref(null);
const users = ref([]);
const selectedUser = ref(null);
const peer = new Peer();
const peerCall = ref(null);
const remoteVideo = ref(null);
const localVideo = ref(null);
const isCalling = ref(false);
const localStream = ref(null);
const permissionError = ref(null);
const permissionGranted = ref(false);

// Check and request media permissions
const checkMediaPermissions = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: true, 
      audio: true 
    });
    
    // Stop the stream immediately as we're just checking permissions
    stream.getTracks().forEach(track => track.stop());
    
    permissionGranted.value = true;
    permissionError.value = null;
    return true;
  } catch (err) {
    console.error("Permission denied:", err);
    permissionError.value = err.message;
    permissionGranted.value = false;
    return false;
  }
};

const callUser = async () => {
  // Check permissions before making a call
  const hasPermission = await checkMediaPermissions();
  if (!hasPermission) {
    alert("Please allow camera and microphone access to make video calls.");
    return;
  }

  const response = await fetch(
    `/api/video-call/request/${selectedUser.value.id}`,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
      body: JSON.stringify({ peerId: peer.id }),
    },
  );

  isCalling.value = true;
  displayLocalVideo();
};

const endCall = () => {
  if (peerCall.value) {
    peerCall.value.close();
  }
  if (localStream.value) {
    localStream.value.getTracks().forEach((track) => track.stop());
  }
  if (remoteVideo.value) {
    remoteVideo.value.srcObject = null;
  }
  if (localVideo.value) {
    localVideo.value.srcObject = null;
  }
  isCalling.value = false;
  localStream.value = null;
};

const displayLocalVideo = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: true, 
      audio: true 
    });
    
    if (localVideo.value) {
      localVideo.value.srcObject = stream;
    }
    localStream.value = stream;
    permissionError.value = null;
  } catch (err) {
    console.error("Error accessing media devices:", err);
    permissionError.value = "Failed to access camera/microphone. Please check permissions.";
    isCalling.value = false;
  }
};

const setSelectedUser = (user) => {
  selectedUser.value = user;
};

const recipientAcceptCall = async (e) => {
  // Check permissions before accepting call
  const hasPermission = await checkMediaPermissions();
  if (!hasPermission) {
    alert("Please allow camera and microphone access to accept video calls.");
    return;
  }

  // send signal that recipient accept the call
  await fetch(`/api/video-call/request/status/${e.user.fromUser.id}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
    body: JSON.stringify({ peerId: peer.id, status: "accept" }),
  });

  // stand by for callers connection
  peer.on("call", (call) => {
    // will be used when ending a call
    peerCall.value = call;
    // accept call if the caller is the one that you accepted
    if (e.user.peerId == call.peer) {
      // Prompt user to allow media devices
      navigator.mediaDevices
        .getUserMedia({ video: true, audio: true })
        .then((stream) => {
          // Answer the call with your stream
          call.answer(stream);
          // Listen for the caller's stream
          call.on("stream", (remoteStream) => {
            if (remoteVideo.value) {
              remoteVideo.value.srcObject = remoteStream;
            }
          });

          // caller end the call
          call.on("close", () => {
            endCall();
          });
        })
        .catch((err) => {
          console.error("Error accessing media devices:", err);
          permissionError.value = "Failed to access camera/microphone during call.";
        });
    }
  });
};

const createConnection = (e) => {
  let receiverId = e.user.peerId;
  navigator.mediaDevices
    .getUserMedia({ video: true, audio: true })
    .then((stream) => {
      // Initiate the call with the receiver's ID
      const call = peer.call(receiverId, stream);

      // will be used when ending a call
      peerCall.value = call;

      // Listen for the receiver's stream
      call.on("stream", (remoteStream) => {
        if (remoteVideo.value) {
          remoteVideo.value.srcObject = remoteStream;
        }
      });

      // receiver end the call
      call.on("close", () => {
        endCall();
      });
    })
    .catch((err) => {
      console.error("Error accessing media devices:", err);
      permissionError.value = "Failed to establish connection.";
    });
};

const connectWebSocket = () => {
  // Check if Echo is available
  if (!window.Echo) {
    console.error("Laravel Echo is not initialized");
    return;
  }

  // request video call
  window.Echo.private(`video-call.${auth.value.user.id}`).listen(
    "RequestVideoCall",
    (e) => {
      selectedUser.value = e.user.fromUser;
      isCalling.value = true;
      recipientAcceptCall(e);
      displayLocalVideo();
    },
  );

  // video call request accepted
  window.Echo.private(`video-call.${auth.value.user.id}`).listen(
    "RequestVideoCallStatus",
    (e) => {
      createConnection(e);
    },
  );
};

onMounted(async () => {
  // Get authenticated user
  await authStore.getUser();
  auth.value = authStore;

  // Get all patients/users
  users.value = await getAllUsers();
  console.log("Users:", users.value);

  // Check initial permissions
  await checkMediaPermissions();

  // Add a delay to ensure Echo is initialized
  setTimeout(() => {
    connectWebSocket();
  }, 1000);
});

onBeforeUnmount(() => {
  if (window.Echo && auth.value?.user?.id) {
    window.Echo.leave(`video-call.${auth.value.user.id}`);
  }
  
  // Clean up media streams
  if (localStream.value) {
    localStream.value.getTracks().forEach(track => track.stop());
  }
});
</script>

<template>
  <UserLayout>
    <div
      class="mx-auto flex h-screen max-w-7xl bg-gray-100 sm:px-6 lg:px-8"
      style="height: 90vh"
    >
      <!-- Sidebar -->
      <div class="w-1/4 border-r border-gray-200 bg-white">
        <div class="border-b border-gray-200 bg-gray-100 p-4 text-lg font-bold">
          Contacts
        </div>
        <div class="space-y-4 p-4">
          <!-- Permission Status -->
          <div v-if="permissionError" class="rounded bg-red-100 p-3 text-red-700">
            <p class="text-sm">{{ permissionError }}</p>
            <button 
              @click="checkMediaPermissions" 
              class="mt-2 text-xs underline"
            >
              Try Again
            </button>
          </div>
          
          <div v-else-if="permissionGranted" class="rounded bg-green-100 p-3 text-green-700">
            <p class="text-sm">✓ Camera and microphone access granted</p>
          </div>
          
          <!-- Contact List -->
          <div
            v-for="(user, key) in users"
            :key="key"
            @click="setSelectedUser(user)"
            :class=" [
              'flex cursor-pointer items-center rounded p-2 hover:bg-blue-500 hover:text-white',
              user.id === selectedUser?.id ? 'bg-blue-500 text-white' : '',
            ]"
          >
            <div class="h-12 w-12 rounded-full bg-blue-200"></div>
            <div class="ml-4">
              <div class="font-semibold">{{ user.name }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Area -->
      <div class="flex w-3/4 flex-col">
        <!-- No Conversation Selected -->
        <div
          v-if="!selectedUser"
          class="flex h-full items-center justify-center font-bold text-gray-800"
        >
          Select Contact
        </div>

        <template v-else>
          <!-- Contact Header -->
          <div class="flex items-center border-b border-gray-200 p-4">
            <div class="h-12 w-12 rounded-full bg-blue-200"></div>
            <div class="ml-4">
              <div class="font-bold">
                {{ selectedUser?.name }}
                <button
                  v-if="!isCalling && permissionGranted"
                  @click="callUser"
                  class="ml-4 rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                >
                  Call
                </button>
                <button
                  v-if="!isCalling && !permissionGranted"
                  @click="checkMediaPermissions"
                  class="ml-4 rounded-lg bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600"
                >
                  Allow Camera Access
                </button>
                <button
                  v-if="isCalling"
                  @click="endCall"
                  class="ml-4 rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600"
                >
                  End Call
                </button>
              </div>
            </div>
          </div>

          <div class="relative flex-1 space-y-4 overflow-y-auto bg-gray-50 p-4">
            <template v-if="isCalling">
              <video
                id="remoteVideo"
                ref="remoteVideo"
                autoplay
                playsinline
                class="w-full border-2 border-gray-800"
              ></video>
              <video
                id="localVideo"
                ref="localVideo"
                autoplay
                playsinline
                muted
                class="absolute right-6 top-6 m-0 w-4/12 border-2 border-gray-800"
                style="margin: 0"
              ></video>
            </template>
            <div
              v-if="!isCalling"
              class="flex h-full items-center justify-center font-bold text-gray-800"
            >
              No Ongoing Call.
            </div>
          </div>
        </template>
      </div>
    </div>
  </UserLayout>
</template>
