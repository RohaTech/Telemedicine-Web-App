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
    // Added for patient consultations
    patientConsultations: [],
    isLoadingConsultations: false,
    consultationsError: "",
  }),

  actions: {
   async getAConsultation(consultation) {
      try {
        const res = await fetch(`/api/consultations/${consultation}`, {
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

    async getUserConsultations() {
      try {
        const res = await fetch("/api/consultations", {
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
    },    async saveNotes(notes) {
      if (!this.consultation?.id) {
        return { success: false, error: "No consultation found" };
      }
      
      this.isSavingNotes = true;
      this.errorMessage = "";
      
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
          const errorData = await response.json().catch(() => ({}));
          throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
        }
        
        const updatedConsultation = await response.json();
        this.consultation.notes = notes;
        
        console.log('Notes saved successfully');
        return { success: true, data: updatedConsultation };
      } catch (error) {
        console.error("Error saving notes:", error);
        this.errorMessage = error.message || "Failed to save notes. Please try again.";
        return { success: false, error: error.message };
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

    async fetchPatientConsultations(patientId) {
      if (!patientId) return { success: false, error: "No patient ID provided" };

      this.isLoadingConsultations = true;
      this.consultationsError = "";
      
      try {
        const response = await fetch(`/api/patients/${patientId}/consultations`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
            "Accept": "application/json"
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        
        if (data.success) {
          // Transform API data to match component structure
          this.patientConsultations = data.data.map(consultation => ({
            id: consultation.id,
            date: new Date(consultation.date).toLocaleDateString(),
            type: consultation.appointment_type || 'General Consultation',
            doctorName: consultation.doctor_name,
            specialty: consultation.doctor_specialty,
            notes: consultation.notes || 'No notes available',
            diagnosis: consultation.appointment_type || 'General consultation',
            prescriptions: consultation.prescription ? 
              (Array.isArray(consultation.prescription.medications) && consultation.prescription.medications.length > 0 ? 
                consultation.prescription.medications : 
                [consultation.prescription.instructions || 'No prescriptions']) : 
              ['No prescriptions'],
            vitalSigns: {
              // These would come from a separate vitals table in a real system
              bloodPressure: "N/A",
              heartRate: "N/A", 
              temperature: "N/A",
              weight: "N/A"
            }
          }));

          console.log('Fetched consultations from store:', this.patientConsultations);
          return { success: true, data: this.patientConsultations };
        } else {
          throw new Error(data.error || 'API response unsuccessful');
        }
      } catch (error) {
        console.error('Error fetching patient consultations:', error);
        this.consultationsError = error.message || 'Failed to fetch consultations';
        
        // Load sample data as fallback
        this.loadSampleConsultationData();
        return { success: false, error: error.message, usedFallback: true };
      } finally {
        this.isLoadingConsultations = false;
      }
    },

    loadSampleConsultationData() {
      this.patientConsultations = [
        {
          id: 1,
          date: "2024-05-15",
          type: "Follow-up Consultation",
          doctorName: "Dr. Sarah Johnson",
          specialty: "Cardiologist",
          notes: "Patient reported significant improvement in chest pain symptoms. Blood pressure well controlled on current medication. Recommended continuation of Lisinopril 10mg daily. Patient advised to maintain low-sodium diet and regular exercise routine. Follow-up in 3 months or sooner if symptoms return.",
          diagnosis: "Hypertension - well controlled",
          prescriptions: ["Lisinopril 10mg daily", "Aspirin 81mg daily"],
          vitalSigns: {
            bloodPressure: "128/82 mmHg",
            heartRate: "68 bpm",
            temperature: "98.4°F",
            weight: "185 lbs"
          }
        },
        {
          id: 2,
          date: "2024-03-20",
          type: "Initial Consultation",
          doctorName: "Dr. Michael Anderson",
          specialty: "Internal Medicine",
          notes: "Patient presented with complaints of intermittent chest discomfort and elevated blood pressure readings at home. Physical examination revealed no acute distress. Heart sounds regular, no murmurs. Lungs clear bilaterally. Recommended lifestyle modifications and initiated antihypertensive therapy.",
          diagnosis: "Essential Hypertension (newly diagnosed)",
          prescriptions: ["Lisinopril 5mg daily", "Lifestyle modifications"],
          vitalSigns: {
            bloodPressure: "145/95 mmHg",
            heartRate: "78 bpm", 
            temperature: "98.6°F",
            weight: "188 lbs"
          }
        },
        {
          id: 3,
          date: "2024-01-10",
          type: "Annual Physical",
          doctorName: "Dr. Emily Rodriguez",
          specialty: "Family Medicine",
          notes: "Routine annual physical examination. Patient reports feeling well overall. No acute complaints. Reviewed family history and current medications. Laboratory results pending. Encouraged patient to maintain healthy lifestyle habits.",
          diagnosis: "Annual wellness visit - no acute findings",
          prescriptions: ["Multivitamin daily", "Continue current supplements"],
          vitalSigns: {
            bloodPressure: "138/88 mmHg",
            heartRate: "72 bpm",
            temperature: "98.2°F", 
            weight: "190 lbs"
          }
        }
      ];
      console.log('Loaded sample consultation data from store:', this.patientConsultations);
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
      // Clear consultation history data
      this.patientConsultations = [];
      this.isLoadingConsultations = false;
      this.consultationsError = "";
    },
  },


});
