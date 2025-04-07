<script setup>
import UserFooter from "@/components/UserLayout/UserFooter.vue";
import UserHeading from "@/components/UserLayout/UserHeading.vue";
</script>

<template>
  <UserHeading />
  <div class="min-h-[calc(100vh-200px)]"><slot></slot></div>
  <UserFooter />
</template>
