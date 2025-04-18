<script setup>
import {
  UserCircleIcon,
  ChevronDownIcon,
  LogoutIcon,
  SettingsIcon,
  InfoCircleIcon,
} from "@/components/UI/Icons";
import { RouterLink } from "vue-router";
import { ref, onMounted, onUnmounted } from "vue";
import owner_image from "/images/AdminLayout/owner.jpg";

const dropdownOpen = ref(false);
const dropdownRef = ref(null);

const menuItems = [
  { href: "/Home", icon: UserCircleIcon, text: "Edit profile" },
  { href: "/Home", icon: SettingsIcon, text: "Account settings" },
  { href: "/Home", icon: InfoCircleIcon, text: "Support" },
];

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const closeDropdown = () => {
  dropdownOpen.value = false;
};

const signOut = () => {
  // Implement sign out logic here
  console.log("Signing out...");
  closeDropdown();
};

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="dropdownRef">
    <button
      class="flex items-center text-gray-700 ark:text-gray-400"
      @click.prevent="toggleDropdown"
    >
      <span class="mr-3 h-11 w-11 overflow-hidden rounded-full">
        <img :src="owner_image" alt="User" />
      </span>

      <span class="text-theme-sm mr-1 block font-medium">Musharof </span>

      <ChevronDownIcon :class="{ 'rotate-180': dropdownOpen }" />
    </button>

    <!-- Dropdown Start -->
    <div
      v-if="dropdownOpen"
      class="shadow-theme-lg ark:bg-gray-dark absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 ark:border-gray-800"
    >
      <div>
        <span
          class="text-theme-sm block font-medium text-gray-700 ark:text-gray-400"
        >
          Musharof Chowdhury
        </span>
        <span
          class="text-theme-xs mt-0.5 block text-gray-500 ark:text-gray-400"
        >
          randomuser@pimjo.com
        </span>
      </div>

      <ul
        class="flex flex-col gap-1 border-b border-gray-200 pb-3 pt-4 ark:border-gray-800"
      >
        <li v-for="item in menuItems" :key="item.href">
          <RouterLink
            :to="item.href"
            class="text-theme-sm group flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 ark:text-gray-400 ark:hover:bg-white/5 ark:hover:text-gray-300"
          >
            <!-- SVG icon would go here -->
            <component
              :is="item.icon"
              class="text-gray-500 group-hover:text-gray-700 ark:group-hover:text-gray-300"
            />
            {{ item.text }}
          </RouterLink>
        </li>
      </ul>
      <RouterLink
        :to="{ name: 'Home' }"
        @click="signOut"
        class="text-theme-sm group mt-3 flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 ark:text-gray-400 ark:hover:bg-white/5 ark:hover:text-gray-300"
      >
        <LogoutIcon
          class="text-gray-500 group-hover:text-gray-700 ark:group-hover:text-gray-300"
        />
        Sign out
      </RouterLink>
    </div>
    <!-- Dropdown End -->
  </div>
</template>
