<script setup>
import Hero_Background from "/images/HomePage/Hero_Background.png";
</script>

<template>
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

      
      <RouterLink :to="{name:'Home'}" class="uppercase  flex items-center justify-center bg-second-accent py-3 px-8 w-48 ">
      Get Started
      </RouterLink>
  </div>
</template>