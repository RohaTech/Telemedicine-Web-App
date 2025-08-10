<script setup>
import { useLaboratoryStore } from "@/stores/laboratoryStore";
import UserLayout from "@/layout/UserLayout.vue";
const laboratoryStore = useLaboratoryStore();

const handleSubmit = async () => {
  const result = await laboratoryStore.createLaboratory();
  if (result.success) {
    alert(result.message);
  }
};
</script>
<template>
  <UserLayout>
    <div
      class="mx-auto max-w-2xl rounded-md border border-gray-300 bg-gray-50 p-5"
    >
      <h1 class="mb-6 text-2xl font-bold">Create Laboratory</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="name" class="mb-1 block font-semibold">Name:</label>
          <input
            type="text"
            id="name"
            v-model="laboratoryStore.laboratory.name"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="laboratoryStore.errors.name"
            class="mt-1 text-sm text-red-600"
          >
            {{ laboratoryStore.errors.name }}
          </p>
        </div>
        <div class="mb-4">
          <label for="email" class="mb-1 block font-semibold">Email:</label>
          <input
            type="email"
            id="email"
            v-model="laboratoryStore.laboratory.email"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="laboratoryStore.errors.email"
            class="mt-1 text-sm text-red-600"
          >
            {{ laboratoryStore.errors.email }}
          </p>
        </div>
        <div class="mb-4">
          <label for="password" class="mb-1 block font-semibold"
            >Password:</label
          >
          <input
            type="password"
            id="password"
            v-model="laboratoryStore.laboratory.password"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="laboratoryStore.errors.password"
            class="mt-1 text-sm text-red-600"
          >
            {{ laboratoryStore.errors.password }}
          </p>
        </div>
        <div class="mb-4">
          <label for="password_confirmation" class="mb-1 block font-semibold"
            >Confirm Password:</label
          >
          <input
            type="password"
            id="password_confirmation"
            v-model="laboratoryStore.laboratory.password_confirmation"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="laboratoryStore.errors.password_confirmation"
            class="mt-1 text-sm text-red-600"
          >
            {{ laboratoryStore.errors.password_confirmation }}
          </p>
        </div>
        <div class="mb-4">
          <label for="phone" class="mb-1 block font-semibold">Phone:</label>
          <input
            type="text"
            id="phone"
            v-model="laboratoryStore.laboratory.phone"
            required
            class="w-full rounded-md border border-gray-300 p-2"
          />
          <p
            v-if="laboratoryStore.errors.phone"
            class="mt-1 text-sm text-red-600"
          >
            {{ laboratoryStore.errors.phone }}
          </p>
        </div>
        <div class="mb-4">
          <label for="address" class="mb-1 block font-semibold">Address:</label>
          <textarea
            id="address"
            v-model="laboratoryStore.laboratory.address"
            required
            class="w-full rounded-md border border-gray-300 p-2"
            rows="4"
          ></textarea>
          <p
            v-if="laboratoryStore.errors.address"
            class="mt-1 text-sm text-red-600"
          >
            {{ laboratoryStore.errors.address }}
          </p>
        </div>
        <button
          type="submit"
          class="rounded-md bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
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
