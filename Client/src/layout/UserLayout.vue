<script setup>
import UserFooter from '@/components/UserLayout/UserFooter.vue';
import UserHeading from '@/components/UserLayout/UserHeading.vue';


</script>

<template>

<UserHeading/>
  <slot></slot>
<UserFooter/>
</template>