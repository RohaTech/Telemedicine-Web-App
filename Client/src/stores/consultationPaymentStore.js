import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useConsultationPaymentStore = defineStore('consultationPayment', () => {
  // State
  const loading = ref(false)
  const error = ref(null)
  const paymentStatus = ref(null)

  // Actions
  const checkPaymentStatus = async (consultationId) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/consultations/${consultationId}/payment-status`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data = await response.json()
      paymentStatus.value = data.data
      return { success: true, data: data.data }
    } catch (err) {
      console.error('Error checking payment status:', err)
      error.value = err.message || 'Failed to check payment status'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const createPaymentIntent = async (consultationId, amount) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch('/api/consultations/create-payment-intent', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          consultation_id: consultationId,
          amount: amount
        })
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data = await response.json()
      return { success: true, data: data.data }
    } catch (err) {
      console.error('Error creating payment intent:', err)
      error.value = err.message || 'Failed to create payment intent'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const confirmPayment = async (consultationId, paymentData) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch('/api/consultations/confirm-payment', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          consultation_id: consultationId,
          ...paymentData
        })
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data = await response.json()
      return { success: true, data: data.data }
    } catch (err) {
      console.error('Error confirming payment:', err)
      error.value = err.message || 'Failed to confirm payment'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    loading,
    error,
    paymentStatus,
    
    // Actions
    checkPaymentStatus,
    createPaymentIntent,
    confirmPayment,
    clearError
  }
})
