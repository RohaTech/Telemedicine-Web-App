<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div class="relative max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-white shadow-xl">
      <!-- Receipt Content -->
      <div class="p-6">
        <!-- Header -->        <div class="text-center border-b border-gray-200 pb-4 mb-6">
          <div class="flex items-center justify-center mb-2">
            <svg class="w-8 h-8 text-first-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h1 class="text-xl font-bold text-gray-800">Payment Successful</h1>
          </div>
          <h2 class="text-lg font-semibold text-first-accent">Tenadam Telemedicine Platform</h2>
          <p class="text-sm text-gray-500 mt-1">Payment Receipt</p>
        </div>

        <!-- Receipt Details -->
        <div class="space-y-4">          <!-- Transaction Info -->
          <div class="bg-green-50 border border-first-accent rounded-lg p-4">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-first-accent mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="text-first-accent font-medium">Payment Confirmed</span>
            </div>
          </div>

          <!-- Receipt Info Grid -->
          <div class="grid grid-cols-1 gap-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Receipt No:</span>
              <span class="text-sm text-gray-800 font-mono">{{ receiptNumber }}</span>
            </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Transaction ID:</span>
              <span class="text-sm text-gray-800 font-mono">{{ paymentData?.transactionId || 'N/A' }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Date & Time:</span>
              <span class="text-sm text-gray-800">{{ formatDate(paymentData?.timestamp || Date.now()) }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Doctor:</span>
              <span class="text-sm text-gray-800">{{ doctorName }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Service:</span>
              <span class="text-sm text-gray-800">Medical Consultation</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Payment Method:</span>
              <span class="text-sm text-gray-800">{{ getPaymentMethodDisplay(paymentMethod) }}</span>
            </div>            <!-- Amount Section -->
            <div class="bg-green-50 border border-first-accent rounded-lg p-3 mt-4">
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-first-accent">Consultation Fee:</span>
                <span class="text-lg font-bold text-first-accent">${{ amount }}</span>
              </div>
              <div class="flex justify-between items-center mt-1">
                <span class="text-xs text-gray-600">Tax (0%):</span>
                <span class="text-xs text-gray-600">$0.00</span>
              </div>
              <hr class="border-first-accent my-2">
              <div class="flex justify-between items-center">
                <span class="text-base font-bold text-first-accent">Total Paid:</span>
                <span class="text-xl font-bold text-first-accent">${{ amount }}</span>
              </div>
            </div>
          </div>          <!-- Payment Status -->
          <div class="text-center py-4">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-green-100 text-first-accent">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm font-medium">Payment Completed</span>
            </div>
          </div>

          <!-- Note -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-600 text-center">
              This is a digital receipt for your consultation payment. 
              Keep this for your records. You can now proceed to your consultation.
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col space-y-3 mt-6">          <button
            @click="printReceipt"
            class="w-full bg-first-accent text-white px-4 py-2 rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-first-accent focus:ring-offset-2 transition-colors"
          >
            Print Receipt
          </button><button
            @click="downloadReceipt"
            class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download PDF Receipt
          </button>          <button
            @click="proceedToConsultation"
            class="w-full bg-second-accent text-white px-4 py-2 rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-second-accent focus:ring-offset-2 transition-colors"
          >
            Proceed to Consultation
          </button>
          
          <button
            @click="$emit('close')"
            class="w-full bg-white text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import jsPDF from 'jspdf'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  paymentData: {
    type: Object,
    default: () => ({})
  },
  consultationId: {
    type: [String, Number],
    required: true
  },
  doctorName: {
    type: String,
    default: 'Doctor'
  },  amount: {
    type: [String, Number],
    required: true
  },
  paymentMethod: {
    type: String,
    default: 'card'
  }
})

const emit = defineEmits(['close', 'proceed-to-consultation'])
const router = useRouter()

// Generate receipt number
const receiptNumber = computed(() => {
  const timestamp = Date.now()
  const random = Math.random().toString(36).substr(2, 4).toUpperCase()
  return `RCT-${timestamp}-${random}`
})

// Generate reference number similar to bank format
const referenceNumber = computed(() => {
  const timestamp = Date.now()
  const random = Math.random().toString(36).substr(2, 6).toUpperCase()
  return `FT${timestamp.toString().slice(-6)}${random}`
})

