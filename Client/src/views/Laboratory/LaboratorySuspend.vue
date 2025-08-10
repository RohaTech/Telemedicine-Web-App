<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const laboratoryName = ref("");
const suspensionReason = ref("");
const contactEmail = ref("rohatech@gmail.com");
const contactPhone = ref("+251921607264");

onMounted(() => {
  // Get laboratory information from auth store
  if (authStore.user) {
    laboratoryName.value = authStore.user.name || "Your Laboratory";
    // You can add suspension reason from user data if available
    suspensionReason.value =
      authStore.user.suspension_reason ||
      "Policy violation or administrative review";
  }
});

const handleLogout = async () => {
  await authStore.logout();
  router.push({ name: "LaboratoryLoginPage" });
};

const handleContactSupport = () => {
  window.location.href = `mailto:${contactEmail.value}?subject=Laboratory Account Suspension - ${laboratoryName.value}`;
};
</script>

<template>
  <div
    class="flex min-h-screen flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div
        class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-red-100"
      >
        <svg
          class="h-8 w-8 text-red-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"
          />
        </svg>
      </div>

      <h2 class="mb-2 text-center text-3xl font-extrabold text-gray-900">
        Account Suspended
      </h2>

      <p class="mb-8 text-center text-sm text-gray-600">
        Laboratory access has been temporarily restricted
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white px-4 py-8 shadow-lg sm:rounded-lg sm:px-10">
        <!-- Laboratory Information -->
        <div class="mb-6">
          <div class="mb-4 flex items-center justify-center">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100"
            >
              <svg
                class="h-6 w-6 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 008 10.172V5L8 4z"
                />
              </svg>
            </div>
          </div>

          <h3 class="mb-2 text-center text-lg font-medium text-gray-900">
            {{ laboratoryName }}
          </h3>

          <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-red-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  Suspension Notice
                </h3>
                <div class="mt-2 text-sm text-red-700">
                  <p>
                    Your laboratory account has been suspended due to:
                    {{ suspensionReason }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Support Section -->
        <div class="mb-6 rounded-md border border-blue-200 bg-blue-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-blue-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Need Help?</h3>
              <div class="mt-2 text-sm text-blue-700">
                <p>
                  If you believe this suspension is in error or need assistance,
                  please contact our support team immediately.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="mb-6 space-y-4">
          <div
            class="flex items-center justify-between rounded-lg bg-gray-50 p-3"
          >
            <div class="flex items-center">
              <svg
                class="mr-3 h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
              <span class="text-sm text-gray-600">Email Support</span>
            </div>
            <span class="text-sm font-medium text-gray-900">{{
              contactEmail
            }}</span>
          </div>

          <div
            class="flex items-center justify-between rounded-lg bg-gray-50 p-3"
          >
            <div class="flex items-center">
              <svg
                class="mr-3 h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                />
              </svg>
              <span class="text-sm text-gray-600">Phone Support</span>
            </div>
            <span class="text-sm font-medium text-gray-900">{{
              contactPhone
            }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
          <button
            @click="handleContactSupport"
            class="flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            <svg
              class="mr-2 h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
            Contact Support Team
          </button>

          <button
            @click="handleLogout"
            class="flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
          >
            <svg
              class="mr-2 h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
              />
            </svg>
            Sign Out
          </button>
        </div>

        <!-- Footer Note -->
        <div class="mt-6 text-center">
          <p class="text-xs text-gray-500">
            This suspension is temporary and can be resolved by contacting our
            support team.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add any custom styles if needed */
</style>
