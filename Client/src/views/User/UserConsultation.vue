<script setup>
import { onMounted, ref, computed } from "vue";
import UserLayout from "@/layout/UserLayout.vue";
import { useConsultationStore } from "@/stores/consultationStore";

const { getUserConsultations } = useConsultationStore();

const consultations = ref([]);

const statusOptions = ["confirmed", "pending", "cancelled", "waiting"];
const selectedStatus = ref("all"); // Default to 'all'

// Helper function to get consultation status based on date
const getConsultationStatus = (consultationDate) => {
  const today = new Date();
  const consDate = new Date(consultationDate);

  // Reset time to compare only dates
  today.setHours(0, 0, 0, 0);
  consDate.setHours(0, 0, 0, 0);

  if (consDate > today) {
    return "upcoming";
  } else if (consDate < today) {
    return "passed";
  } else {
    return "today";
  }
};

onMounted(async () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
  consultations.value = await getUserConsultations();
  console.log("consultations.value", consultations.value.data);
});
</script>

<template>
  <UserLayout>
    <div
      class="ark:border-gray-800 ark:bg-white/[0.03] bg- -400 mx-auto mt-16 min-h-fit max-w-[1600px] overflow-hidden rounded-xl border border-gray-200 bg-white pb-10 shadow-sm"
    >
      <!-- Status Filter -->
      <div class="border-b border-gray-200 p-4">
        <h3 class="my-4 mb-2 py-8 text-2xl font-semibold">
          All Your Reserved Consultations
        </h3>
      </div>

      <div class="custom-scrollbar max-w-full overflow-x-auto">
        <table class="min-w-full text-center">
          <thead>
            <tr class="ark:border-gray-70 0 border-b border-gray-200">
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Name
                </p>
              </th>

              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Email
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Phone
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Consultation Date
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 text-center sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">Action</p>
              </th>
            </tr>
          </thead>
          <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
            <!-- No results message -->
            <tr v-if="!consultations">
              <td colspan="7" class="px-5 py-8 text-center sm:px-6">
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
                    No Reserved Consultation found
                  </p>
                </div>
              </td>
            </tr>
            <tr
              v-else
              v-for="(consultation, index) in consultations.data"
              :key="index"
              class="ark:border-gray-800 border-t border-gray-100"
            >
              <td class="px-5 py-4 sm:px-6">
                <div class="gap-3">
                  <span class="block text-theme-sm font-medium text-gray-800">
                    {{ consultation.doctor?.name }}
                  </span>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-theme-sm text-gray-500">
                  {{ consultation.doctor?.email }}
                </p>
              </td>
              <td class="px-5 py-4 text-center sm:px-6">
                <div class=" ">
                  {{ consultation.doctor?.phone }}
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                {{ consultation.consultation_date }}
              </td>
              <td class="px-5 py-4 sm:px-6">
                <!-- Today's consultation - Active button -->
                <RouterLink
                  :to="{
                    name: 'UserConsultationDetail',
                    params: { id: consultation.id },
                  }"
                  v-if="
                    getConsultationStatus(consultation.consultation_date) ===
                    'today'
                  "
                  class="ark:bg-gray-800 ark:text-white rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                  Go to Consultation
                </RouterLink>

                <!-- Future consultation - Disabled state -->
                <span
                  v-else-if="
                    getConsultationStatus(consultation.consultation_date) ===
                    'upcoming'
                  "
                  class="rounded-md bg-yellow-100 px-4 py-2 text-sm font-medium text-yellow-800"
                >
                  Please Wait
                </span>

                <!-- Past consultation - Disabled state -->
                <RouterLink
                  :to="{
                    name: 'UserConsultationDetail',
                    params: { id: consultation.id },
                  }"
                  v-else
                  class="ark:bg-gray-800 ark:text-white rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                  Go to Consultation
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Using the Modal component -->
    </div>
  </UserLayout>
</template>
