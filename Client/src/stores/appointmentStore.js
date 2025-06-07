// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\appointmentStore.js
import { defineStore } from "pinia";
import router from "@/router";

export const useAppointmentStore = defineStore("appointmentStore", {
  state: () => {
    return {
      appointment: {
        patient_id: "",
        doctor_id: "",
        appointment_date: "",
        time: "",
        status: "pending",
      },
      appointments: [],
      appointmentsByStatus: [], // New property for filtered appointments
      patients: [],
      errors: {},
      isLoading: false,
    };
  },

  actions: {
    async fetchAppointments() {
      try {
        this.isLoading = true;
        const res = await fetch("/api/doctor/appointments", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) {
          throw new Error(
            "Failed to fetch appointments: " + (await res.text()),
          );
        }

        const data = await res.json();
        console.log("Fetched appointments:", data);

        if (data.message === "No appointments found for this doctor") {
          this.appointments = [];
          return;
        }

        this.appointments = data.map((appointment) => {
          const startDateTime = new Date(
            `${appointment.date}T${appointment.time}`,
          );
          const endDateTime = new Date(
            startDateTime.getTime() + 60 * 60 * 1000,
          ); // 1-hour duration
          // Map status to color
          let calendarColor;
          switch (appointment.status) {
            case "pending":
              calendarColor = "Warning";
              break;
            case "confirmed":
              calendarColor = "Success";
              break;
            case "cancelled":
              calendarColor = "Danger";
              break;
            default:
              calendarColor = "Primary";
          }
          return {
            id: appointment.id,
            title: appointment.patientName,
            start: startDateTime.toISOString(),
            end: endDateTime.toISOString(),
            description: appointment.status,
            extendedProps: {
              calendar: calendarColor,
            },
          };
        });
      } catch (err) {
        console.error("Error fetching appointments:", err);
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
      } finally {
        this.isLoading = false;
      }
    },

    async getAppointments() {
      try {
        const res = await fetch("/api/appointments", {
          headers: {
            ...(localStorage.getItem("token") && {
              authorization: `Bearer ${localStorage.getItem("token")}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to fetch appointments",
          };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
        return { success: false };
      }
    },
    async getUserAppointments() {
      try {
        const res = await fetch(`/api/appointments/user`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        });
        const data = await res.json();
        console.log(data);
        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to fetch appointments",
          };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
        return { success: false };
      }
    },

    async fetchPatientsWithAppointments() {
      try {
        const res = await fetch("/api/doctor/patients", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) {
          throw new Error("Failed to fetch patients: " + (await res.text()));
        }

        const data = await res.json();
        console.log("Fetched patients:", data);

        if (data.message === "No patients with prior appointments found") {
          this.patients = [];
          return;
        }

        this.patients = data;
      } catch (err) {
        console.error("Error fetching patients:", err);
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
      }
    },

    async getAppointment(id) {
      try {
        const res = await fetch(`/api/appointments/${id}`, {
          headers: {
            ...(localStorage.getItem("token") && {
              authorization: `Bearer ${localStorage.getItem("token")}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to fetch appointment",
          };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
        return { success: false };
      }
    },

    async createAppointment(appointmentData) {
      try {
        // Fetch the doctor ID dynamically if not provided
        const doctorId =
          appointmentData.doctor_id || (await this.fetchDoctorId());
        if (!doctorId) {
          throw new Error("Unable to determine doctor ID");
        }

        const payload = {
          patient_id: appointmentData.patient_id,
          doctor_id: doctorId,
          appointment_date: appointmentData.date,
          time: appointmentData.time,
          status: appointmentData.status || "pending",
        };

        const res = await fetch("/api/appointments", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
          body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to create appointment",
          };
          return { success: false, message: this.errors.message };
        }

        this.errors = {};
        // Refresh the appointments list after creating a new appointment
        await this.fetchAppointments();
        return { success: true, message: "Appointment created successfully!" };
      } catch (err) {
        console.error("Error creating appointment:", err);
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
        return { success: false, message: this.errors.message };
      }
    },

    async updateAppointment(id, appointmentData) {
      try {
        const res = await fetch(`/api/appointments/${id}`, {
          method: "put",
          headers: {
            "Content-Type": "application/json",
            ...(localStorage.getItem("token") && {
              authorization: `Bearer ${localStorage.getItem("token")}`,
            }),
          },
          body: JSON.stringify(appointmentData),
        });

        const data = await res.json();

        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to update appointment",
          };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: "Appointment updated successfully!" };
      } catch (err) {
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
        return { success: false };
      }
    },

    async fetchDoctorId() {
      try {
        const res = await fetch("/api/user", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) {
          const errorData = await res.json();
          throw new Error(errorData.message || "Failed to fetch doctor ID");
        }

        const data = await res.json();
        if (!data.id) {
          throw new Error("Doctor ID not found in response");
        }

        return data.id; // The /api/user endpoint returns the authenticated user's ID
      } catch (err) {
        console.error("Error fetching doctor ID:", err);
        this.errors = {
          message: err.message || "Failed to fetch doctor ID",
        };
        return null;
      }
    },

    async deleteAppointment(id) {
      try {
        const res = await fetch(`/api/appointments/${id}`, {
          method: "delete",
          headers: {
            ...(localStorage.getItem("token") && {
              authorization: `Bearer ${localStorage.getItem("token")}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to delete appointment",
          };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: "Appointment deleted successfully!" };
      } catch (err) {
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
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

    // New method to get appointments by status
    async getAppointmentsByStatus(status) {
      try {
        this.isLoading = true;
        const res = await fetch(`/api/appointments/status/${status}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        });

        const data = await res.json();

        if (!res.ok || data.errors) {
          this.errors = data.errors || {
            message: "Failed to fetch appointments",
          };
          return { success: false };
        }

        this.appointmentsByStatus = data;
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = {
          message: err.message || "An unexpected error occurred",
        };
        return { success: false };
      } finally {
        this.isLoading = false;
      }
    },

    // Helper methods for specific statuses
    async getPendingAppointments() {
      return await this.getAppointmentsByStatus('pending');
    },

    async getConfirmedAppointments() {
      return await this.getAppointmentsByStatus('confirmed');
    },

    async getCancelledAppointments() {
      return await this.getAppointmentsByStatus('cancelled');
    },

    async getWaitingAppointments() {
      return await this.getAppointmentsByStatus('waiting');
    },
  },
});
