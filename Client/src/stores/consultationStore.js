// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\consultationStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const useConsultationStore = defineStore('consultationStore', {
  state: () => {
    return {
      consultation: {
        patient_id: "",
        doctor_id: "",
        prescription_id: "",
        consultation_date: "",
        notes: "",
      },
      errors: {},
    };
  },
  
  actions: {
    async getConsultations() {
      try {
        const res = await fetch('/api/consultations', {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch consultations' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getConsultation(id) {
      try {
        const res = await fetch(`/api/consultations/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch consultation' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createConsultation() {
      try {
        const res = await fetch('/api/consultations', {
          method: 'post',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.consultation),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create consultation' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'Consultations' });
        return { success: true, message: 'Consultation created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updateConsultation(id, consultationData) {
      try {
        const res = await fetch(`/api/consultations/${id}`, {
          method: 'put',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(consultationData),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update consultation' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Consultation updated successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deleteConsultation(id) {
      try {
        const res = await fetch(`/api/consultations/${id}`, {
          method: 'delete',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete consultation' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Consultation deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.consultation = {
        patient_id: "",
        doctor_id: "",
        prescription_id: "",
        consultation_date: "",
        notes: "",
      };
      this.errors = {};
    },
  },
});