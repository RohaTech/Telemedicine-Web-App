<script setup>
import DoctorLayout from "@/layout/DoctorLayout.vue";
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useAppointmentStore } from "@/stores/appointmentStore";

const authStore = useAuthStore();
const { getAppointmentsByStatus } = useAppointmentStore();

const { user } = authStore;
const router = useRouter();

const isLoading = ref(true);
const dashboardData = ref({
  upcomingAppointments: 0,
  pendingLabRequests: 0,
  unreadNotifications: 0,
});

const waitingAppointments = ref([]);

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
    waitingAppointments.value = await getAppointmentsByStatus("waiting");

    console.log("unscheduled Consultations Data:", waitingAppointments.value);
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
            class="fill-[#2FC7A1]"
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
            class="fill-[#048581]"
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
      <RouterLink
        :to="{ name: 'DoctorWaitingAppointments' }"
        class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6"
      >
        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100"
        >
          <svg
            class="fill-yellow-600"
            xmlns="http://www.w3.org/2000/svg"
            width="32"
            height="32"
            version="1.1"
            viewBox="0 0 100 100"
          >
            <path
              d="M86.04,14.5H75.5v-2.53c0-1.91-1.55-3.47-3.47-3.47h-4.07c-1.91,0-3.47,1.55-3.47,3.47v2.53h-29v-2.53
                c0-1.91-1.55-3.47-3.47-3.47h-4.07c-1.91,0-3.47,1.55-3.47,3.47v2.53H13.96c-3.01,0-5.46,2.45-5.46,5.46v58.08
                c0,3.01,2.45,5.46,5.46,5.46H40c0.83,0,1.5-0.67,1.5-1.5s-0.67-1.5-1.5-1.5H13.96c-1.36,0-2.46-1.1-2.46-2.46V19.96
                c0-1.36,1.1-2.46,2.46-2.46H24.5v4.53c0,1.91,1.55,3.47,3.47,3.47h4.07c1.91,0,3.47-1.55,3.47-3.47V17.5h29v4.53
                c0,1.91,1.55,3.47,3.47,3.47h4.07c1.91,0,3.47-1.55,3.47-3.47V17.5h10.54c1.36,0,2.46,1.1,2.46,2.46V46c0,0.83,0.67,1.5,1.5,1.5
                s1.5-0.67,1.5-1.5V19.96C91.5,16.95,89.05,14.5,86.04,14.5z M32.5,22.03c0,0.26-0.21,0.47-0.47,0.47h-4.07
                c-0.26,0-0.47-0.21-0.47-0.47V11.97c0-0.26,0.21-0.47,0.47-0.47h4.07c0.26,0,0.47,0.21,0.47,0.47V22.03z M72.5,22.03
                c0,0.26-0.21,0.47-0.47,0.47h-4.07c-0.26,0-0.47-0.21-0.47-0.47V11.97c0-0.26,0.21-0.47,0.47-0.47h4.07c0.26,0,0.47,0.21,0.47,0.47
                V22.03z M56,39.5c-0.83,0-1.5-0.67-1.5-1.5s0.67-1.5,1.5-1.5h4c0.83,0,1.5,0.67,1.5,1.5s-0.67,1.5-1.5,1.5H56z M72,39.5
                c-0.83,0-1.5-0.67-1.5-1.5s0.67-1.5,1.5-1.5h4c0.83,0,1.5,0.67,1.5,1.5s-0.67,1.5-1.5,1.5H72z M29.5,38c0,0.83-0.67,1.5-1.5,1.5h-4
                c-0.83,0-1.5-0.67-1.5-1.5s0.67-1.5,1.5-1.5h4C28.83,36.5,29.5,37.17,29.5,38z M38.5,38c0-0.83,0.67-1.5,1.5-1.5h4
                c0.83,0,1.5,0.67,1.5,1.5s-0.67,1.5-1.5,1.5h-4C39.17,39.5,38.5,38.83,38.5,38z M29.5,52c0,0.83-0.67,1.5-1.5,1.5h-4
                c-0.83,0-1.5-0.67-1.5-1.5s0.67-1.5,1.5-1.5h4C28.83,50.5,29.5,51.17,29.5,52z M44,53.5h-4c-0.83,0-1.5-0.67-1.5-1.5
                s0.67-1.5,1.5-1.5h4c0.83,0,1.5,0.67,1.5,1.5S44.83,53.5,44,53.5z M29.5,66c0,0.83-0.67,1.5-1.5,1.5h-4c-0.83,0-1.5-0.67-1.5-1.5
                s0.67-1.5,1.5-1.5h4C28.83,64.5,29.5,65.17,29.5,66z M41.06,64.94c0.28,0.28,0.44,0.67,0.44,1.06c0,0.1-0.01,0.2-0.03,0.29
                c-0.02,0.1-0.05,0.19-0.08,0.28c-0.04,0.1-0.09,0.18-0.14,0.26c-0.06,0.09-0.12,0.16-0.19,0.23c-0.07,0.07-0.14,0.13-0.23,0.19
                c-0.08,0.05-0.17,0.1-0.26,0.14c-0.09,0.03-0.18,0.06-0.28,0.08C40.2,67.49,40.1,67.5,40,67.5c-0.1,0-0.2-0.01-0.29-0.03
                c-0.1-0.02-0.19-0.05-0.28-0.08c-0.09-0.04-0.18-0.09-0.26-0.14c-0.08-0.06-0.16-0.12-0.23-0.19c-0.28-0.28-0.44-0.67-0.44-1.06
                c0-0.39,0.16-0.78,0.44-1.06c0.07-0.07,0.15-0.13,0.23-0.19c0.08-0.05,0.17-0.1,0.26-0.13c0.09-0.04,0.18-0.07,0.28-0.09
                C40.19,64.43,40.71,64.59,41.06,64.94z M68,44.5c-12.96,0-23.5,10.54-23.5,23.5S55.04,91.5,68,91.5S91.5,80.96,91.5,68
                S80.96,44.5,68,44.5z M69.5,88.42V88c0-0.83-0.67-1.5-1.5-1.5s-1.5,0.67-1.5,1.5v0.42c-10.11-0.74-18.19-8.82-18.92-18.92H48
                c0.83,0,1.5-0.67,1.5-1.5s-0.67-1.5-1.5-1.5h-0.42c0.74-10.11,8.82-18.19,18.92-18.92V48c0,0.83,0.67,1.5,1.5,1.5s1.5-0.67,1.5-1.5
                v-0.42c10.11,0.74,18.19,8.82,18.92,18.92H88c-0.83,0-1.5,0.67-1.5,1.5s0.67,1.5,1.5,1.5h0.42C87.69,79.61,79.61,87.69,69.5,88.42z
                M77.06,74.94c0.59,0.59,0.59,1.54,0,2.12c-0.29,0.29-0.68,0.44-1.06,0.44s-0.77-0.15-1.06-0.44l-8-8C66.66,68.78,66.5,68.4,66.5,68
                V58c0-0.83,0.67-1.5,1.5-1.5s1.5,0.67,1.5,1.5v9.38L77.06,74.94z"
            ></path>
          </svg>
        </div>
        <div class="mt-5">
          <span class="text-sm text-gray-500">Waiting Appointments</span>
          <h4 class="mt-2 text-title-sm font-bold text-gray-800">
            {{ waitingAppointments.data?.length }}
          </h4>
        </div>
      </RouterLink>

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
