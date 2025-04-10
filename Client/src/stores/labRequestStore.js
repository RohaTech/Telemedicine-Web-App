// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\labRequestStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const useLabRequestStore = defineStore('labRequestStore', {
  state: () => {
    return {
      labRequest: {
        consultation_id: "",
        doctor_id: "",
        patient_id: "",
        laboratory_id: "",
        test_type: "",
        status: "pending",
      },
      errors: {},
    };
  },
  
  actions: {
    async getLabRequests() {
      try {
        const res = await fetch('/api/lab-requests', {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch lab requests' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getLabRequest(id) {
      try {
        const res = await fetch(`/api/lab-requests/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch lab request' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createLabRequest() {
      try {
        const res = await fetch('/api/lab-requests', {
          method: 'post',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.labRequest),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create lab request' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'LabRequests' });
        return { success: true, message: 'Lab request created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updateLabRequest(id, labRequestData) {
      try {
        const res = await fetch(`/api/lab-requests/${id}`, {
          method: 'put',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(labRequestData),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update lab request' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Lab request updated successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deleteLabRequest(id) {
      try {
        const res = await fetch(`/api/lab-requests/${id}`, {
          method: 'delete',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete lab request' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Lab request deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.labRequest = {
        consultation_id: "",
        doctor_id: "",
        patient_id: "",
        laboratory_id: "",
        test_type: "",
        status: "pending",
      };
      this.errors = {};
    },
  },
});