<script setup>
import Hero_Background from "/images/HomePage/Hero_Background.png";
</script>

<template>
  <div
    class="h-screen w-full space-y-8 bg-cover bg-center p-48 text-white"
    :style="{ backgroundImage: `url(${Hero_Background})` }"
  >
    <div class="">
      <p class="ml-3 text-xl text-yellow-300">Welcome to Tenadam</p>
      <h1 class="text-7xl font-bold">ACCESS YOUR HEALTHCARE</h1>
      <h1 class="text-7xl font-bold">Anytime, Anywhere</h1>
    </div>

    <p class="max-w-[480px]">
      We are experienced in educational platform and skilled strategies for the
      success of our online learning.
    </p>

    <RouterLink
      :to="{ name: 'GetStarted' }"
      class="flex w-48 items-center justify-center bg-second-accent px-8 py-3 uppercase"
    >
      Get Started
    </RouterLink>
  </div>
</template>
