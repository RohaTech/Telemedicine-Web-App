<script setup>
import CategoriesSection from "@/components/HomePage/CategoriesSection.vue";
import ExploreFeatured from "@/components/HomePage/ExploreFeatured.vue";
import RecentlyVisited from "@/components/HomePage/RecentlyVisited.vue";
import HeroSection from "@/components/WelcomePage/HeroSection.vue";

import UserLayout from "@/layout/UserLayout.vue";
</script>

<template>
  <UserLayout>
    <HeroSection />
    <CategoriesSection />
    <RecentlyVisited />
    <ExploreFeatured />
  </UserLayout>
</template>
