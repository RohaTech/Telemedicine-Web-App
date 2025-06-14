# Payment Gateway Implementation Guide

This document outlines the payment gateway implementation for the telemedicine web application, including integration options and best practices.

## Overview

The payment system ensures that patients pay consultation fees before accessing doctor consultations. The system supports multiple payment methods and provides a seamless user experience.

## Current Implementation

### Architecture
- **Frontend**: Vue.js with payment modal and service layer
- **Backend**: Laravel with Payment model and controller
- **Database**: Payment records with consultation linking
- **Services**: Modular payment service with gateway abstraction

### Payment Flow
1. Patient clicks "Go to Consultation" 
2. System checks if consultation is paid
3. If unpaid, displays payment modal
4. Patient selects payment method and completes payment
5. System records payment and allows consultation access
6. If already paid, directly accesses consultation

## Payment Methods Supported

### 1. Credit/Debit Card (Stripe Integration Ready)
```javascript
// Example Stripe integration
const stripe = Stripe('your-publishable-key');
const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
  payment_method: { card: cardElement }
});
```

### 2. Mobile Money (African Markets)
- MTN Mobile Money
- Airtel Money  
- Orange Money
- Vodafone Cash

### 3. PayPal
```javascript
// PayPal button integration
paypal.Buttons({
  createOrder: (data, actions) => {
    return actions.order.create({
      purchase_units: [{ amount: { value: amount } }]
    });
  }
}).render('#paypal-button-container');
```

## File Structure

```
Client/
├── src/
│   ├── components/
│   │   └── Payment/
│   │       └── PaymentModal.vue          # Payment UI component
│   ├── services/
│   │   ├── paymentService.js             # Main payment service
│   │   └── realPaymentGateways.js        # Real gateway integrations
│   ├── stores/
│   │   ├── consultationPaymentStore.js   # Payment state management
│   │   └── paymentStore.js               # General payment store
│   └── views/
│       └── User/
│           └── UserConsultation.vue      # Consultation listing with payment

Server/
├── app/
│   ├── Http/Controllers/
│   │   └── PaymentController.php         # Payment API endpoints
│   └── Models/
│       └── Payment.php                   # Payment database model
├── database/migrations/
│   └── *_add_consultation_id_to_payments_table.php
└── routes/
    └── api.php                          # Payment API routes
```

## API Endpoints

### Payment Status Check
```http
GET /api/consultations/{id}/payment-status
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "consultation_id": 123,
    "paid": true,
    "payment": {...},
    "consultation_fee": 50
  }
}
```

### Create Payment Intent
```http
POST /api/consultations/create-payment-intent
Content-Type: application/json
Authorization: Bearer {token}

{
  "consultation_id": 123,
  "amount": 50
}
```

### Confirm Payment
```http
POST /api/consultations/confirm-payment
Content-Type: application/json
Authorization: Bearer {token}

{
  "consultation_id": 123,
  "payment_intent_id": "pi_xxx",
  "payment_method": "card",
  "amount": 50
}
```

## Database Schema

### payments Table
```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED NOT NULL,
    consultation_id BIGINT UNSIGNED NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('card', 'mobile_money', 'paypal', 'cash') NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE
);
```

## Integration Guide

### 1. Stripe Integration

#### Frontend Setup
```bash
npm install @stripe/stripe-js
```

```javascript
// Initialize Stripe
import { loadStripe } from '@stripe/stripe-js';
const stripe = await loadStripe('pk_test_...');

// Create payment intent
const response = await fetch('/api/create-payment-intent', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ amount: 5000 }) // Amount in cents
});
const { client_secret } = await response.json();

// Confirm payment
const { error, paymentIntent } = await stripe.confirmCardPayment(client_secret, {
  payment_method: { card: cardElement }
});
```

#### Backend Setup
```bash
composer require stripe/stripe-php
```

```php
// PaymentController.php
use Stripe\Stripe;
use Stripe\PaymentIntent;

public function createPaymentIntent(Request $request)
{
    Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    
    $paymentIntent = PaymentIntent::create([
        'amount' => $request->amount,
        'currency' => 'usd',
        'metadata' => [
            'consultation_id' => $request->consultation_id
        ]
    ]);
    
    return response()->json([
        'client_secret' => $paymentIntent->client_secret
    ]);
}
```

