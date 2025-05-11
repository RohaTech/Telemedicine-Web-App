
import { defineStore } from 'pinia';
import router from '@/router';

export const useLabResultStore = defineStore('labResultStore', {
  state: () => {
    return {
      errors: {},
    };
  },

  actions: {
    async getLabResults() {
      try {
        const res = await fetch('/api/lab-results', {
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
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async getLabResult(id) {
      try {
        const res = await fetch(`/api/lab-results/${id}`, {
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });
        const data = await res.json();
        if (!res.ok) {
          this.errors = data || { message: 'Failed to fetch lab result' };
          return { success: false };
        }
        this.errors = {};
        return { success: true, data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async createLabResult() {
      try {
        const formData = new FormData();
        formData.append('lab_request_id', this.labResult.lab_request_id);
        formData.append('laboratory_id', this.labResult.laboratory_id);
        formData.append('result_details', this.labResult.result_details);
        if (this.labResult.attachment) {
          formData.append('attachment', this.labResult.attachment);
        }

        const res = await fetch('/api/lab-results', {
          method: 'POST',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
          body: formData,
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to create lab result' };
          return { success: false };
        }

        this.errors = {};
        this.resetForm();
        router.push({ name: 'LabResults' });
        return { success: true, message: 'Lab result created successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async updateLabResult(id) {
      try {
        let res;
        if (this.labResult.attachment instanceof File) {
          // Use FormData with POST and _method=PUT if there's a new file
          const formData = new FormData();
          formData.append('lab_request_id', this.labResult.lab_request_id);
          formData.append('laboratory_id', this.labResult.laboratory_id);
          formData.append('result_details', this.labResult.result_details);
          formData.append('attachment', this.labResult.attachment);
          formData.append('_method', 'PUT');

          res = await fetch(`/api/lab-results/${id}`, {
            method: 'POST', // Fallback to POST due to FormData limitation
            headers: {
              ...(localStorage.getItem('token') && {
                authorization: `Bearer ${localStorage.getItem('token')}`,
              }),
            },
            body: formData,
          });
        } else {
          // Use PUT with JSON if no new file
          res = await fetch(`/api/lab-results/${id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              ...(localStorage.getItem('token') && {
                authorization: `Bearer ${localStorage.getItem('token')}`,
              }),
            },
            body: JSON.stringify({
              lab_request_id: this.labResult.lab_request_id,
              laboratory_id: this.labResult.laboratory_id,
              result_details: this.labResult.result_details,
            }),
          });
        }

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to update lab result' };
          return { success: false };
        }

        this.errors = {};
        router.push({ name: 'LabResults' });
        return { success: true, message: 'Lab result updated successfully!', data };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    async deleteLabResult(id) {
      try {
        const res = await fetch(`/api/lab-results/${id}`, {
          method: 'DELETE',
          headers: {
            ...(localStorage.getItem('token') && {
              authorization: `Bearer ${localStorage.getItem('token')}`,
            }),
          },
        });

        const data = await res.json();

        if (!res.ok) {
          this.errors = data || { message: 'Failed to delete lab result' };
          return { success: false };
        }

        this.errors = {};
        return { success: true, message: 'Lab result deleted successfully!' };
      } catch (err) {
        this.errors = { message: err.message || 'An unexpected error occurred' };
        return { success: false };
      }
    },

    resetForm() {
      this.labResult = {
        lab_request_id: "",
        laboratory_id: "",
        result_details: "",
        attachment: null,
      };
      this.errors = {};
    },
  },
});