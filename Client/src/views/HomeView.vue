<script setup>
import AboutUsSection from "@/components/HomeView/AboutUsSection.vue";
import CallToActionSection from "@/components/HomeView/CallToActionSection.vue";
import ExploreSection from "@/components/HomeView/ExploreSection.vue";
import HeroSection from "@/components/HomeView/HeroSection.vue";
import UserLayout from "@/layout/UserLayout.vue";
</script>

<template>
  <UserLayout>
    <HeroSection />
    <AboutUsSection />
    <ExploreSection />
    <CallToActionSection />

    <!-- <RouterLink
              :to="{ name: 'Home' }"
              class="flex w-[158px] items-center justify-around rounded-3xl bg-[#17254e] py-1.5 text-sm uppercase text-white transition-all duration-300 ease-linear hover:bg-second-accent"
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
            </RouterLink> -->
  </UserLayout>
</template>