### 2. PayPal Integration

#### Frontend Setup
```html
<!-- Include PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID"></script>
```

```javascript
// Create PayPal button
paypal.Buttons({
  createOrder: function(data, actions) {
    return actions.order.create({
      purchase_units: [{
        amount: { value: '50.00' }
      }]
    });
  },
  onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
      // Send to backend for verification
    });
  }
}).render('#paypal-button-container');
```

### 3. Mobile Money Integration

#### Flutterwave Integration
```bash
composer require flutterwave/flutterwave-php
```

```php
// Mobile money payment
use Flutterwave\Flutterwave;

$flutterwave = new Flutterwave();
$response = $flutterwave->mobileMoney([
    'amount' => $amount,
    'phone_number' => $phoneNumber,
    'network' => 'MTN', // or AIRTEL, etc.
    'currency' => 'UGX'
]);
```

## Security Best Practices

### 1. Payment Data Security
- Never store sensitive payment data (card numbers, CVV)
- Use HTTPS for all payment-related communications
- Implement proper input validation and sanitization
- Use payment gateway tokens instead of raw payment data

### 2. Transaction Verification
- Always verify payments on the backend
- Implement webhook handlers for payment confirmations
- Use idempotency keys to prevent duplicate payments
- Log all payment transactions for auditing

### 3. Error Handling
```javascript
// Proper error handling in payment service
try {
  const result = await processPayment(paymentData);
  if (!result.success) {
    throw new Error(result.error);
  }
  return result;
} catch (error) {
  console.error('Payment error:', error);
  // Log error to monitoring service
  // Show user-friendly error message
  throw new Error('Payment failed. Please try again.');
}
```

## Testing

### Test Payment Methods
- **Stripe**: Use test card numbers (4242 4242 4242 4242)
- **PayPal**: Use PayPal sandbox environment
- **Mobile Money**: Use provider test numbers

### Test Scenarios
1. Successful payment completion
2. Payment failure handling
3. Network interruption during payment
4. Duplicate payment prevention
5. Payment status checking
6. Refund processing

## Monitoring and Analytics

### Key Metrics to Track
- Payment success rate
- Payment method usage
- Average transaction amount
- Failed payment reasons
- Payment completion time

### Logging Requirements
```php
// Payment event logging
Log::info('Payment initiated', [
    'consultation_id' => $consultationId,
    'amount' => $amount,
    'payment_method' => $paymentMethod,
    'user_id' => auth()->id()
]);

Log::info('Payment completed', [
    'transaction_id' => $transactionId,
    'status' => 'completed'
]);
```

## Deployment Considerations

### Environment Variables
```env
# Stripe
STRIPE_PUBLISHABLE_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# PayPal
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...
PAYPAL_MODE=live # or sandbox

# Mobile Money
FLUTTERWAVE_PUBLIC_KEY=...
FLUTTERWAVE_SECRET_KEY=...
```

### Production Checklist
- [ ] SSL certificate installed and configured
- [ ] Payment gateway credentials updated to production
- [ ] Webhook endpoints configured and secured
- [ ] Error monitoring and alerting set up
- [ ] Payment reconciliation process established
- [ ] Backup and recovery procedures documented
- [ ] Security audit completed
- [ ] Load testing performed
- [ ] User acceptance testing completed

## Support and Maintenance

### Common Issues
1. **Payment Failed**: Check gateway status and user payment method
2. **Duplicate Payments**: Implement idempotency and proper validation
3. **Webhook Failures**: Retry mechanism and manual reconciliation
4. **Refund Requests**: Automated refund processing via gateway APIs

### Maintenance Tasks
- Regular payment reconciliation
- Monitor gateway API changes
- Update payment method options
- Security patches and updates
- Performance optimization

## Conclusion

This payment system provides a robust foundation for handling consultation payments in the telemedicine application. The modular design allows for easy integration of additional payment methods and gateways as the platform grows.

For immediate implementation, start with Stripe for card payments and gradually add other payment methods based on user requirements and geographic expansion needs.
