<script setup>
import star_icon from "/images/HomePage/star_icon.png";
import call_to_action_bg_pattern from "/images/HomePage/call_to_action_bg_pattern.png";
import customer_person from "/images/HomePage/customer_person.png";
import doctor_person from "/images/HomePage/doctor_person.png";
</script>

<template>
  <div class="relative mt-32 bg-[#f4f4f4] p-16">
    <img :src="star_icon" :alt="star_icon" class="absolute right-24 top-8" />

    <div class="mt-16">
      <h1
        class="mx-auto max-w-[480px] text-center text-4xl font-bold text-dark-blue"
      >
        Join Tenadam Now
      </h1>

      <div class="mt-16 flex justify-between gap-x-4 text-white">
        <div
          class="flex w-full items-center justify-around bg-[#0e2a46] bg-cover bg-center pt-10"
          :style="{ backgroundImage: `url(${call_to_action_bg_pattern})` }"
        >
          <div class="space-y-6">
            <p class="ml-2">Start Today</p>
            <p class="ml-2 text-lg font-bold">Join Us A Customer</p>

            <RouterLink
              :to="{ name: 'Home' }"
              class="flex w-[158px] items-center justify-around rounded-3xl bg-first-accent py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-second-accent"
            >
              Join Now

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

          <img
            :src="customer_person"
            :alt="customer_person"
            class="w-[300px]"
          />
        </div>
        <div
          class="flex w-full items-center justify-around bg-first-accent bg-cover bg-center pt-10"
          :style="{ backgroundImage: `url(${call_to_action_bg_pattern})` }"
        >
          <div class="space-y-6">
            <p class="ml-2">Start Today</p>
            <p class="ml-2 text-lg font-bold">Join Us A Physician</p>

            <RouterLink
              :to="{ name: 'DoctorRegister' }"
              class="flex w-[158px] items-center justify-around rounded-3xl bg-[#17254E] py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-[#1F3061]"
            >
              Join Now

              <div
                class="flex size-8 items-center justify-center rounded-full bg-[#1F3061]"
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

          <img :src="doctor_person" :alt="doctor_person" class="w-[230px]" />
        </div>
      </div>
    </div>
  </div>
</template>
