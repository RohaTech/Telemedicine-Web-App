<script setup>
import UserLayout from "@/layout/UserLayout.vue";
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";

onMounted(() => {
  window.scroll(0, 0);
});
</script>

<template>
  <UserLayout>
    <div class="flex justify-center px-4 py-12 sm:px-6 lg:px-8">
      <div
        class="w-full max-w-md border border-gray-50 bg-[#f3f5f5] p-8 drop-shadow-md"
      >
        <h1 class="mb-4 text-2xl font-bold text-[#0F172A]">
          Registration Status
        </h1>

        <div class="space-y-4">
          <div class="rounded-lg bg-yellow-100 p-4 text-yellow-700">
            <p class="font-semibold">Your registration is under review.</p>
            <p class="mt-2 text-sm">
              Our admin team is reviewing your application. This typically takes
              1-3 business days. You’ll receive an email once a decision is
              made.
            </p>
            <p class="mt-2 text-sm">
              Please check your email (including spam/junk) for updates.
            </p>
          </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-6 text-center text-sm text-[#64748B]">
          <p>
            Need help? Contact us at
            <a
              href="mailto:rohatechofficial@gmail.com"
              class="text-[#26C6DA] hover:underline"
              >rohatechofficial@gmail.com</a
            >
          </p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
