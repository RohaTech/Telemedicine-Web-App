// Stripe Payment Integration Example
// This file shows how you would integrate Stripe for real payments

class StripePaymentService {
  constructor() {
    this.stripe = null;
    this.elements = null;
    this.cardElement = null;
  }

  // Initialize Stripe (call this when the payment modal opens)
  async initializeStripe(publishableKey) {
    try {
      // Load Stripe.js
      if (!window.Stripe) {
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        document.head.appendChild(script);
        
        await new Promise((resolve) => {
          script.onload = resolve;
        });
      }

      this.stripe = window.Stripe(publishableKey);
      this.elements = this.stripe.elements();
      
      return true;
    } catch (error) {
      console.error('Failed to initialize Stripe:', error);
      return false;
    }
  }

  // Create payment intent on the backend
  async createPaymentIntent(consultationId, amount) {
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
      });

      if (!response.ok) {
        throw new Error('Failed to create payment intent');
      }

      const data = await response.json();
      return data.data;
    } catch (error) {
      console.error('Error creating payment intent:', error);
      throw error;
    }
  }

  // Create card element for the UI
  createCardElement(elementId) {
    if (!this.elements) {
      throw new Error('Stripe not initialized');
    }

    this.cardElement = this.elements.create('card', {
      style: {
        base: {
          fontSize: '16px',
          color: '#424770',
          '::placeholder': {
            color: '#aab7c4',
          },
        },
        invalid: {
          color: '#9e2146',
        },
      },
    });

    this.cardElement.mount(`#${elementId}`);
    return this.cardElement;
  }

  // Process the payment
  async processStripePayment(consultationId, amount, billingDetails) {
    try {
      // Step 1: Create payment intent
      const paymentIntent = await this.createPaymentIntent(consultationId, amount);

      // Step 2: Confirm payment with Stripe
      const { error, paymentIntent: confirmedPaymentIntent } = await this.stripe.confirmCardPayment(
        paymentIntent.client_secret,
        {
          payment_method: {
            card: this.cardElement,
            billing_details: billingDetails,
          }
        }
      );

      if (error) {
        throw new Error(error.message);
      }

      // Step 3: Confirm payment on your backend
      const confirmResponse = await fetch('/api/consultations/confirm-payment', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          consultation_id: consultationId,
          payment_intent_id: confirmedPaymentIntent.id,
          payment_method: 'card',
          amount: amount
        })
      });

      if (!confirmResponse.ok) {
        throw new Error('Failed to confirm payment on server');
      }

      const confirmData = await confirmResponse.json();
      return {
        success: true,
        paymentIntent: confirmedPaymentIntent,
        payment: confirmData.data
      };
    } catch (error) {
      return {
        success: false,
        error: error.message
      };
    }
  }

  // Clean up
  destroy() {
    if (this.cardElement) {
      this.cardElement.destroy();
      this.cardElement = null;
    }
    this.elements = null;
    this.stripe = null;
  }
}

// PayPal Integration Example
class PayPalPaymentService {
  constructor() {
    this.paypal = null;
  }

  // Initialize PayPal
  async initializePayPal(clientId) {
    try {
      if (!window.paypal) {
        const script = document.createElement('script');
        script.src = `https://www.paypal.com/sdk/js?client-id=${clientId}&currency=USD`;
        document.head.appendChild(script);
        
        await new Promise((resolve) => {
          script.onload = resolve;
        });
      }

      this.paypal = window.paypal;
      return true;
    } catch (error) {
      console.error('Failed to initialize PayPal:', error);
      return false;
    }
  }

  // Create PayPal button
  createPayPalButton(elementId, amount, consultationId) {
    if (!this.paypal) {
      throw new Error('PayPal not initialized');
    }

    return this.paypal.Buttons({
      createOrder: (data, actions) => {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: amount.toString(),
              currency_code: 'USD'
            },
            description: `Consultation Payment - ID: ${consultationId}`
          }]
        });
      },
      
      onApprove: async (data, actions) => {
        const order = await actions.order.capture();
        
        // Confirm payment on your backend
        const response = await fetch('/api/consultations/confirm-payment', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            consultation_id: consultationId,
            payment_intent_id: order.id,
            payment_method: 'paypal',
            amount: amount
          })
        });

        if (response.ok) {
          return { success: true, order };
        } else {
          throw new Error('Failed to confirm PayPal payment');
        }
      },
      
      onError: (err) => {
        console.error('PayPal error:', err);
        throw new Error('PayPal payment failed');
      }
    }).render(`#${elementId}`);
  }
}

// Mobile Money Integration Example (for African markets)
class MobileMoneyService {
  constructor() {
    this.providers = {
      mtn: 'MTN Mobile Money',
      airtel: 'Airtel Money',
      orange: 'Orange Money',
      vodafone: 'Vodafone Cash'
    };
  }

  // Process mobile money payment
  async processMobileMoneyPayment(consultationId, amount, phoneNumber, provider) {
    try {
      // In a real implementation, you would integrate with providers like:
      // - MTN MoMo API
      // - Airtel Money API
      // - Orange Money API
      // - Flutterwave (aggregator)
      // - Paystack (aggregator)

      const response = await fetch('/api/mobile-money/initiate-payment', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          consultation_id: consultationId,
          amount: amount,
          phone_number: phoneNumber,
          provider: provider
        })
      });

      if (!response.ok) {
        throw new Error('Failed to initiate mobile money payment');
      }

      const data = await response.json();
      
      // Usually, mobile money payments require user confirmation on their phone
      // You would then poll the status or use webhooks to confirm payment
      
      return {
        success: true,
        transactionId: data.transaction_id,
        message: 'Please check your phone and confirm the payment'
      };
    } catch (error) {
      return {
        success: false,
        error: error.message
      };
    }
  }

  // Check mobile money payment status
  async checkPaymentStatus(transactionId) {
    try {
      const response = await fetch(`/api/mobile-money/status/${transactionId}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        }
      });

      if (!response.ok) {
        throw new Error('Failed to check payment status');
      }

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error checking mobile money status:', error);
      throw error;
    }
  }
}

// Export services
export {
  StripePaymentService,
  PayPalPaymentService,
  MobileMoneyService
};

/* 
IMPLEMENTATION NOTES:

1. For Stripe:
   - Sign up at https://stripe.com
   - Get your publishable and secret keys
   - Install Stripe PHP library: composer require stripe/stripe-php
   - Update backend PaymentController with real Stripe integration

2. For PayPal:
   - Sign up at https://developer.paypal.com
   - Get your client ID and secret
   - Use PayPal SDK for backend integration

3. For Mobile Money:
   - Integrate with Flutterwave, Paystack, or direct provider APIs
   - Each country/provider has different integration requirements

4. Security considerations:
   - Never store card details in your database
   - Use HTTPS for all payment pages
   - Validate payments on the backend
   - Implement webhook verification
   - Log all payment transactions for auditing

5. Backend updates needed:
   - Update PaymentController with real gateway integration
   - Add webhook handlers for payment confirmations
   - Implement proper error handling and logging
   - Add payment status checking and refund capabilities
*/
