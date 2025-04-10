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
  </UserLayout>
</template>
