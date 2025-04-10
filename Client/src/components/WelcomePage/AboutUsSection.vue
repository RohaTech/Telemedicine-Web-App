<script setup>
import about_us_image_group from "/images/HomePage/about_us_image_group.png";
import about_us_star_icon from "/images/HomePage/about_us_star_icon.png";
</script>

<template>
  <div
    class="mx-auto grid w-full max-w-[1300px] grid-cols-2 place-items-center py-24"
  >
    <img :src="about_us_image_group" alt="" class="" />

    <div class="relative space-y-8 text-dark-blue">
      <img :src="about_us_star_icon" alt="" class="absolute right-0 top-0" />

      <div
        class="mt-4 flex w-32 items-center justify-center rounded-md bg-second-accent py-2 uppercase text-white"
      >
        About Us
      </div>
      <div class="text-4xl font-bold capitalize">
        <p class="">Access & Manage your Healthcare</p>
        <p class=" ">From Anywhere</p>
      </div>

      <p class="">
        Tenadam offers secure telemedicine to connect rural Ethiopia with
        doctors, ensuring quality care, easy record access, and timely health
        support.
      </p>

      <div class="flex justify-between gap-x-8">
        <div class="">
          <h1 class="font-bold uppercase">Remote Access</h1>
          <p class="">
            Connect with doctors from anywhere in Ethiopia for timely care.
          </p>
        </div>
        <div class="">
          <h1 class="font-bold uppercase">Cost Effective</h1>
          <p class="">
            Reduce travel expenses with healthcare at your fingertips.
          </p>
        </div>
      </div>

      <RouterLink
        :to="{ name: 'Home' }"
        class="flex w-[158px] items-center justify-around rounded-3xl bg-first-accent py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-second-accent"
      >
        Learn More

        <div
          class="flex size-8 items-center justify-center rounded-full bg-second-accent"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="size-5 fill-white"
            viewBox="0 0 256 256"
          >
            <path
              d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"
            ></path>
          </svg>
        </div>
      </RouterLink>
    </div>
  </div>
</template>
