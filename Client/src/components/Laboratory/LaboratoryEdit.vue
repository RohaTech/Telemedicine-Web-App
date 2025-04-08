<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Laboratory\LaboratoryEdit.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Edit Laboratory</h1>
      <form @submit.prevent="handleSubmit" v-if="laboratory">
        <div class="mb-4">
          <label for="name" class="block mb-1 font-semibold">Name:</label>
          <input
            type="text"
            id="name"
            v-model="laboratory.name"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="laboratoryStore.errors.name" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.name }}
          </p>
        </div>
        <div class="mb-4">
          <label for="email" class="block mb-1 font-semibold">Email:</label>
          <input
            type="email"
            id="email"
            v-model="laboratory.email"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="laboratoryStore.errors.email" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.email }}
          </p>
        </div>
        <div class="mb-4">
          <label for="password" class="block mb-1 font-semibold">Password (leave blank to keep current):</label>
          <input
            type="password"
            id="password"
            v-model="laboratory.password"
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="laboratoryStore.errors.password" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.password }}
          </p>
        </div>
        <div class="mb-4">
          <label for="phone" class="block mb-1 font-semibold">Phone:</label>
          <input
            type="text"
            id="phone"
            v-model="laboratory.phone"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="laboratoryStore.errors.phone" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.phone }}
          </p>
        </div>
        <div class="mb-4">
          <label for="address" class="block mb-1 font-semibold">Address:</label>
          <textarea
            id="address"
            v-model="laboratory.address"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
            rows="4"
          ></textarea>
          <p v-if="laboratoryStore.errors.address" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.address }}
          </p>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors mr-2"
        >
          Update Laboratory
        </button>
        <router-link
          to="/laboratories"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Cancel
        </router-link>
      </form>
      <div v-if="laboratoryStore.errors.message" class="mt-4 text-red-600">
        <p>{{ laboratoryStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useLaboratoryStore } from '@/stores/laboratoryStore';
import UserLayout from '@/layout/UserLayout.vue';
const route = useRoute();
const laboratoryStore = useLaboratoryStore();
const laboratory = ref(null);

const fetchLaboratory = async () => {
  const result = await laboratoryStore.getLaboratory(route.params.id);
  if (result.success) {
    laboratory.value = { ...result.data, password: '' }; // Don't prefill password
  }
};

const handleSubmit = async () => {
  const updateData = { ...laboratory.value };
  if (!updateData.password) delete updateData.password; // Don't send empty password
  const result = await laboratoryStore.updateLaboratory(route.params.id, updateData);
  if (result.success) {
    alert(result.message);
    router.push({ name: 'Laboratories' });
  }
};

onMounted(fetchLaboratory);
</script>