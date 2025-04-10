// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\notificationStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const useNotificationStore = defineStore('notificationStore', {
  state: () => {
    return {
      notification: {
        user_id: "",
        message: "",
        status: "unread", // Default status
      },
      errors: {},
    };
  },

  actions: {
    async getNotifications() {
      try {
        const res = await fetch('/api/notifications', {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch notifications' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getNotification(id) {
      try {
        const res = await fetch(`/api/notifications/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch notification' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createNotification() {
      try {
        const res = await fetch('/api/notifications', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.notification),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create notification' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'Notifications' });
        return { success: true, message: 'Notification created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updateNotification(id) {
      try {
        const res = await fetch(`/api/notifications/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.notification),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update notification' };
          return { success: false };
        }

        this.errors = {};
        router.push({ name: 'Notifications' });
        return { success: true, message: 'Notification updated successfully!', data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deleteNotification(id) {
      try {
        const res = await fetch(`/api/notifications/${id}`, {
          method: 'DELETE',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete notification' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Notification deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.notification = {
        user_id: "",
        message: "",
        status: "unread",
      };
      this.errors = {};
    },
  },
});