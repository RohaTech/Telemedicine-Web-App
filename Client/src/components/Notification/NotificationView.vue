<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Notification\NotificationView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Notification Details</h1>
      <div v-if="notificationStore.errors.message" class="mb-4 text-red-600">
        {{ notificationStore.errors.message }}
      </div>
      <div v-if="notification">
        <div class="mb-4"><strong>ID:</strong> {{ notification.id }}</div>
        <div class="mb-4"><strong>User:</strong> {{ notification.user ? notification.user.name : 'N/A' }}</div>
        <div class="mb-4"><strong>Message:</strong> {{ notification.message }}</div>
        <div class="mb-4"><strong>Status:</strong> {{ notification.status }}</div>
        <router-link
          to="/notifications"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading notification...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useNotificationStore } from '@/stores/notificationStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const notificationStore = useNotificationStore();
const notification = ref(null);

const fetchNotification = async () => {
  const result = await notificationStore.getNotification(route.params.id);
  if (result.success) {
    notification.value = result.data;
  }
};

onMounted(fetchNotification);
</script>