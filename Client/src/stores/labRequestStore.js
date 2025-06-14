// filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\Store\labRequestStore.js
import { defineStore } from 'pinia';
import router from '@/router';

export const useLabRequestStore = defineStore('labRequestStore', {
  state: () => {
    return {
      labRequest: {
        consultation_id: "",
        doctor_id: "",
        patient_id: "",
        laboratory_id: "",
        test_type: "",
        status: "pending",
      },
      labRequests: [], // Add list for managing all lab requests
      isLoading: false, // Add loading state
      errors: {},
    };
  },

  getters: {
    // Add getters for filtering
    getLabRequestsByStatus: (state) => (status) => {
      if (!status) return state.labRequests;
      return state.labRequests.filter(request => request.status === status);
    },
    
    pendingRequests: (state) => {
      return state.labRequests.filter(request => request.status === 'pending');
    },
    
    completedRequests: (state) => {
      return state.labRequests.filter(request => request.status === 'completed');
    },
  },

  actions: {    async getLabRequests() {
      this.isLoading = true;
      this.errors = {};
      
      try {
        const res = await fetch('/api/lab-requests', {
          method: "GET",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        });
        const data = await res.json();
        
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch lab requests' };
          return { success: false, error: this.errors.message };
        } else {
          this.errors = {};
          this.labRequests = Array.isArray(data) ? data : [];
          return { success: true, data: this.labRequests };
        }
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false, error: err.message };
      } finally {
        this.isLoading = false;
      }
    },

    // Add alias method for consistency with component
    async fetchLabRequests() {
      return await this.getLabRequests();
    },

    async getLabRequest(id) {
      try {
        const res = await fetch(`/api/lab-requests/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch lab request' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },    async createLabRequest(labRequestData = null) {
      try {
        // Use provided data or fall back to store state
        const dataToSend = labRequestData || this.labRequest;
        
        const res = await fetch('/api/lab-requests', {
          method: 'post',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(dataToSend),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create lab request' };
          return { success: false, error: data.message || 'Failed to create lab request', errors: data.errors };
        }

        this.errors = {};
        
        // Add to local store if we have labRequests array
        if (this.labRequests && Array.isArray(this.labRequests)) {
          this.labRequests.unshift(data);
        }
        
        // Only reset form and redirect if using store state (not when called from modal)
        if (!labRequestData) {
          this.resetForm();
          router.push({ name: 'LabRequests' });
        }
        
        return { success: true, message: 'Lab request created successfully!', data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false, error: err.message };
      }
    },

    async updateLabRequest(id, labRequestData) {
      try {
        const res = await fetch(`/api/lab-requests/${id}`, {
          method: 'put',
          headers: {
            'Content-Type': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: JSON.stringify(labRequestData),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update lab request' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Lab request updated successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },    async deleteLabRequest(id) {
      try {
        const res = await fetch(`/api/lab-requests/${id}`, {
          method: 'delete',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        if (!res.ok) {
          const data = await res.json().catch(() => ({}));
          this.errors = data || { message: 'Failed to delete lab request' };
          return { success: false, error: data.message || 'Failed to delete lab request' };
        }

        // Remove from local state
        this.labRequests = this.labRequests.filter(request => request.id !== parseInt(id));
        this.errors = {};
        return { success: true, message: 'Lab request deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false, error: err.message };
      }
    },

    resetForm() {
      this.labRequest = {
        consultation_id: "",
        doctor_id: "",
        patient_id: "",
        laboratory_id: "",
        test_type: "",
        status: "pending",
      };
      this.errors = {};
    },
  },
});