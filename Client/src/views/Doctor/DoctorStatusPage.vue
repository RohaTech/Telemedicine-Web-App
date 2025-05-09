<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useDoctorStore } from "@/stores/doctorStore";
import { storeToRefs } from "pinia";

const authStore = useAuthStore();
const doctorStore = useDoctorStore();

const { user, errors } = storeToRefs(authStore);
const router = useRouter();
const { status } = storeToRefs(doctorStore);
const isLoading = ref(true);

const fetchStatus = async () => {
  try {
    isLoading.value = true;
    await doctorStore.fetchStatus();
    if (status.value === "active") {
      router.push({ name: "DoctorHome" });
    }
  } catch (error) {
    console.error("Error fetching status:", error);
    // Errors are handled by doctorStore
  } finally {
    isLoading.value = false;
  }
};

onMounted(async () => {
  if (!user.value) {
    await authStore.getUser();
  }
  if (user.value && user.value.role === "doctor") {
    await fetchStatus();
  } else {
    router.push({ name: "Login" });
  }
});

const goToHome = () => {
  router.push({ name: "DoctorHome" });
};

const contactSupport = () => {
  window.location.href = "mailto:rohatechofficial@gmail.com";
};
</script>

<template>
  <UserLayout>
    <div class="flex min-h-screen justify-center px-4 py-12 sm:px-6 lg:px-8">
      <div
        class="w-full max-w-md border border-gray-50 bg-[#f3f5f5] p-8 drop-shadow-md"
      >
        <h1 class="mb-4 text-2xl font-bold text-[#0F172A]">
          Registration Status
        </h1>

        <!-- Loading State -->
        <div v-if="isLoading" class="text-center">
          <p class="text-gray-600">Loading your status...</p>
          <svg
            class="mx-auto mt-4 h-8 w-8 animate-spin text-[#26C6DA]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z"
            ></path>
          </svg>
        </div>

        <!-- Error State -->
        <div
          v-if="errors.general && !isLoading"
          class="mb-4 rounded-lg bg-red-100 p-4 text-red-700"
        >
          {{ errors.general }}
          <button
            @click="fetchStatus"
            class="mt-2 text-sm text-[#26C6DA] hover:underline"
          >
            Try Again
          </button>
        </div>

        <!-- Status Display -->
        <div v-if="status && !isLoading" class="space-y-4">
          <div
            v-if="status === 'pending'"
            class="rounded-lg bg-yellow-100 p-4 text-yellow-700"
          >
            <p class="font-semibold">Your registration is under review.</p>
            <p class="mt-2 text-sm">
              Our admin team is reviewing your application. This typically takes
              1-3 business days. You’ll receive an email once a decision is
              made.
            </p>
            <p class="mt-2 text-sm">
              Please check your email (including spam/junk) for updates.
            </p>
          </div>
          <div
            v-else-if="status === 'active'"
            class="rounded-lg bg-green-100 p-4 text-green-700"
          >
            <p class="font-semibold">Your registration is approved!</p>
            <p class="mt-2 text-sm">
              Congratulations! You can now log in to start providing healthcare
              services.
            </p>
            <button
              @click="goToHome"
              class="mt-4 rounded-3xl bg-green-500 px-4 py-2 text-sm uppercase text-white hover:bg-green-700"
            >
              Go To Home
            </button>
          </div>
          <div
            v-else-if="status === 'rejected'"
            class="rounded-lg bg-red-100 p-4 text-red-700"
          >
            <p class="font-semibold">Your registration was not approved.</p>
            <p class="mt-2 text-sm">
              We’re sorry, but your application was not approved. Please contact
              our support team for more details or to appeal this decision.
            </p>
            <button
              @click="contactSupport"
              class="mt-4 rounded-3xl bg-red-500 px-4 py-2 text-sm uppercase text-white hover:bg-red-700"
            >
              Contact Support
            </button>
          </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-6 text-center text-sm text-[#64748B]">
          <p>
            Need help? Contact us at
            <a
              href="mailto:rohatechofficial@gmail.com"
              class="text-[#26C6DA] hover:underline"
              >rohatechofficial@gmail.com</a
            >
          </p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
