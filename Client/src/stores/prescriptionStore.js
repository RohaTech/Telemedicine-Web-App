// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\prescriptionStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const usePrescriptionStore = defineStore('prescriptionStore', {
  state: () => {
    return {
      prescription: {
        doctor_id: null,
        patient_id: null,
        consultation_id: null,
        medicine_name: "",
        directions: "",
      },
      errors: {},
    };
  },

  actions: {
    async getPrescriptions() {
      try {
        const res = await fetch('/api/prescriptions', {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch prescriptions' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getPrescription(id) {
      try {
        const res = await fetch(`/api/prescriptions/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch prescription' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createPrescription() {
      try {
        const res = await fetch('/api/prescriptions', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.prescription),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create prescription' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'Prescriptions' });
        return { success: true, message: 'Prescription created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updatePrescription(id) {
      try {
        const res = await fetch(`/api/prescriptions/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.prescription),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update prescription' };
          return { success: false };
        }

        this.errors = {};
        router.push({ name: 'Prescriptions' });
        return { success: true, message: 'Prescription updated successfully!', data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deletePrescription(id) {
      try {
        const res = await fetch(`/api/prescriptions/${id}`, {
          method: 'DELETE',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete prescription' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Prescription deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.prescription = {
        doctor_id: "",
        patient_id: "",
        consultation_id: "",
        medicine_name: "",
        directions: "",
      };
      this.errors = {};
    },
  },
});