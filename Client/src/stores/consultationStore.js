import { defineStore } from "pinia";
import { useAppointmentStore } from "./appointmentStore";

export const useConsultationStore = defineStore("consultation", {
  state: () => ({
    appointment: null,
    patient: null,
    consultation: null,
    chatMessages: [],
    isLoading: false,
    errorMessage: "",
    isSavingNotes: false,
    isSendingMessage: false,
  }),

  actions: {
    async fetchConsultationData(appointmentId) {
      this.isLoading = true;
      this.errorMessage = "";
      try {
        // Fetch appointment via appointmentStore
        const appointmentStore = useAppointmentStore();
        const appointmentResponse =
          await appointmentStore.getAppointment(appointmentId);
        if (!appointmentResponse.success) {
          throw new Error(
            appointmentResponse.message || "Failed to fetch appointment",
          );
        }
        this.appointment = appointmentResponse.data;

        // Fetch patient
        const patientResponse = await fetch(
          `/api/users/${this.appointment.patient_id}`,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "application/json",
            },
          },
        );
        if (!patientResponse.ok) {
          throw new Error("Failed to fetch patient data");
        }
        this.patient = await patientResponse.json();

        // Fetch or create consultation
        const consultationResponse = await fetch(
          `/api/consultations?appointment_id=${appointmentId}`,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "application/json",
            },
          },
        );
        if (consultationResponse.ok) {
          this.consultation = await consultationResponse.json();
        } else if (consultationResponse.status === 404) {
          const createResponse = await fetch("/api/consultations", {
            method: "POST",
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              appointment_id: appointmentId,
              patient_id: this.appointment.patient_id,
              doctor_id: this.appointment.doctor_id,
              consultation_date: new Date().toISOString(),
              notes: "",
            }),
          });
          if (!createResponse.ok) {
            throw new Error("Failed to create consultation");
          }
          this.consultation = await createResponse.json();
        }

        // Fetch chat messages
        await this.fetchChatMessages();
      } catch (error) {
        this.errorMessage =
          error.message || "An error occurred while loading consultation data";
      } finally {
        this.isLoading = false;
      }
    },

    async fetchChatMessages() {
      if (!this.consultation?.id) return;
      try {
        const response = await fetch(
          `/api/chats/consultation/${this.consultation.id}`,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "application/json",
            },
          },
        );
        if (!response.ok) {
          throw new Error("Failed to fetch chat messages");
        }
        this.chatMessages = await response.json();
      } catch (error) {
        console.error("Error fetching chat messages:", error);
        this.errorMessage = error.message || "Failed to fetch chat messages";
      }
    },

    async saveNotes(notes) {
      if (!this.consultation?.id) return;
      this.isSavingNotes = true;
      try {
        const response = await fetch(
          `/api/consultations/${this.consultation.id}`,
          {
            method: "PUT",
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ notes }),
          },
        );
        if (!response.ok) {
          throw new Error("Failed to save notes");
        }
        this.consultation.notes = notes;
      } catch (error) {
        this.errorMessage = "Failed to save notes. Please try again.";
      } finally {
        this.isSavingNotes = false;
      }
    },

    async sendMessage(text) {
      if (!this.consultation?.id || !text.trim()) return;
      this.isSendingMessage = true;
      try {
        const response = await fetch("/api/chats", {
          method: "POST",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            consultation_id: this.consultation.id,
            sender: "Doctor",
            text,
            timestamp: new Date().toISOString(),
          }),
        });
        if (!response.ok) {
          throw new Error("Failed to send message");
        }
        const message = await response.json();
        this.chatMessages.push(message);
      } catch (error) {
        this.errorMessage = "Failed to send message. Please try again.";
      } finally {
        this.isSendingMessage = false;
      }
    },

    clearData() {
      this.appointment = null;
      this.patient = null;
      this.consultation = null;
      this.chatMessages = [];
      this.isLoading = false;
      this.errorMessage = "";
      this.isSavingNotes = false;
      this.isSendingMessage = false;
    },
  },
});
