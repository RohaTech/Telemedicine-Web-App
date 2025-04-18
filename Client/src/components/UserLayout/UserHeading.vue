<script setup>
import UserMenu from "../AdminLayout/UserMenu.vue";
import Logo from "../UI/Logo.vue";
</script>

<template>
  <div class="border-b py-4">
    <div class="flex w-full items-center justify-between bg-white px-10">
      <Logo class="w-[150px]" />
      <ul class="flex items-center gap-x-12 text-lg">
        <RouterLink
          :to="{ name: 'Home' }"
          class="cursor-pointer hover:font-semibold"
          >Home</RouterLink
        >
        <RouterLink
          :to="{ name: 'Consultations' }"
          class="cursor-pointer hover:font-semibold"
          >Consultations</RouterLink
        >
        <RouterLink
          :to="{ name: 'Appointments' }"
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

      <UserMenu/>
    </div>
  </div>
</template>
