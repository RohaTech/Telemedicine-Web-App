<script setup>
import AdminLayout from "@/layout/AdminLayout.vue";
import MonthlyTarget from "@/components/Ecommerce/MonthlyTarget.vue";
import EcommerceMetrics from "@/components/Ecommerce/EcommerceMetrics.vue";
import MonthlySale from "@/components/Ecommerce/MonthlySale.vue";
import StatisticsChart from "@/components/Ecommerce/StatisticsChart.vue";
import RecentOrders from "@/components/Ecommerce/RecentOrders.vue";
</script>
<template>
  <admin-layout>
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <EcommerceMetrics/>
        <MonthlySale />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <MonthlyTarget />
      </div>

      <div class="col-span-12">
        <StatisticsChart />
      </div>

      <div class="col-span-12 xl:col-span-5">
        
      </div>

      <div class="col-span-12 xl:col-span-7">
        <RecentOrders />
      </div>
    </div>
  </admin-layout>
</template>


 