import { defineStore } from "pinia";

export const useDoctorStore = defineStore("doctorStore", {
  state: () => ({
    doctors: [],
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
    async updateDoctorStatus(status, doctorId) {
      try {
        const response = await fetch(`/api/doctors/update-status/${doctorId}`, {
          method: "PUT",
          headers: {
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
  },
});
