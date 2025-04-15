import { defineStore } from 'pinia';
import router from '@/router';

export const usePatientStore = defineStore('patientStore', {
  state: () => {
    return {
      errors: {},
    };
  },

  actions: {
    async getAllPatients() {
      const res = await fetch("/api/users/patient", {
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




  },
});