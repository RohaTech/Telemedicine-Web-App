import { defineStore } from 'pinia';
import router from '@/router';

export const useLaboratoryStore = defineStore('laboratoryStore', {
  state: () => {
    return {
      errors: {},
      user: null, // Add user state
      token: null, // Add token state
    };
  },

  actions: {

    /**************** Login and Register  ***************/
    async authenticate(apiRoute, formData) {
      try {
        const res = await fetch(`/api/laboratories/${apiRoute}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        });

        const data = await res.json();
        console.log(data);

        if (res.ok) {
          this.errors = {};
          this.user = data.laboratory; // Store laboratory data
          this.token = data.token; // Store token
          localStorage.setItem("token", data.token);
          router.push({ name: "Home" }); // Example route
        } else {
          this.errors = data.errors || { message: data.message || 'Authentication failed' };
          this.user = null;
          this.token = null;
          localStorage.removeItem("token");
        }
      } catch (error) {
        this.errors = { message: error.message || 'An unexpected error occurred' };
        this.user = null;
        this.token = null;
        localStorage.removeItem("token");
      }
    },

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

    async getPendingLaboratories() {
      const res = await fetch("/api/status-pending/laboratories", {
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
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(laboratoryData),
        });

        const data = await res.json();
        console.log(data);
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
    async updateLaboratoryStatus(status, laboratory) {
      try {
        const res = await fetch(`/api/laboratories/update-status/${laboratory}`, {
          method: 'put',
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ status }),
        });

        const data = await res.json();
        console.log(data);
        if (!res.ok) {
          return
        }

        this.errors = {};
        return { success: true, message: ' laboratory the request approved successfully!' };
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
  },
});