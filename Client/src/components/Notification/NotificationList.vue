<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Notification\NotificationList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Notifications List</h1>
      <div v-if="notificationStore.errors.message" class="mb-4 text-red-600">
        {{ notificationStore.errors.message }}
      </div>
      <div v-if="notifications.length === 0" class="text-gray-500">No notifications found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">User</th>
            <th class="p-2 text-left">Message</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="notification in notifications" :key="notification.id" class="border-b">
            <td class="p-2">{{ notification.id }}</td>
            <td class="p-2">{{ notification.user ? notification.user.name : 'N/A' }}</td>
            <td class="p-2">{{ notification.message }}</td>
            <td class="p-2">{{ notification.status }}</td>
            <td class="p-2">
              <router-link
                :to="{ name: 'NotificationView', params: { id: notification.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'NotificationEdit', params: { id: notification.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deleteNotification(notification.id)"
                class="text-red-600 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useNotificationStore } from '@/stores/notificationStore';
import UserLayout from '@/layout/UserLayout.vue';
const notificationStore = useNotificationStore();
const notifications = ref([]);

const fetchNotifications = async () => {
  const result = await notificationStore.getNotifications();
  if (result.success) {
    notifications.value = result.data;
  }
};

const deleteNotification = async (id) => {
  if (confirm('Are you sure you want to delete this notification?')) {
    const result = await notificationStore.deleteNotification(id);
    if (result.success) {
      notifications.value = notifications.value.filter((n) => n.id !== id);
    }
  }
};

onMounted(fetchNotifications);
</script>