// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\appointmentStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const useAppointmentStore = defineStore('appointmentStore', {
  state: () => {
    return {
      appointment: {
        patient_id: "",
        doctor_id: "",
        appointment_date: "",
        status: "pending",
      },
      errors: {},
    };
  },
  
  actions: {    
    async getAppointments() {
    try {
      const res = await fetch('/api/appointments', {
        headers: {
          ...(localStorage.getItem('token') && {
            authorization: `Bearer ${localStorage.getItem('token')}`,
          }),
        },
      });
      const data = await res.json();
      if (!res.ok || data.errors) {
        this.errors = data.errors || { message: 'Failed to fetch appointments' };
        return { success: false };
      }
      this.errors = {};
      return { success: true, data };
    } catch (err) {
      this.errors = { message: err.message || 'An unexpected error occurred' };
      return { success: false };
    }
  },

  async getAppointment(id) {
    try {
      const res = await fetch(`/api/appointments/${id}`, {
        headers: {
          ...(localStorage.getItem('token') && {
            authorization: `Bearer ${localStorage.getItem('token')}`,
          }),
        },
      });
      const data = await res.json();
      if (!res.ok || data.errors) {
        this.errors = data.errors || { message: 'Failed to fetch appointment' };
        return { success: false };
      }
      this.errors = {};
      return { success: true, data };
    } catch (err) {
      this.errors = { message: err.message || 'An unexpected error occurred' };
      return { success: false };
    }
  },

    async createAppointment() {
      const res = await fetch('/api/appointments', {
        method: 'post',
        headers: {
          'Content-Type': 'application/json',
          ...(localStorage.getItem('token') && {
            authorization: `Bearer ${localStorage.getItem('token')}`,
          }),
        },
        body: JSON.stringify(this.appointment),
      });

      const data = await res.json();

      if (!res.ok || data.errors) {
        this.errors = data.errors || { message: 'Failed to create appointment' };
      } else {
        this.errors = {};
        router.push({ name: 'Appointments' }); // Assuming you have a route named 'Appointments'
        this.resetForm();
        return { success: true, message: 'Appointment created successfully!' };
      }
    },
async updateAppointment(id, appointmentData) {
      try {
        const res = await fetch(`/api/appointments/${id}`, {
          method: 'put',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(appointmentData),
        });

        const data = await res.json();

        if (!res.ok || data.errors) {
          this.errors = data.errors || { message: 'Failed to update appointment' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Appointment updated successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deleteAppointment(id) {
      try {
        const res = await fetch(`/api/appointments/${id}`, {
          method: 'delete',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok || data.errors) {
          this.errors = data.errors || { message: 'Failed to delete appointment' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Appointment deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },


    resetForm() {
      this.appointment = {
        patient_id: "",
        doctor_id: "",
        appointment_date: "",
        status: "pending",
      };
      this.errors = {};
    },
  },
});