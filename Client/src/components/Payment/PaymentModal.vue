<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div class="relative max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
      <!-- Modal Header -->
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Payment Required</h2>
        <button
          @click="$emit('close')"
          class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Payment Info -->
      <div class="mb-6 rounded-lg bg-blue-50 p-4">
        <div class="flex items-center">
          <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clip-rule="evenodd"
            />
          </svg>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              <strong>Consultation Fee:</strong> ${{ consultationFee }}
            </p>
            <p class="text-xs text-blue-600">
              Payment is required to start the consultation with Dr. {{ doctorName }} and 15% will be charged for the platform
            </p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="py-8 text-center">
        <div
          class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"
        ></div>
        <p class="mt-4 text-gray-600">Processing payment...</p>
      </div>

      <!-- Payment Form -->
      <div v-else>
        <!-- Payment Method Selection -->
        <div class="mb-6">
          <label class="mb-2 block text-sm font-medium text-gray-700">
            Payment Method
          </label>
          <div class="space-y-2">
            <label class="flex items-center">
              <input
                v-model="paymentMethod"
                type="radio"
                value="card"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">Credit/Debit Card</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="paymentMethod"
                type="radio"
                value="mobile_money"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">Mobile Money</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="paymentMethod"
                type="radio"
                value="paypal"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">PayPal</span>
            </label>
          </div>
        </div>

        <!-- Card Payment Form -->
        <div v-if="paymentMethod === 'card'" class="space-y-4">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
              Card Number
            </label>
            <input
              v-model="cardDetails.number"
              type="text"
              placeholder="1234 5678 9012 3456"
              maxlength="19"
              @input="formatCardNumber"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Expiry Date
              </label>
              <input
                v-model="cardDetails.expiry"
                type="text"
                placeholder="MM/YY"
                maxlength="5"
                @input="formatExpiry"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                CVC
              </label>
              <input
                v-model="cardDetails.cvc"
                type="text"
                placeholder="123"
                maxlength="4"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
              Cardholder Name
            </label>
            <input
              v-model="cardDetails.name"
              type="text"
              placeholder="John Doe"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
          </div>
        </div>

        <!-- Mobile Money Form -->
        <div v-else-if="paymentMethod === 'mobile_money'" class="space-y-4">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
              Mobile Money Provider
            </label>            <select
              v-model="mobileMoneyDetails.provider"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
              <option value="">Select Provider</option>
              <option value="mpesa">M-Pesa</option>
              <option value="telebirr">Telebirr</option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
              Phone Number
            </label>
            <input
              v-model="mobileMoneyDetails.phone"
              type="tel"
              placeholder="+233 XX XXX XXXX"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
          </div>
        </div>

        <!-- PayPal Info -->
        <div v-else-if="paymentMethod === 'paypal'" class="space-y-4">
          <div class="rounded-lg bg-yellow-50 p-4">
            <p class="text-sm text-yellow-800">
              You will be redirected to PayPal to complete your payment securely.
            </p>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mb-4 rounded-lg bg-red-50 p-4">
          <p class="text-sm text-red-800">{{ error }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6">
          <button
            @click="$emit('close')"
            type="button"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="processPayment"
            :disabled="!isFormValid || processing"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {{ processing ? 'Processing...' : `Pay $${consultationFee}` }}
          </button>
        </div>      </div>
    </div>
  </div>
  <!-- Payment Receipt Modal -->
  <PaymentReceipt
    :show="showReceipt"
    :payment-data="receiptData || {}"
    :consultation-id="consultationId || 0"
    :doctor-name="doctorName || 'Doctor'"
    :amount="receiptData?.amount || consultationFee"
    :payment-method="receiptData?.paymentMethod || paymentMethod || 'card'"
    @close="handleReceiptClose"
    @proceed-to-consultation="handleReceiptProceed"
  />
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useConsultationPaymentStore } from '@/stores/consultationPaymentStore'
import { useToast } from "vue-toastification"
import paymentService from '@/services/paymentService'
import PaymentReceipt from './PaymentReceipt.vue'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  consultationId: {
    type: [String, Number],
    required: true
  },  consultationFee: {
    type: [String, Number],
    required: true
  },
  doctorName: {
    type: String,
    default: 'the Doctor'
  }
})

// Emits
const emit = defineEmits(['close', 'payment-success'])

// Store and composables
const paymentStore = useConsultationPaymentStore()
const toast = useToast()

// State
const paymentMethod = ref('card')
const processing = ref(false)
const error = ref(null)
const showReceipt = ref(false)
const receiptData = ref(null)

const cardDetails = ref({
  number: '',
  expiry: '',
  cvc: '',
  name: ''
})

const mobileMoneyDetails = ref({
  provider: '',
  phone: ''
})

// Computed
const loading = computed(() => paymentStore.loading || processing.value)

const isFormValid = computed(() => {
  if (paymentMethod.value === 'card') {
    return cardDetails.value.number &&
           cardDetails.value.expiry &&
           cardDetails.value.cvc &&
           cardDetails.value.name
  } else if (paymentMethod.value === 'mobile_money') {
    return mobileMoneyDetails.value.provider && mobileMoneyDetails.value.phone
  } else if (paymentMethod.value === 'paypal') {
    return true
  }
  return false
})

// Methods
const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '')
  let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value
  if (formattedValue.length > 19) formattedValue = formattedValue.substring(0, 19)
  cardDetails.value.number = formattedValue
}

const formatExpiry = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4)
  }
  cardDetails.value.expiry = value
}

const processPayment = async () => {
  processing.value = true
  error.value = null

  try {
    const paymentData = {
      consultationId: props.consultationId,
      amount: props.consultationFee,
      paymentMethod: paymentMethod.value,
      cardDetails: cardDetails.value,
      mobileMoneyDetails: mobileMoneyDetails.value
    }

    const result = await paymentService.processPayment(paymentData)

    if (result.success) {
      // Store receipt data and show 
      
      receiptData.value = {
        ...result.receiptData,
        consultationId: props.consultationId,
        doctorName: props.doctorName,
        // amount: props.consultationFee,
        paymentMethod: paymentMethod.value
      }

      showReceipt.value = true

      toast.success('Payment successful!', {
        position: "top-right",
        timeout: 2000
      })
    } else {
      throw new Error(result.error || 'Payment failed')
    }
  } catch (err) {
    console.error('Payment error:', err)
    error.value = err.message || 'Payment processing failed. Please try again.'

    toast.error('Payment failed. Please try again.', {
      position: "top-right",
      timeout: 3000
    })
  } finally {
    processing.value = false
  }
}

// Handle receipt close and proceed to consultation
const handleReceiptProceed = () => {
  showReceipt.value = false
  emit('payment-success', receiptData.value)
  emit('close')
}

const handleReceiptClose = () => {
  showReceipt.value = false
  emit('close')
}

// Clear form when modal closes
watch(() => props.show, (newValue) => {
  if (!newValue) {    // Reset form
    paymentMethod.value = 'card'
    cardDetails.value = { number: '', expiry: '', cvc: '', name: '' }
    mobileMoneyDetails.value = { provider: '', phone: '' }
    error.value = null
    processing.value = false
    showReceipt.value = false
    receiptData.value = null
  }
})
</script>
