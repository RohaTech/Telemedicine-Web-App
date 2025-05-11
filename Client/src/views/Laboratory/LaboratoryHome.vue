<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useLaboratoryStore } from "@/stores/laboratoryStore";
import LaboratoryLayout from "@/layout/LaboratoryLayout.vue";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
import LabRequestIndexTable from "./LabRequestIndexTable.vue";

const currentPageTitle = ref("Lab Requests");
const route = useRoute();
const laboratoryStore = useLaboratoryStore();
const laboratory = ref(null);

// onMounted(async () => {
//   laboratory.value = await laboratoryStore.getLaboratory(route.params.id);

//   // console.log(laboratory.value);
// });
</script>
<template>
  <LaboratoryLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 xl:px-10 xl:py-12"
    >
      <div class="mx-auto w-full text-center">
        <h3 class="mb-4 text-theme-xl font-semibold text-gray-800 sm:text-2xl">
          Open Lab Requests
        </h3>

        <LabRequestIndexTable />
      </div>
    </div>
  </LaboratoryLayout>
</template>
