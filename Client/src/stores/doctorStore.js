// src/stores/doctor.js
import { defineStore } from "pinia";
import { ref } from "vue";

export const useDoctorStore = defineStore("doctorStore", {
  state: () => ({
    doctors: [],
    status: null,
    errors: null,
    dashboardData: null,
  }),
  actions: {
    async getDoctors() {
      try {
        const response = await fetch("/api/doctors", {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        this.doctors = data;
        return this.doctors;
      } catch (error) {
        console.error("Error fetching doctors:", error);
        return [];
      }
    },
    async getDoctor(id) {
      try {
        const response = await fetch(`/api/doctors/${id}`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        this.doctors = data;
        return this.doctors;
      } catch (error) {
        console.error("Error fetching doctors:", error);
        return [];
      }
    },

    async getActiveDoctors() {
      try {
        const response = await fetch("/api/doctors/status-active", {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        this.doctors = data;
        return this.doctors;
      } catch (error) {
        console.error("Error fetching active doctors:", error);
        return [];
      }
    },
    async getDoctorsByCategory(category) {

      const response = await fetch(`/api/doctors/categories/${category}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });
      const data = await response.json();
      console.log(data);
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        return data;
      }

    },

    async updateDoctorStatus(status, doctorId) {
      try {
        const response = await fetch(`/api/doctors/update-status/${doctorId}`, {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ status }),
        });

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
      } catch (error) {
        console.error("Error updating doctor status:", error);
      }
    },

    async fetchStatus() {
      try {
        this.errors = null;
        const response = await fetch("/api/doctor-status", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        });
        const data = await response.json();
        if (response.ok) {
          this.status = data.status;
        } else {
          this.errors = data.errors || { general: "Failed to fetch status" };
        }
      } catch (error) {
        console.error("Error fetching status:", error);
        this.errors = {
          general:
            "An error occurred while fetching your status. Please try again.",
        };
      }
    },

    async fetchDashboardData() {
      try {
        this.errors = null;
        const response = await fetch("/api/doctor-dashboard", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        });
        const data = await response.json();
        if (response.ok) {
          this.dashboardData = data;
        } else {
          this.errors = data.errors || {
            general: "Failed to fetch dashboard data",
          };
        }
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
        this.errors = {
          general:
            "An error occurred while fetching your dashboard. Please try again.",
        };
      }
    },

    clearStatus() {
      this.status = null;
      this.errors = null;
      this.dashboardData = null;
    },
  },
});
