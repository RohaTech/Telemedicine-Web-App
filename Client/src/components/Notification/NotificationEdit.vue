<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Notification\NotificationEdit.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Edit Notification</h1>
      <form @submit.prevent="handleSubmit" v-if="notification">
        <div class="mb-4">
          <label for="user_id" class="block mb-1 font-semibold">User ID:</label>
          <input
            type="number"
            id="user_id"
            v-model="notification.user_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="notificationStore.errors.user_id" class="text-red-600 text-sm mt-1">
            {{ notificationStore.errors.user_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="message" class="block mb-1 font-semibold">Message:</label>
          <input
            type="text"
            id="message"
            v-model="notification.message"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="notificationStore.errors.message" class="text-red-600 text-sm mt-1">
            {{ notificationStore.errors.message }}
          </p>
        </div>
        <div class="mb-4">
          <label for="status" class="block mb-1 font-semibold">Status:</label>
          <select
            id="status"
            v-model="notification.status"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          >
            <option value="unread">Unread</option>
            <option value="read">Read</option>
          </select>
          <p v-if="notificationStore.errors.status" class="text-red-600 text-sm mt-1">
            {{ notificationStore.errors.status }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors mr-2"
        >
          Update Notification
        </button>
        <router-link
          to="/notifications"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Cancel
        </router-link>
      </form>
      <div v-if="notificationStore.errors.message" class="mt-4 text-red-600">
        {{ notificationStore.errors.message }}
      </div>
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
    notification.value = { ...result.data };
    notificationStore.notification = { ...result.data };
  }
};

const handleSubmit = async () => {
  notificationStore.notification.user_id = Number(notification.value.user_id);
  notificationStore.notification.message = notification.value.message;
  notificationStore.notification.status = notification.value.status;

  const result = await notificationStore.updateNotification(route.params.id);
  if (result.success) {
    alert(result.message);
    notification.value = { ...result.data }; // Update local state
  }
};

onMounted(fetchNotification);
</script>