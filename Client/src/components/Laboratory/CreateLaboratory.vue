<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Laboratory\CreateLaboratory.vue -->
<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto p-5 border border-gray-300 rounded-md bg-gray-50">
      <h1 class="text-2xl font-bold mb-6">Create Laboratory</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="name" class="block mb-1 font-semibold">Name:</label>
          <input
            type="text"
            id="name"
            v-model="laboratoryStore.laboratory.name"
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
            v-model="laboratoryStore.laboratory.email"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="laboratoryStore.errors.email" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.email }}
          </p>
        </div>
        <div class="mb-4">
          <label for="password" class="block mb-1 font-semibold">Password:</label>
          <input
            type="password"
            id="password"
            v-model="laboratoryStore.laboratory.password"
            required
            class="w-full p-2 border border-gray-300 rounded-md"
          />
          <p v-if="laboratoryStore.errors.password" class="text-red-600 text-sm mt-1">
            {{ laboratoryStore.errors.password }}
          </p>
        </div>
        <div class="mb-4">
        <label for="password_confirmation" class="block mb-1 font-semibold">Confirm Password:</label>
        <input
          type="password"
          id="password_confirmation"
          v-model="laboratoryStore.laboratory.password_confirmation"
          required
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="laboratoryStore.errors.password_confirmation" class="text-red-600 text-sm mt-1">
          {{ laboratoryStore.errors.password_confirmation }}
        </p>
      </div>
        <div class="mb-4">
          <label for="phone" class="block mb-1 font-semibold">Phone:</label>
          <input
            type="text"
            id="phone"
            v-model="laboratoryStore.laboratory.phone"
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
            v-model="laboratoryStore.laboratory.address"
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
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Create Laboratory
        </button>
      </form>
      <div v-if="laboratoryStore.errors.message" class="mt-4 text-red-600">
        <p>{{ laboratoryStore.errors.message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { useLaboratoryStore } from '@/stores/laboratoryStore';
import UserLayout from '@/layout/UserLayout.vue';
const laboratoryStore = useLaboratoryStore();

const handleSubmit = async () => {
  const result = await laboratoryStore.createLaboratory();
  if (result.success) {
    alert(result.message);
  }
};
</script>