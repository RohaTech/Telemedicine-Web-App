<script setup>
import AppSidebar from "@/components/AdminLayout/AppSidebar.vue";
import AppHeader from "@/components/AdminLayout/AppHeader.vue";
import { useSidebar } from "@/composables/useSidebar";
// import Backdrop from "./Backdrop.vue";
const { isExpanded, isHovered } = useSidebar();
</script>
<template>
  <div class="min-h-screen xl:flex">
    <app-sidebar />
    <!-- <Backdrop /> -->
    <div
      class="flex-1 transition-all duration-300 ease-in-out"
      :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']"
    >
      <app-header />
      <div class="max-w-(--breakpoint-2xl) mx-auto p-4 md:p-6">
        <slot></slot>
      </div>
    </div>
  </div>
</template>
