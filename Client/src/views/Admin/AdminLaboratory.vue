
<script setup>
import { ref } from "vue";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
import AdminLayout from "@/layout/AdminLayout.vue";
import ComponentCard from "@/components/common/ComponentCard.vue";
import LaboratoryIndexTable from "@/components/AdminLaboratory/LaboratoryIndexTable.vue";
const currentPageTitle = ref("Basic Tables");
</script>
<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Basic Table 1">
      <LaboratoryIndexTable/>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>


