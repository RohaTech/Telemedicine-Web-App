// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\paymentStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const usePaymentStore = defineStore('paymentStore', {
  state: () => {
    return {
      payment: {
        patient_id: "",
        doctor_id: "",
        amount: "",
        payment_method: "cash",
        status: "pending",
        transaction_id: "",
      },
      errors: {},
    };
  },

  actions: {
    async getPayments() {
      try {
        const res = await fetch('/api/payments', {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch payments' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getPayment(id) {
      try {
        const res = await fetch(`/api/payments/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch payment' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createPayment() {
      try {
        const res = await fetch('/api/payments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.payment),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create payment' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'Payments' });
        return { success: true, message: 'Payment created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updatePayment(id) {
      try {
        const res = await fetch(`/api/payments/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(this.payment),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update payment' };
          return { success: false };
        }

        this.errors = {};
        router.push({ name: 'Payments' });
        return { success: true, message: 'Payment updated successfully!', data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deletePayment(id) {
      try {
        const res = await fetch(`/api/payments/${id}`, {
          method: 'DELETE',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete payment' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Payment deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.payment = {
        patient_id: "",
        doctor_id: "",
        amount: "",
        payment_method: "cash",
        status: "pending",
        transaction_id: "",
      };
      this.errors = {};
    },
  },
});