// Format date
const formatDate = (timestamp) => {
  return new Date(timestamp).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

// Get payment method display name
const getPaymentMethodDisplay = (method) => {
  const methods = {
    'card': 'Credit/Debit Card',
    'mobile_money': 'Mobile Money',
    'paypal': 'PayPal'
  }
  return methods[method] || method
}

// Print receipt
const printReceipt = () => {
  window.print()
}

// Download receipt as PDF with bank-style format
const downloadReceipt = () => {
  const doc = new jsPDF()
  
  // Set up colors using the new brand colors
  const primaryColor = [4, 133, 129] // #048581 - Primary teal
  const secondaryColor = [47, 199, 161] // #2FC7A1 - Secondary teal/green
  const lightTeal = [200, 240, 240] // Light teal for borders
  const textColor = [0, 0, 0]
  const grayColor = [128, 128, 128]
  
  // Header section with primary teal background
  doc.setFillColor(...primaryColor)
  doc.rect(0, 0, 210, 35, 'F')
  
  // Company logo area - using the brand colors
  doc.setFillColor(...secondaryColor) // Light green/teal for logo background
  doc.circle(25, 17, 10, 'F')
  doc.setTextColor(255, 255, 255)
  doc.setFontSize(12)
  doc.setFont('helvetica', 'bold')
  doc.text('T', 22, 19)
  
  // Header text
  doc.setTextColor(255, 255, 255)
  doc.setFontSize(18)
  doc.setFont('helvetica', 'bold')
  doc.text('Tenadam Telemedicine Platform', 45, 18)
  doc.setFontSize(11)
  doc.setFont('helvetica', 'normal')
  doc.text('Payment Invoice / Customer Receipt', 45, 26)
  
  // Reset text color for body
  doc.setTextColor(...textColor)
  
  // Company and Customer Information Section
  doc.setFontSize(10)
  doc.setFont('helvetica', 'bold')
  
  // Left column - Company Information
  doc.text('Company Address & Other Information', 15, 50)
  doc.setFont('helvetica', 'normal')
  doc.setFontSize(8)
  
  const companyInfo = [
    ['Country:', 'Ethiopia'],
    ['City:', 'Addis Ababa'],
    ['Address:', 'Bole District St. 01, Kirkos'],
    ['Postal code:', '1000'],
    ['SWIFT Code:', 'TENADAMAA'],
    ['Email:', 'support@tenadam.com'],
    ['Tel:', '+251-11-123-4567'],
    ['Fax:', '+251-11-123-4568'],
    ['VAT Receipt No:', receiptNumber.value],
    ['VAT Registration No:', '011148'],
    ['VAT Registration Date:', '01/01/2023']
  ]
  
  companyInfo.forEach((info, index) => {
    doc.text(info[0], 15, 57 + (index * 4))
    doc.text(info[1], 65, 57 + (index * 4))
  })
  
  // Right column - Customer Information
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(10)
  doc.text('Customer Information', 110, 50)
  doc.setFont('helvetica', 'normal')
  doc.setFontSize(8)
  
  const customerInfo = [
    ['Patient Name:', 'CURRENT USER'],
    ['Region:', 'Addis Ababa'],
    ['City:', 'Addis Ababa'],
    ['Sub City:', 'Bole'],
    ['Wereda/Kebele:', '--'],
    ['House/Plot No.:', '--'],
    ['VAT Registration No.:', '20001203'],
    ['TIN (TAX ID):', '--'],
    ['Branch:', 'MAIN BRANCH']
  ]
  
  customerInfo.forEach((info, index) => {
    doc.text(info[0], 110, 57 + (index * 4))
    doc.text(info[1], 155, 57 + (index * 4))
  })
  
  // Payment Transaction Information Section
  doc.setFillColor(...lightTeal)
  doc.rect(15, 105, 180, 12, 'F')
  doc.setFillColor(...primaryColor)
  doc.rect(15, 105, 180, 8, 'F')
    doc.setTextColor(255, 255, 255)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(12)
  doc.text('Payment / Transaction Information', 85, 111)
  
  // Transaction details table
  doc.setTextColor(...textColor)
  doc.setFont('helvetica', 'normal')
  doc.setFontSize(9)
  doc.setDrawColor(...primaryColor)
  doc.setLineWidth(0.5)
  
  const transactionData = [
    ['Payer', 'CURRENT USER'],
    ['Account', '****1393'],
    ['Receiver', props.doctorName.toUpperCase()],
    ['Account', '****7638'],
    ['Payment Date & Time', formatDate(props.paymentData?.timestamp || Date.now())],
    ['Reference No. (VAT Invoice No)', referenceNumber.value],
    ['Reason / Type of service', 'Medical Consultation via Telemedicine'],
    ['Transferred Amount', `${props.amount}.00 USD`],
    ['Commission or Service Charge', '0.00 USD'],
    ['15% VAT on Commission', '0.00 USD'],
    ['Total amount debited from customers account', `${props.amount}.00 USD`]
  ]
  
  let yPos = 125
  transactionData.forEach((row, index) => {
    // Alternate row colors for better readability
    if (index % 2 === 0) {
      doc.setFillColor(250, 250, 250)
      doc.rect(15, yPos - 2, 180, 8, 'F')
    }
    
    // Draw border with brand color
    doc.setDrawColor(...primaryColor)
    doc.rect(15, yPos - 2, 180, 8)
    
    // Add data with better formatting
    doc.setFont('helvetica', 'normal')
    doc.text(row[0], 20, yPos + 2)
    doc.setFont('helvetica', 'bold')
    doc.text(row[1], 110, yPos + 2)
    
    yPos += 8
  })
  
  // Amount in Words section with brand styling
  doc.setDrawColor(...primaryColor)
  doc.setLineWidth(1)
  doc.rect(15, yPos + 10, 120, 20)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(9)
  doc.setTextColor(...primaryColor)
  doc.text('Amount in Word', 20, yPos + 18)
  doc.setFont('helvetica', 'normal')
  doc.setFontSize(8)
  doc.setTextColor(...textColor)
  doc.text(`USD ${convertNumberToWords(props.amount)} & Zero cents`, 20, yPos + 25)
  
  // QR Code placeholder with better styling using brand colors
  doc.setDrawColor(...secondaryColor)
  doc.setLineWidth(1)
  doc.rect(145, yPos + 10, 50, 20)
  doc.setTextColor(...secondaryColor)
  doc.setFontSize(6)
  doc.text('█ █ █ █ █ █ █ █ █', 150, yPos + 16)
  doc.text('█   █   █   █   █', 150, yPos + 18)
  doc.text('█ █ █ █ █ █ █ █ █', 150, yPos + 20)
  doc.text('█   █   █   █   █', 150, yPos + 22)
  doc.text('█ █ █ █ █ █ █ █ █', 150, yPos + 24)
  doc.setFontSize(8)
  doc.setTextColor(...primaryColor)
  doc.text('QR CODE', 160, yPos + 28)
  
  // Footer with styling similar to bank receipt using brand colors
  doc.setFillColor(245, 245, 245)
  doc.rect(15, yPos + 40, 180, 20, 'F')
  doc.setTextColor(...primaryColor)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(11)
  doc.text('The Platform you can always rely on.', 70, yPos + 48)
  doc.setFont('helvetica', 'normal')
  doc.setFontSize(8)
  doc.setTextColor(...secondaryColor)
  doc.text('© 2025 Tenadam Telemedicine Platform. All rights reserved.', 55, yPos + 55)
  
  // Save the PDF
  doc.save(`tenadam-payment-receipt-${receiptNumber.value}.pdf`)
}

// Convert number to words (enhanced version)
const convertNumberToWords = (num) => {
  const ones = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine']
  const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen']
  const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety']
  
  const convertHundreds = (n) => {
    let result = ''
    
    if (n >= 100) {
      result += ones[Math.floor(n / 100)] + ' Hundred'
      n %= 100
      if (n > 0) result += ' '
    }
    
    if (n >= 20) {
      result += tens[Math.floor(n / 10)]
      n %= 10
      if (n > 0) result += ' ' + ones[n]
    } else if (n >= 10) {
      result += teens[n - 10]
    } else if (n > 0) {
      result += ones[n]
    }
    
    return result
  }
  
  if (num === 0) return 'Zero'
  if (num < 0) return 'Negative ' + convertNumberToWords(-num)
  
  let result = ''
  
  if (num >= 1000000) {
    result += convertHundreds(Math.floor(num / 1000000)) + ' Million'
    num %= 1000000
    if (num > 0) result += ' '
  }
  
  if (num >= 1000) {
    result += convertHundreds(Math.floor(num / 1000)) + ' Thousand'
    num %= 1000
    if (num > 0) result += ' '
  }
  
  if (num > 0) {
    result += convertHundreds(num)
  }
  
  return result
}

// Generate receipt content for download
const generateReceiptContent = () => {
  return `
TENADAM TELEMEDICINE PLATFORM
Payment Receipt
================================

Receipt No: ${receiptNumber.value}
Transaction ID: ${props.paymentData.transactionId}
Date & Time: ${formatDate(props.paymentData.timestamp)}

Doctor: ${props.doctorName}
Service: Medical Consultation
Payment Method: ${getPaymentMethodDisplay(props.paymentMethod)}

Consultation Fee: $${props.amount}
Tax (0%): $0.00
--------------------------------
Total Paid: $${props.amount}

Status: PAID
Payment Confirmed

This is a digital receipt for your consultation payment.
Keep this for your records.

Thank you for using Tenadam Telemedicine Platform!
`
}

// Proceed to consultation
const proceedToConsultation = () => {
  emit('proceed-to-consultation')
  router.push({
    name: 'UserConsultationDetail',
    params: { id: props.consultationId }
  })
}
</script>

<style scoped>
@media print {
  .fixed {
    position: static !important;
  }
  
  .bg-black {
    background: transparent !important;
  }
  
  .shadow-xl {
    box-shadow: none !important;
  }
  
  button {
    display: none !important;
  }
}
</style>
