<script setup>
import { ref } from "vue";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
import AdminLayout from "@/layout/AdminLayout.vue";
import ComponentCard from "@/components/common/ComponentCard.vue";
import DoctorIndexTable from "@/components/AdminDoctor/DoctorIndexTable.vue";

const currentPageTitle = ref("Doctors");
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="List of Doctors">
        <DoctorIndexTable />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>
