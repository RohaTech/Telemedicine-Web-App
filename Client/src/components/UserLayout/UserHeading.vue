<script setup>
import Logo from "../UI/Logo.vue";
</script>

<template>
  <div class="py-4 border-b">
    <div class="w-full px-10 items-center bg-white flex justify-between">
      <Logo class="w-[150px]" />
      <ul class="flex gap-x-12 items-center text-lg">
        <li class="cursor-pointer hover:font-semibold">Home</li>
        <li class="cursor-pointer hover:font-semibold">About Us</li>
        <li class="cursor-pointer hover:font-semibold">Categories</li>
        <li class="cursor-pointer hover:font-semibold">Reservation</li>
        <li class="cursor-pointer hover:font-semibold">Contact</li>
      </ul>

      <div class="flex gap-x-8 items-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="size-8 cursor-pointer"
          viewBox="0 0 256 256"
        >
          <path
            d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"
          ></path>
        </svg>

        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="size-8 cursor-pointer"
          viewBox="0 0 256 256"
        >
          <path
            d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24ZM74.08,197.5a64,64,0,0,1,107.84,0,87.83,87.83,0,0,1-107.84,0ZM96,120a32,32,0,1,1,32,32A32,32,0,0,1,96,120Zm97.76,66.41a79.66,79.66,0,0,0-36.06-28.75,48,48,0,1,0-59.4,0,79.66,79.66,0,0,0-36.06,28.75,88,88,0,1,1,131.52,0Z"
          ></path>
        </svg>
      </div>
    </div>
  </div>
</template>
