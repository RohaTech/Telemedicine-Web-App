<script setup>
import AboutUsSection from "@/components/WelcomePage/AboutUsSection.vue";
import CallToActionSection from "@/components/WelcomePage/CallToActionSection.vue";
import ExploreSection from "@/components/WelcomePage/ExploreSection.vue";
import HeroSection from "@/components/WelcomePage/HeroSection.vue";
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
