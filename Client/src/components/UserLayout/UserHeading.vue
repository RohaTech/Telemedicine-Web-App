<script setup>
import { ref, onMounted } from "vue";
import UserMenu from "../AdminLayout/UserMenu.vue";
import Logo from "../UI/Logo.vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
</script>

<template>
  <div class="border-b py-4">
    <div class="flex w-full items-center justify-between bg-white px-10">
      <Logo class="w-[160px] fill-first-accent" />
      <ul class="flex items-center gap-x-12 text-lg">
        <RouterLink
          v-if="authStore.user"
          :to="{ name: 'Home' }"
          class="cursor-pointer hover:font-semibold"
          >Home</RouterLink
        >
        <RouterLink
          v-else
          :to="{ name: 'Welcome' }"
          class="cursor-pointer hover:font-semibold"
          >Home</RouterLink
        >
        <RouterLink
          :to="{ name: 'Consultations' }"
          class="cursor-pointer hover:font-semibold"
          >Consultations</RouterLink
        >
        <RouterLink
          :to="{ name: 'UserAppointment' }"
          class="cursor-pointer hover:font-semibold"
          >Appointments</RouterLink
        >
        <RouterLink
          :to="{ name: 'Welcome' }"
          class="cursor-pointer hover:font-semibold"
          >Reservation</RouterLink
        >
        <RouterLink
          :to="{ name: 'Welcome' }"
          class="cursor-pointer hover:font-semibold"
          >Contact</RouterLink
        >
      </ul>

      <div class="" v-if="authStore.user">
        <UserMenu />
      </div>

      <RouterLink
        v-else
        :to="{ name: 'Login' }"
        class="cursor-pointer rounded-md bg-second-accent px-6 py-2 text-white"
      >
        Sign in</RouterLink
      >
    </div>
  </div>
</template>
