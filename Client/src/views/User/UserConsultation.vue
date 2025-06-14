<script setup>
import { onMounted, ref, computed } from "vue";
import UserLayout from "@/layout/UserLayout.vue";
import { useConsultationStore } from "@/stores/consultationStore";
import { useConsultationPaymentStore } from "@/stores/consultationPaymentStore";
import PaymentModal from "@/components/Payment/PaymentModal.vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import paymentService from "@/services/paymentService";

const { getUserConsultations } = useConsultationStore();
const paymentStore = useConsultationPaymentStore();
const router = useRouter();
const toast = useToast();

const consultations = ref([]);
const showPaymentModal = ref(false);
const selectedConsultation = ref(null);
const paymentStatuses = ref(new Map());

const statusOptions = ["confirmed", "pending", "cancelled", "waiting"];
const selectedStatus = ref("all"); // Default to 'all'

// Helper function to get consultation status based on date
const getConsultationStatus = (consultationDate) => {
  const today = new Date();
  const consDate = new Date(consultationDate);

  // Reset time to compare only dates
  today.setHours(0, 0, 0, 0);
  consDate.setHours(0, 0, 0, 0);

  if (consDate > today) {
    return "upcoming";
  } else if (consDate < today) {
    return "passed";
  } else {
    return "today";
  }
};

// Check if consultation has been paid for
const checkPaymentStatus = async (consultationId) => {
  if (paymentStatuses.value.has(consultationId)) {
    return paymentStatuses.value.get(consultationId);
  }

  try {
    const result = await paymentService.checkConsultationPayment(consultationId);
    if (result.success) {
      paymentStatuses.value.set(consultationId, result.isPaid);
      return result.isPaid;
    }
  } catch (error) {
    console.error('Error checking payment status:', error);
  }
  
  return false;
};

// Handle consultation access
const handleConsultationAccess = async (consultation) => {
  console.log('Consultation data:', consultation); // Debug log
  
  // Only check payment for today's consultations
  if (getConsultationStatus(consultation.consultation_date) === 'today') {
    const isPaid = await checkPaymentStatus(consultation.id);
    
    if (!isPaid) {
      // Show payment modal with proper null checks
      selectedConsultation.value = consultation;
      showPaymentModal.value = true;
      return;
    }
  }
  
  // If paid or not today's consultation, proceed to consultation
  router.push({
    name: 'UserConsultationDetail',
    params: { id: consultation.id },
  });
};

// Handle successful payment
const handlePaymentSuccess = async (paymentData) => {
  if (selectedConsultation.value) {
    // Update payment status
    paymentStatuses.value.set(selectedConsultation.value.id, true);
    
    toast.success('Payment successful! Redirecting to consultation...', {
      position: "top-right",
      timeout: 2000
    });
    
    // Navigate to consultation after short delay
    setTimeout(() => {
      router.push({
        name: 'UserConsultationDetail',
        params: { id: selectedConsultation.value.id },
      });
    }, 1000);
  }
};

// Close payment modal
const closePaymentModal = () => {
  showPaymentModal.value = false;
  selectedConsultation.value = null;
};

// Helper function to safely get consultation fee
const getConsultationFee = (consultation) => {
  try {
    console.log('Getting consultation fee for:', consultation); // Debug log
    
    // Try multiple possible paths for the consultation fee
    const fee = consultation?.doctor?.doctor?.payment || 
                consultation?.doctor?.payment ||
                null;
    
    console.log('Extracted fee:', fee); // Debug log
    
    if (fee && fee > 0) {
      return fee;
    }
    
    console.warn('No valid payment amount found for consultation, using fallback');
    return 50; // Only use fallback if no valid payment is found
  } catch (error) {
    console.error('Error getting consultation fee:', error);
    return 50; // Error fallback
  }
};

// Helper function to safely get doctor name
const getDoctorName = (consultation) => {
  try {
    return consultation?.doctor?.name || 
           consultation?.doctor?.user?.name || 
           'the Doctor'; // Default fallback
  } catch (error) {
    console.error('Error getting doctor name:', error);
    return 'the Doctor'; // Default fallback
  }
};

