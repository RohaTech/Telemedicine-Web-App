<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Payment\CreatePayment.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Create Payment</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="patient_id" class="block mb-1 font-semibold">Patient ID:</label>
          <input
            type="number"
            id="patient_id"
            v-model="paymentStore.payment.patient_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="paymentStore.errors.patient_id" class="text-red-600 text-sm mt-1">
            {{ paymentStore.errors.patient_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="doctor_id" class="block mb-1 font-semibold">Doctor ID:</label>
          <input
            type="number"
            id="doctor_id"
            v-model="paymentStore.payment.doctor_id"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="paymentStore.errors.doctor_id" class="text-red-600 text-sm mt-1">
            {{ paymentStore.errors.doctor_id }}
          </p>
        </div>
        <div class="mb-4">
          <label for="amount" class="block mb-1 font-semibold">Amount:</label>
          <input
            type="number"
            id="amount"
            v-model="paymentStore.payment.amount"
            required
            step="0.01"
            min="0"
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="paymentStore.errors.amount" class="text-red-600 text-sm mt-1">
            {{ paymentStore.errors.amount }}
          </p>
        </div>
        <div class="mb-4">
          <label for="payment_method" class="block mb-1 font-semibold">Payment Method:</label>
          <select
            id="payment_method"
            v-model="paymentStore.payment.payment_method"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          >
            <option value="cash">Cash</option>
            <option value="credit_card">Credit Card</option>
            <option value="debit_card">Debit Card</option>
            <option value="online">Online</option>
          </select>
          <p v-if="paymentStore.errors.payment_method" class="text-red-600 text-sm mt-1">
            {{ paymentStore.errors.payment_method }}
          </p>
        </div>
        <div class="mb-4">
          <label for="status" class="block mb-1 font-semibold">Status:</label>
          <select
            id="status"
            v-model="paymentStore.payment.status"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          >
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="failed">Failed</option>
          </select>
          <p v-if="paymentStore.errors.status" class="text-red-600 text-sm mt-1">
            {{ paymentStore.errors.status }}
          </p>
        </div>
        <div class="mb-4">
          <label for="transaction_id" class="block mb-1 font-semibold">Transaction ID (optional):</label>
          <input
            type="text"
            id="transaction_id"
            v-model="paymentStore.payment.transaction_id"
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="paymentStore.errors.transaction_id" class="text-red-600 text-sm mt-1">
            {{ paymentStore.errors.transaction_id }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Create Payment
        </button>
      </form>
      <div v-if="paymentStore.errors.message" class="mt-4 text-red-600">
        {{ paymentStore.errors.message }}
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { usePaymentStore } from '@/stores/paymentStore';
import UserLayout from '@/layout/UserLayout.vue';
const paymentStore = usePaymentStore();

const handleSubmit = async () => {
  const result = await paymentStore.createPayment();
  if (result.success) {
    alert(result.message);
  }
};
</script>