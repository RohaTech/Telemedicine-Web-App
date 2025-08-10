// Payment Service for handling different payment gateways
// This service provides a unified interface for various payment methods

class PaymentService {
  constructor() {
    this.apiBase = '/api';
  }

  // Check if consultation payment is required
  async checkConsultationPayment(consultationId) {
    try {
      const response = await fetch(`${this.apiBase}/consultations/${consultationId}/payment-status`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      return {
        success: true,
        isPaid: data.data.paid,
        consultationFee: data.data.consultation_fee,
        payment: data.data.payment
      };
    } catch (error) {
      console.error('Error checking consultation payment:', error);
      return {
        success: false,
        error: error.message
      };
    }
  }  // Process different payment methods
  async processPayment(paymentData) {
    const { paymentMethod, consultationId, amount } = paymentData;    
    
    console.log('Processing payment with initial amount:', amount); // Debug log
    
    try {
      // Get consultation details to extract doctor payment amount
      let actualAmount = amount;
      try {
        const consultationDetails = await this.getConsultationDetails(consultationId);
        console.log('Consultation details for payment:', consultationDetails); // Debug log
        
        // Try multiple possible paths for the payment amount
        const doctorPayment = consultationDetails?.doctor?.doctor?.payment || 
                             consultationDetails?.doctor?.payment;
        
        if (doctorPayment && doctorPayment > 0) {
          actualAmount = doctorPayment;
          console.log('Using doctor payment amount:', actualAmount); // Debug log
        } else {
          console.warn('No valid doctor payment found, using provided amount:', amount);
          actualAmount = amount;
        }
      } catch (error) {
        console.warn('Could not fetch consultation details, using provided amount:', error);
        actualAmount = amount;
      }

      console.log('Final payment amount to process:', actualAmount); // Debug log

      const updatedPaymentData = {
        ...paymentData,
        amount: actualAmount
      };

      switch (paymentMethod) {
        case 'card':
          return await this.processCardPayment(updatedPaymentData);
        case 'mobile_money':
          return await this.processMobileMoneyPayment(updatedPaymentData);
        case 'paypal':
          return await this.processPayPalPayment(updatedPaymentData);
        default:
          throw new Error('Unsupported payment method');
      }
    } catch (error) {
      return {
        success: false,
        error: error.message      };
    }
  }

  // Get consultation details including doctor payment amount
  async getConsultationDetails(consultationId) {
    try {
      const response = await fetch(`${this.apiBase}/consultations/${consultationId}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      return data.data;
    } catch (error) {
      console.error('Error fetching consultation details:', error);
      throw error;
    }
  }

  // Stripe/Card Payment Implementation
  async processCardPayment(paymentData) {
    const { consultationId, amount, cardDetails } = paymentData;

    try {
      // In a real implementation, you would:
      // 1. Create payment intent with Stripe
      // 2. Confirm payment on frontend
      // 3. Verify payment on backend

      // For now, simulate card payment
      await this.simulatePaymentDelay();

      // Validate card details (basic validation)
      if (!this.validateCardDetails(cardDetails)) {
        throw new Error('Invalid card details');
      }

      // Create transaction ID
      const transactionId = `card_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;      // Record payment in backend
      const paymentRecord = await this.createPaymentRecord({
        consultation_id: consultationId,
        amount: amount,
        payment_method: 'card',
        transaction_id: transactionId,
        status: 'completed'
      });

      return {
        success: true,
        transactionId,
        payment: paymentRecord,
        receiptData: {
          transactionId,
          timestamp: Date.now(),
          amount,
          paymentMethod: 'card',
          cardType: this.getCardType(cardDetails.number)
        }
      };
    } catch (error) {
      return {
        success: false,
        error: error.message
      };
    }
  }

  // Mobile Money Payment Implementation
  async processMobileMoneyPayment(paymentData) {
    const { consultationId, amount, mobileMoneyDetails } = paymentData;

    try {
      await this.simulatePaymentDelay();

      // Validate mobile money details
      if (!mobileMoneyDetails.provider || !mobileMoneyDetails.phone) {
        throw new Error('Mobile money provider and phone number are required');
      }

      const transactionId = `mm_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;      const paymentRecord = await this.createPaymentRecord({
        consultation_id: consultationId,
        amount: amount,
        payment_method: 'mobile_money',
        transaction_id: transactionId,
        status: 'completed'
      });

      return {
        success: true,
        transactionId,
        payment: paymentRecord,
        receiptData: {
          transactionId,
          timestamp: Date.now(),
          amount,
          paymentMethod: 'mobile_money',
          provider: this.getProviderDisplayName(mobileMoneyDetails.provider),
          phoneNumber: mobileMoneyDetails.phone
        }
      };
    } catch (error) {
      return {
        success: false,
        error: error.message
      };
    }
  }

  // PayPal Payment Implementation
  async processPayPalPayment(paymentData) {
    const { consultationId, amount } = paymentData;

    try {
      await this.simulatePaymentDelay();

      const transactionId = `pp_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;      const paymentRecord = await this.createPaymentRecord({
        consultation_id: consultationId,
        amount: amount,
        payment_method: 'paypal',
        transaction_id: transactionId,
        status: 'completed'
      });

      return {
        success: true,
        transactionId,
        payment: paymentRecord,
        receiptData: {
          transactionId,
          timestamp: Date.now(),
          amount,
          paymentMethod: 'paypal'
        }
      };
    } catch (error) {
      return {
        success: false,
        error: error.message
      };
    }
  }
  // Create payment record in backend
  async createPaymentRecord(paymentData) {
    try {
      // Get consultation details first
      const consultationResponse = await fetch(`${this.apiBase}/consultations/${paymentData.consultation_id}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
      });

      if (!consultationResponse.ok) {
        throw new Error('Failed to get consultation details');
      }

      const consultationResult = await consultationResponse.json();
      console.log('Consultation result for payment:', consultationResult);      // Handle different possible response structures
      const consultation = consultationResult.data || consultationResult;
      
      // Extract patient_id and doctor_id with multiple fallback options
      let patient_id = consultation.patient_id || 
                      consultation.patient?.id || 
                      consultation.user_id;
                      
      // If we still don't have patient_id, get it from the current user
      if (!patient_id) {
        try {
          const currentUser = JSON.parse(localStorage.getItem('user'));
          patient_id = currentUser?.id;
        } catch (error) {
          console.warn('Could not get current user from localStorage:', error);
        }
      }
      
      const doctor_id = consultation.doctor_id || 
                       consultation.doctor?.id || 
                       consultation.doctor?.user_id;

      console.log('Extracted IDs:', { patient_id, doctor_id, consultation });

      if (!patient_id || !doctor_id) {
        throw new Error(`Missing required IDs: patient_id=${patient_id}, doctor_id=${doctor_id}`);
      }

      // Create payment record
      const response = await fetch(`${this.apiBase}/payments`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },        body: JSON.stringify({
          ...paymentData,
          patient_id: patient_id,
          doctor_id: doctor_id
        })
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to create payment record');
      }

      const result = await response.json();
      return result.payment;
    } catch (error) {
      throw new Error(`Payment recording failed: ${error.message}`);
    }
  }

  // Utility methods
  validateCardDetails(cardDetails) {
    return cardDetails.number &&
           cardDetails.expiry &&
           cardDetails.cvc &&
           cardDetails.name &&
           cardDetails.number.replace(/\s/g, '').length >= 13;
  }
  async simulatePaymentDelay() {
    // Simulate network delay for payment processing (shorter for dev)
    return new Promise(resolve => setTimeout(resolve, 1500));
  }

  // Format amounts for display
  formatAmount(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount);
  }
  // Generate secure transaction ID
  generateTransactionId(prefix = 'TXN') {
    const timestamp = Date.now();
    const random = Math.random().toString(36).substr(2, 9);
    return `${prefix}_${timestamp}_${random}`;
  }

  // Get card type from card number
  getCardType(cardNumber) {
    const number = cardNumber.replace(/\s/g, '');
    if (number.startsWith('4')) return 'Visa';
    if (number.startsWith('5') || number.startsWith('2')) return 'Mastercard';
    if (number.startsWith('3')) return 'American Express';
    return 'Card';
  }

  // Get provider display name for mobile money
  getProviderDisplayName(provider) {
    const providers = {
      'mpesa': 'M-Pesa',
      'telebirr': 'Telebirr'
    };
    return providers[provider] || provider;
  }
}

// Export singleton instance
export default new PaymentService();