onMounted(async () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });  consultations.value = await getUserConsultations();
  console.log("consultations.value", consultations.value.data);
  
  // Debug: Log the structure of the first consultation
  if (consultations.value?.data && consultations.value.data.length > 0) {
    console.log("First consultation structure:", consultations.value.data[0]);
    console.log("Doctor data:", consultations.value.data[0]?.doctor);
  }
  
  // Pre-load payment statuses for today's consultations
  if (consultations.value?.data) {
    for (const consultation of consultations.value.data) {
      if (getConsultationStatus(consultation.consultation_date) === 'today') {
        await checkPaymentStatus(consultation.id);
      }
    }
  }
});
</script>

<template>
  <UserLayout>
    <div
      class="ark:border-gray-800 ark:bg-white/[0.03] bg- -400 mx-auto mt-16 min-h-fit max-w-[1600px] overflow-hidden rounded-xl border border-gray-200 bg-white pb-10 shadow-sm"
    >
      <!-- Status Filter -->
      <div class="border-b border-gray-200 p-4">
        <h3 class="my-4 mb-2 py-8 text-2xl font-semibold">
          All Your Reserved Consultations
        </h3>
      </div>

      <div class="custom-scrollbar max-w-full overflow-x-auto">
        <table class="min-w-full text-center">
          <thead>
            <tr class="ark:border-gray-70 0 border-b border-gray-200">
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Name
                </p>
              </th>

              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Email
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Doctor Phone
                </p>
              </th>
              <th class="w-2/11 text- px-5 py-3 sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">
                  Consultation Date
                </p>
              </th>
              <th class="w-2/11 px-5 py-3 text-center sm:px-6">
                <p class="text-theme-xs font-medium text-gray-500">Action</p>
              </th>
            </tr>
          </thead>
          <tbody class="ark:divide-gray-700 divide-y divide-gray-200">
            <!-- No results message -->
            <tr v-if="!consultations">
              <td colspan="7" class="px-5 py-8 text-center sm:px-6">
                <div class="flex flex-col items-center justify-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <p class="mt-2 text-base text-gray-500">
                    No Reserved Consultation found
                  </p>
                </div>
              </td>
            </tr>
            <tr
              v-else
              v-for="(consultation, index) in consultations.data"
              :key="index"
              class="ark:border-gray-800 border-t border-gray-100"
            >
              <td class="px-5 py-4 sm:px-6">
                <div class="gap-3">
                  <span class="block text-theme-sm font-medium text-gray-800">
                    {{ consultation.doctor?.name }}
                  </span>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-theme-sm text-gray-500">
                  {{ consultation.doctor?.email }}
                </p>
              </td>
              <td class="px-5 py-4 text-center sm:px-6">
                <div class=" ">
                  {{ consultation.doctor?.phone }}
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                {{ consultation.consultation_date }}
              </td>              <td class="px-5 py-4 sm:px-6">
                <!-- Today's consultation - Payment required button -->
                <button
                  @click="handleConsultationAccess(consultation)"
                  v-if="
                    getConsultationStatus(consultation.consultation_date) ===
                    'today'
                  "
                  class="ark:bg-gray-800 ark:text-white rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center space-x-2"
                >
                  <span v-if="paymentStatuses.get(consultation.id)">Go to Consultation</span>
                  <span v-else>
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Pay & Start Consultation
                  </span>
                </button>

                <!-- Future consultation - Disabled state -->
                <span
                  v-else-if="
                    getConsultationStatus(consultation.consultation_date) ===
                    'upcoming'
                  "
                  class="rounded-md bg-yellow-100 px-4 py-2 text-sm font-medium text-yellow-800"
                >
                  Please Wait
                </span>

                <!-- Past consultation - Disabled state -->
                <span
                  v-else
                  class="rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600"
                >
                  Consultation Ended
                </span>
              </td>
            </tr>
          </tbody>
        </table>      </div>      <!-- Payment Modal -->
      <PaymentModal
        :show="showPaymentModal"
        :consultation-id="selectedConsultation?.id"
        :consultation-fee="getConsultationFee(selectedConsultation)"
        :doctor-name="getDoctorName(selectedConsultation)"
        @close="closePaymentModal"
        @payment-success="handlePaymentSuccess"
      />
    </div>
  </UserLayout>
</template>
