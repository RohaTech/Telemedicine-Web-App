<script setup>
import { useLaboratoryStore } from "@/stores/laboratoryStore";
import { usePatientStore } from "@/stores/patientStore";
import { onMounted, ref } from "vue";

const { getPendingLaboratories } = useLaboratoryStore();
const { getAllPatients } = usePatientStore();
const laboratories = ref([]);
onMounted(async () => {
  laboratories.value = await getAllPatients();
  console.log(laboratories.value);
});

const users = ref([
  {
    name: "Lindsey Curtis",
    role: "Web Designer",
    avatar: "/images/user/user-17.jpg",
    project: "Agency Website",
    team: [
      "/images/user/user-22.jpg",
      "/images/user/user-23.jpg",
      "/images/user/user-24.jpg",
    ],
    status: "Active",
    budget: "3.9K",
  },
  {
    name: "Kaiya George",
    role: "Project Manager",
    avatar: "/images/user/user-18.jpg",
    project: "Technology",
    team: ["/images/user/user-25.jpg", "/images/user/user-26.jpg"],
    status: "Pending",
    budget: "24.9K",
  },
  {
    name: "Zain Geidt",
    role: "Content Writer",
    avatar: "/images/user/user-19.jpg",
    project: "Blog Writing",
    team: ["/images/user/user-27.jpg"],
    status: "Active",
    budget: "12.7K",
  },
  {
    name: "Abram Schleifer",
    role: "Digital Marketer",
    avatar: "/images/user/user-20.jpg",
    project: "Social Media",
    team: [
      "/images/user/user-28.jpg",
      "/images/user/user-29.jpg",
      "/images/user/user-30.jpg",
    ],
    status: "Cancel",
    budget: "2.8K",
  },
  {
    name: "Carla George",
    role: "Front-end Developer",
    avatar: "/images/user/user-21.jpg",
    project: "Website",
    team: [
      "/images/user/user-31.jpg",
      "/images/user/user-32.jpg",
      "/images/user/user-33.jpg",
    ],
    status: "Active",
    budget: "4.5K",
  },
]);
</script>

<template>
  <div
    class="ark:border-gray-800 ark:bg-white/[0.03] overflow-hidden rounded-xl border border-gray-200 bg-white"
  >
    <div class="custom-scrollbar max-w-full overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="ark:border-gray-700 border-b border-gray-200">
            <th class="w-3/11 px-5 py-3 text-left sm:px-6">
              <p
                class="ark:text-gray-400 text-theme-xs font-medium text-gray-500"
              >
                User
              </p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p
                class="ark:text-gray-400 text-theme-xs font-medium text-gray-500"
              >
                Project Name
              </p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p
                class="ark:text-gray-400 text-theme-xs font-medium text-gray-500"
              >
                Team
              </p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p
                class="ark:text-gray-400 text-theme-xs font-medium text-gray-500"
              >
                Status
              </p>
            </th>
            <th class="w-2/11 px-5 py-3 text-left sm:px-6">
              <p
                class="ark:text-gray-400 text-theme-xs font-medium text-gray-500"
              >
                Budget
              </p>
            </th>
          </tr>
        </thead>
        <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
          <tr
            v-for="(user, index) in users"
            :key="index"
            class="ark:border-gray-800 border-t border-gray-100"
          >
            <td class="px-5 py-4 sm:px-6">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 overflow-hidden rounded-full">
                  <img :src="user.avatar" :alt="user.name" />
                </div>
                <div>
                  <span
                    class="ark:text-white/90 block text-theme-sm font-medium text-gray-800"
                  >
                    {{ user.name }}
                  </span>
                  <span
                    class="ark:text-gray-400 block text-theme-xs text-gray-500"
                  >
                    {{ user.role }}
                  </span>
                </div>
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="ark:text-gray-400 text-theme-sm text-gray-500">
                {{ user.project }}
              </p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="flex -space-x-2">
                <div
                  v-for="(member, memberIndex) in user.team"
                  :key="memberIndex"
                  class="ark:border-gray-900 h-6 w-6 overflow-hidden rounded-full border-2 border-white"
                >
                  <img :src="member" alt="team member" />
                </div>
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <span
                :class="[
                  'rounded-full px-2 py-0.5 text-theme-xs font-medium',
                  {
                    'ark:bg-success-500/15 ark:text-success-500 bg-success-50 text-success-700':
                      user.status === 'Active',
                    'ark:bg-warning-500/15 ark:text-warning-400 bg-warning-50 text-warning-700':
                      user.status === 'Pending',
                    'ark:bg-error-500/15 ark:text-error-500 bg-error-50 text-error-700':
                      user.status === 'Cancel',
                  },
                ]"
              >
                {{ user.status }}
              </span>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="ark:text-gray-400 text-theme-sm text-gray-500">
                {{ user.budget }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
