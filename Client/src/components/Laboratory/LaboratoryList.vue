<!-- filepath: e:\code files\Final Year\Telemedicine-Web-App\Client\src\views\Laboratory\LaboratoryList.vue -->
<template>
  <UserLayout>
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-2xl font-bold mb-6">Laboratories List</h1>
      <div v-if="laboratoryStore.errors.message" class="mb-4 text-red-600">
        {{ laboratoryStore.errors.message }}
      </div>
      <div v-if="laboratories.length === 0" class="text-gray-500">No laboratories found.</div>
      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">Email</th>
            <th class="p-2 text-left">Phone</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="laboratory in laboratories" :key="laboratory.id" class="border-b">
            <td class="p-2">{{ laboratory.id }}</td>
            <td class="p-2">{{ laboratory.name }}</td>
            <td class="p-2">{{ laboratory.email }}</td>
            <td class="p-2">{{ laboratory.phone }}</td>
            <td class="p-2">
              <router-link
                :to="{ name: 'LaboratoryView', params: { id: laboratory.id } }"
                class="text-blue-600 hover:underline mr-2"
              >
                View
              </router-link>
              <router-link
                :to="{ name: 'LaboratoryEdit', params: { id: laboratory.id } }"
                class="text-green-600 hover:underline mr-2"
              >
                Edit
              </router-link>
              <button
                @click="deleteLaboratory(laboratory.id)"
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
import { useLaboratoryStore } from '@/stores/laboratoryStore';
import UserLayout from '@/layout/UserLayout.vue';
const laboratoryStore = useLaboratoryStore();
const laboratories = ref([]);

const fetchLaboratories = async () => {
  const result = await laboratoryStore.getLaboratories();
  if (result.success) {
    laboratories.value = result.data;
  }
};

const deleteLaboratory = async (id) => {
  if (confirm('Are you sure you want to delete this laboratory?')) {
    const result = await laboratoryStore.deleteLaboratory(id);
    if (result.success) {
      laboratories.value = laboratories.value.filter((lab) => lab.id !== id);
    }
  }
};

onMounted(fetchLaboratories);
</script>