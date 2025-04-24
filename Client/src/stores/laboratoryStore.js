import { defineStore } from 'pinia';
import router from '@/router';

export const useLaboratoryStore = defineStore('laboratoryStore', {
  state: () => {
    return {
      errors: {},
    };
  },

  actions: {

    async getLaboratories() {
      const res = await fetch("/api/laboratories", {
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        return data;
      }
    },

    async getLaboratory(id) {

      const res = await fetch(`/api/laboratories/${id}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        return data;
      }
    },


    async createLaboratory(formData) {
      const res = await fetch("/api/laboratories", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          'Content-Type': 'application/json',

        },
        body: JSON.stringify(formData),
      });
      const data = await res.json();
      console.log(data);
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        router.push({ name: "LaboratoryStatusPage" });
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