// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\laboratoryStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const useLaboratoryStore = defineStore('laboratoryStore', {
  state: () => {
    return {
      laboratory: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "", 
        phone: "",
        address: "",
      },
      errors: {},
    };
  },
  
  actions: {
    async getLaboratories() {
      try {
        const res = await fetch('/api/laboratories', {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch laboratories' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getLaboratory(id) {
      try {
        const res = await fetch(`/api/laboratories/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch laboratory' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createLaboratory() {
        if (this.laboratory.password !== this.laboratory.password_confirmation) {
            this.errors = { password_confirmation: 'Passwords do not match' };
            return { success: false };
          }
        try {
        const res = await fetch('/api/laboratories/register', {
          method: 'post',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.laboratory),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create laboratory' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'Laboratories' });
        return { success: true, message: 'Laboratory created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updateLaboratory(id, laboratoryData) {
      try {
        const res = await fetch(`/api/laboratories/${id}`, {
          method: 'put',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(laboratoryData),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update laboratory' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Laboratory updated successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deleteLaboratory(id) {
      try {
        const res = await fetch(`/api/laboratories/${id}`, {
          method: 'delete',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete laboratory' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Laboratory deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.laboratory = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "", // Reset confirmation too
        phone: "",
        address: "",
      };
      this.errors = {};
    },
  },
});