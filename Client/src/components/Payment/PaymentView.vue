<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Payment\PaymentView.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Payment Details</h1>
      <div v-if="paymentStore.errors.message" class="mb-4 text-red-600">
        {{ paymentStore.errors.message }}
      </div>
      <div v-if="payment">
        <div class="mb-4"><strong>ID:</strong> {{ payment.id }}</div>
        <div class="mb-4"><strong>Patient:</strong> {{ payment.patient ? payment.patient.name : 'N/A' }}</div>
        <div class="mb-4"><strong>Doctor:</strong> {{ payment.doctor ? payment.doctor.name : 'N/A' }}</div>
        <div class="mb-4"><strong>Amount:</strong> {{ payment.amount }}</div>
        <div class="mb-4"><strong>Payment Method:</strong> {{ payment.payment_method }}</div>
        <div class="mb-4"><strong>Status:</strong> {{ payment.status }}</div>
        <div class="mb-4"><strong>Transaction ID:</strong> {{ payment.transaction_id || 'N/A' }}</div>
        <router-link
          to="/payments"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Back to List
        </router-link>
      </div>
      <div v-else class="text-gray-500">Loading payment...</div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePaymentStore } from '@/stores/paymentStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const paymentStore = usePaymentStore();
const payment = ref(null);

const fetchPayment = async () => {
  const result = await paymentStore.getPayment(route.params.id);
  if (result.success) {
    payment.value = result.data;
  }
};

onMounted(fetchPayment);
</script>