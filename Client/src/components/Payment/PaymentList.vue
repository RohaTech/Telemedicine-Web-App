<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Payment\PaymentList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Payments List</h1>
      <div v-if="paymentStore.errors.message" class="mb-4 text-red-600">
        {{ paymentStore.errors.message }}
      </div>
      <div v-if="payments.length === 0" class="text-gray-500">No payments found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Patient</th>
            <th class="p-2 text-left">Doctor</th>
            <th class="p-2 text-left">Amount</th>
            <th class="p-2 text-left">Method</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-left">Transaction ID</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments" :key="payment.id" class="border-b">
            <td class="p-2">{{ payment.id }}</td>
            <td class="p-2">{{ payment.patient ? payment.patient.name : 'N/A' }}</td>
            <td class="p-2">{{ payment.doctor ? payment.doctor.name : 'N/A' }}</td>
            <td class="p-2">{{ payment.amount }}</td>
            <td class="p-2">{{ payment.payment_method }}</td>
            <td class="p-2">{{ payment.status }}</td>
            <td class="p-2">{{ payment.transaction_id || 'N/A' }}</td>
            <td class="p-2">
              <router-link
                :to="{ name: 'PaymentView', params: { id: payment.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'PaymentEdit', params: { id: payment.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deletePayment(payment.id)"
                class="text-red-600 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePaymentStore } from '@/stores/paymentStore';
import UserLayout from '@/layout/UserLayout.vue';
const paymentStore = usePaymentStore();
const payments = ref([]);

const fetchPayments = async () => {
  const result = await paymentStore.getPayments();
  if (result.success) {
    payments.value = result.data;
  }
};

const deletePayment = async (id) => {
  if (confirm('Are you sure you want to delete this payment?')) {
    const result = await paymentStore.deletePayment(id);
    if (result.success) {
      payments.value = payments.value.filter((p) => p.id !== id);
    }
  }
};

onMounted(fetchPayments);
</script>