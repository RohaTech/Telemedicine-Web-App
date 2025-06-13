<script setup>
import { ref } from "vue";
import { useToast } from "vue-toastification"; // Import useToast
import LaboratoryLayout from "@/layout/LaboratoryLayout.vue";
import LabRequestIndexTable from "./LabRequestIndexTable.vue";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";

const currentPageTitle = ref("Lab Requests");
// const toast = useToast();
// const triggerCustomToast = () => {
//   toast.success("You clicked the toast button!", {});
// };
</script>

<template>
  <LaboratoryLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 xl:px-10 xl:py-12"
    >
      <div class="mx-auto w-full text-center">
        <h3 class="mb-4 text-theme-xl font-semibold text-gray-800 sm:text-2xl">
          Lab Requests
        </h3>

        <LabRequestIndexTable />
      </div>
    </div>
  </LaboratoryLayout>
</template>
