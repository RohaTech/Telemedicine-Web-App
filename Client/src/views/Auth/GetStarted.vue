<script setup>
import star_icon from "/images/HomePage/star_icon.png";
import call_to_action_bg_pattern from "/images/HomePage/call_to_action_bg_pattern.png";
import customer_person from "/images/HomePage/customer_person.png";
import doctor_person from "/images/HomePage/doctor_person.png";
import UserLayout from "@/layout/UserLayout.vue";
import { onMounted } from "vue";

onMounted(() => {
  window.scroll({ top: 0, left: 0 });
});
</script>

<template>
  <UserLayout>
    <div class="relative p-16">
      <img :src="star_icon" :alt="star_icon" class="absolute right-24 top-8" />

      <div class="mt-16">
        <div class="flex flex-col items-center">
          <h1
            class="mx-auto max-w-[480px] text-center text-4xl font-bold text-dark-blue"
          >
            Join Tenadam Now
          </h1>
          <RouterLink
            :to="{ name: 'Login' }"
            class="mx-auto mt-2 text-center text-gray-400 hover:text-black"
          >
            Already register? sign in
          </RouterLink>
        </div>

        <div class="mt-16 flex justify-between gap-x-4 text-white">
          <div
            class="flex w-[720px] items-center justify-around bg-[#0e2a46] bg-cover bg-center pt-10"
            :style="{ backgroundImage: `url(${call_to_action_bg_pattern})` }"
          >
            <div class="space-y-6">
              <p class="ml-2">Start Today</p>
              <p class="ml-2 text-lg font-bold">Join Us A Patient</p>

              <RouterLink
                :to="{ name: 'PatientRegister' }"
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
            class="flex w-[720px] items-center justify-around bg-first-accent bg-cover bg-center pt-10"
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
        <div
          class="mx-auto mt-8 flex h-[340px] w-[760px] items-center justify-around bg-blue-500 bg-cover bg-center pt-10 text-white"
          :style="{ backgroundImage: `url(${call_to_action_bg_pattern})` }"
        >
          <div class="space-y-6">
            <p class="ml-2">Start Today</p>
            <p class="ml-2 text-lg font-bold">Register a Laboratory</p>

            <RouterLink
              :to="{ name: 'LaboratoryRegister' }"
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

          <svg
            xmlns="http://www.w3.org/2000/svg"
            id="male-scientist"
            viewBox="0 0 48 48"
            class="size-[250px] self-end"
          >
            <path
              d="M17.271,25.41l-8.122,2.742c-2.201.743-3.775,2.69-4.041,4.998l-1.081,9.392c-.273,2.375,1.583,4.458,3.974,4.458h27.999c2.39,0,4.247-2.083,3.974-4.458l-1.081-9.392c-.266-2.308-1.84-4.255-4.041-4.998l-8.122-2.742h-9.458Z"
              style="fill: #eff3f9"
            ></path>
            <path
              d="M36,47h-4.5c-1.63,0-2.57-1.84-1.62-3.17l3.25-4.52c.24-.34.37-.75.37-1.17v-3.19c-.43-.11-.75-.49-.75-.95,0-.55.45-1,1-1h3.5c.55,0,1,.45,1,1,0,.46-.32.84-.75.95v3.19c0,.42.13.83.37,1.17l2.06,2.86.04.37c.28,2.38-1.58,4.46-3.97,4.46Z"
              style="fill: #cfe0f3"
            ></path>
            <path
              d="M39.93 43.71c-.32 1.85-1.94 3.29-3.93 3.29H8c-1.99 0-3.61-1.44-3.93-3.29.72.79 1.76 1.29 2.93 1.29h30c1.17 0 2.21-.5 2.93-1.29ZM25.31 25.41c-1.46 1.88-3.74 3.09-6.31 3.09-1.99 0-3.8-.73-5.2-1.92l3.47-1.17h8.04Z"
              style="fill: #cfe0f3"
            ></path>
            <path
              d="M39.93,43.71c-.32,1.85-1.94,3.29-3.93,3.29h-4.5c-1.18,0-2-.97-2-2h7.5c1.17,0,2.21-.5,2.93-1.29Z"
              style="fill: #b6cae5"
            ></path>
            <rect
              width="2"
              height="21.59"
              x="21"
              y="25.41"
              style="fill: #cfe0f3"
            ></rect>
            <rect
              width="2"
              height="2"
              x="21"
              y="45"
              style="fill: #b6cae5"
            ></rect>
            <path
              d="M25 25c3.137 0 5.044 1.845 5.721 2.642.167.197.149.488-.033.671l-1.334 1.334c-.195.195-.195.512 0 .707l1.441 1.441c.131.131.181.319.126.497-.35 1.137-2.064 5.423-8.921 9.708l3-17ZM19 25c-3.137 0-5.044 1.845-5.721 2.642-.167.197-.149.488.033.671l1.334 1.334c.195.195.195.512 0 .707l-1.441 1.441c-.131.131-.181.319-.126.497.35 1.137 2.064 5.423 8.921 9.708l-3-17Z"
              style="fill: #cfe0f3"
            ></path>
            <path
              d="M19.61,28.48c-.2.01-.4.02-.61.02-1.85,0-3.55-.63-4.9-1.68.98-.83,2.61-1.82,4.9-1.82l.61,3.48Z"
              style="fill: #b6cae5"
            ></path>
            <path
              d="M21.314,41.684c.273.556,1.059.558,1.34.005,1.46-2.872,3.345-6.565,3.345-12.078v-6.611s-8,0-8,0v6.611c0,4.707,2.171,9.746,3.314,12.073Z"
              style="fill: #9eb6d8"
            ></path>
            <path
              d="M26,23v6.61c0,1.21-.09,2.34-.26,3.39-.8-1.21-2.18-2-3.74-2-1.52,0-2.87.75-3.68,1.91-.2-1.09-.32-2.21-.32-3.3v-6.61h8Z"
              style="fill: #b6cae5"
            ></path>
            <path
              d="M22,31h0c-2.209,0-4-1.791-4-4v-4h8v4c0,2.209-1.791,4-4,4Z"
              style="fill: #ffcdb4"
            ></path>
            <path
              d="M26,23v1.36c-1.35,2.47-3.98,4.14-7,4.14-.24,0-.48-.01-.71-.03-.19-.45-.29-.95-.29-1.47v-4h8Z"
              style="fill: #f6a28d"
            ></path>
            <path
              d="M13.387,17.618l-1.689-6.733c-.35-1.41-.234-2.903.342-4.246l.284-.659c.843-1.952,2.561-3.395,4.638-3.871l4.096-.951c.475-.108.951-.158,1.435-.158,1.668,0,3.295.651,4.513,1.869,2.027,0,3.854,1.21,4.655,3.07l.3.701c.576,1.343.692,2.836.342,4.246l-1.654,6.592-17.262.141Z"
              style="fill: #333e54"
            ></path>
            <path
              d="M15,8c0-2.29.862-4.374,2.271-5.962l-.31.072c-2.077.475-3.796,1.919-4.638,3.871l-.284.659c-.576,1.343-.692,2.836-.342,4.246l1.689,6.733,2.033-.017c-.221-3.248-.42-6.923-.42-9.602Z"
              style="fill: #263244"
            ></path>
            <path
              d="M31.5,17c0-1.341-1.059-2.427-2.385-2.488-.136-1.15-.276-2.108-.395-2.841-.157-.967-.993-1.67-1.973-1.67h-9.493c-.979,0-1.816.703-1.973,1.67-.119.733-.259,1.692-.395,2.841-1.326.061-2.385,1.147-2.385,2.488,0,1.219.874,2.232,2.029,2.453.344,3.885,4.492,6.547,7.471,6.547s7.127-2.662,7.471-6.547c1.155-.221,2.029-1.233,2.029-2.453Z"
              style="fill: #ffcdb4"
            ></path>
            <path
              d="M17.225,20.455c-.146-2.783.092-7.141.332-10.455h-.303c-.979,0-1.816.703-1.973,1.67-.119.733-.259,1.692-.395,2.841-1.326.061-2.385,1.147-2.385,2.488,0,1.219.874,2.232,2.029,2.453.269,3.044,2.873,5.33,5.421,6.18-1.563-1.204-2.614-3.053-2.725-5.178Z"
              style="fill: #f6a28d"
            ></path>
            <path
              d="M43.621,43.834l-3.247-4.524c-.244-.34-.375-.748-.375-1.166v-3.194c.428-.113.75-.486.75-.95,0-.552-.448-1-1-1h-3.5c-.552,0-1,.448-1,1,0,.464.322.836.75.95v3.194c0,.418-.131.826-.375,1.166l-3.246,4.524c-.95,1.323-.004,3.166,1.625,3.166h7.993c1.629,0,2.574-1.843,1.625-3.166Z"
              style="fill: #eff3f9"
            ></path>
            <path
              d="M43.62,43.83l-3.25-4.52c-.24-.34-.37-.75-.37-1.17v-3.191c.427-.114.75-.488.75-.949,0-.55-.45-1-1-1h-1.25c.3.23.5.59.5,1s-.2.77-.5,1v3.64c0,.57.12,1.13.35,1.65l1.492,3.298c.3.662-.185,1.412-.911,1.412h-7.431c0,1.03.82,2,2,2h8c1.63,0,2.57-1.84,1.62-3.17Z"
              style="fill: #cfe0f3"
            ></path>
            <path
              d="M42,47h-8c-1.63,0-2.57-1.84-1.62-3.17l2.03-2.83h7.18l2.03,2.83c.95,1.33.01,3.17-1.62,3.17Z"
              style="fill: #6ee7ff"
            ></path>
            <path
              d="M42,47h-8c-1.18,0-2-.97-2-2h7.43c.727,0,1.211-.75.911-1.412l-1.171-2.588h2.42l2.03,2.83c.95,1.33.01,3.17-1.62,3.17Z"
              style="fill: #47caf0"
            ></path>
          </svg>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
