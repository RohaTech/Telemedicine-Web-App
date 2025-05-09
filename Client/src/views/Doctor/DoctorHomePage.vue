<script setup>
import DoctorLayout from "@/layout/DoctorLayout.vue";
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const { user } = authStore;
const router = useRouter();

const isLoading = ref(true);
const dashboardData = ref({
  upcomingAppointments: 0,
  pendingLabRequests: 0,
  unreadNotifications: 0,
});

const fetchDashboardData = async () => {
  try {
    isLoading.value = true;
    const response = await fetch("/api/doctor/dashboard", {
      method: "GET",
      headers: {
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) {
      throw new Error("Failed to fetch dashboard data");
    }

    const data = await response.json();
    dashboardData.value = data;
  } catch (error) {
    console.error("Error fetching dashboard data:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(async () => {
  if (!user) {
    await authStore.getUser();
  }
  if (user && user.role === "doctor") {
    await fetchDashboardData();
  } else {
    router.push({ name: "Login" });
  }
});

// Computed property to determine greeting based on time
const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) {
    return "Good Morning";
  } else if (hour < 18) {
    return "Good Afternoon";
  } else {
    return "Good Evening";
  }
});
</script>

<template>
  <DoctorLayout>
    <header
      class="flex items-center justify-between bg-white px-6 py-8 shadow-md"
    >
      <!-- Left Section: Date and Greeting -->
      <div>
        <p class="text-sm text-gray-500">
          {{ new Date().toLocaleDateString() }}
        </p>
        <h1 class="text-xl font-bold text-gray-800">
          {{ greeting }},
          <span class="uppercase">{{ user?.name || "Doctor" }}</span>
        </h1>
      </div>

      <!-- Right Section: Feedback -->
      <div class="flex items-center space-x-4">
        <a
          class="text-sm text-gray-500 hover:underline"
          href="mailto:rohatechofficial@gmail.com"
        >
          Give Feedback
        </a>
      </div>
    </header>

    <!-- Dashboard Metrics -->
    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
      <!-- Appointments Metric -->
      <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6">
        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100"
        >
          <svg
            class="fill-blue-600"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="24"
            height="24"
          >
            <path
              d="M19 3h-1V2a1 1 0 10-2 0v1H8V2a1 1 0 10-2 0v1H5a3 3 0 00-3 3v13a3 3 0 003 3h14a3 3 0 003-3V6a3 3 0 00-3-3zm1 16a1 1 0 01-1 1H5a1 1 0 01-1-1V10h16v9zm0-11H4V6a1 1 0 011-1h1v1a1 1 0 102 0V5h8v1a1 1 0 102 0V5h1a1 1 0 011 1v2z"
            />
          </svg>
        </div>
        <div class="mt-5">
          <span class="text-sm text-gray-500">Upcoming Appointments</span>
          <h4 class="mt-2 text-title-sm font-bold text-gray-800">
            {{ dashboardData.upcomingAppointments }}
          </h4>
        </div>
      </div>

      <!-- Lab Requests Metric -->
      <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6">
        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100"
        >
          <svg
            class="fill-green-600"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="24"
            height="24"
          >
            <path
              d="M21 2H3a1 1 0 00-1 1v18a1 1 0 001 1h18a1 1 0 001-1V3a1 1 0 00-1-1zM4 4h16v10H4V4zm0 16v-4h16v4H4z"
            />
          </svg>
        </div>
        <div class="mt-5">
          <span class="text-sm text-gray-500">Pending Lab Requests</span>
          <h4 class="mt-2 text-title-sm font-bold text-gray-800">
            {{ dashboardData.pendingLabRequests }}
          </h4>
        </div>
      </div>

      <!-- Notifications Metric -->
      <!-- <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6">
        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100"
        >
          <svg
            class="fill-yellow-600"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="24"
            height="24"
          >
            <path
              d="M12 2a7 7 0 00-7 7v4.586l-1.707 1.707A1 1 0 004 17h16a1 1 0 00.707-1.707L19 13.586V9a7 7 0 00-7-7zm0 18a3 3 0 01-2.829-2h5.658A3 3 0 0112 20z"
            />
          </svg>
        </div>
        <div class="mt-5">
          <span class="text-sm text-gray-500">Unread Notifications</span>
          <h4 class="mt-2 text-title-sm font-bold text-gray-800">
            {{ dashboardData.unreadNotifications }}
          </h4>
        </div>
      </div> -->
    </section>
  </DoctorLayout>
</template>
