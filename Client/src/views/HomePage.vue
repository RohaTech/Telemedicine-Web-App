<script setup>
import CategoriesSection from "@/components/HomePage/CategoriesSection.vue";
import ExploreFeatured from "@/components/HomePage/ExploreFeatured.vue";
import RecentlyVisited from "@/components/HomePage/RecentlyVisited.vue";
import HeroSection from "@/components/WelcomePage/HeroSection.vue";
import Hero_Background from "/images/HomePage/Hero_Background.png";
import UserLayout from "@/layout/UserLayout.vue";
</script>

<template>
  <UserLayout>
    <div
    class="w-full h-screen bg-cover bg-center text-white p-48 space-y-8 "
    :style="{ backgroundImage: `url(${Hero_Background})` }"
  >

    <div class="">
    <p class="text-yellow-300 text-xl ml-3">Welcome to Tenadam</p>
    <h1 class="font-bold text-7xl">ACCESS YOUR HEALTHCARE</h1>
    <h1 class="font-bold text-7xl">Anytime, Anywhere</h1>
    </div>

    <p class="  max-w-[480px]">We are experienced in educational platform and skilled strategies
      for the success of our online learning.</p>

      
  </div>
    <CategoriesSection />
    <RecentlyVisited />
    <ExploreFeatured />
  </UserLayout>
</template>
