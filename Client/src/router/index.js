import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AppointmentList from '@/components/Appointment/AppointmentList.vue'
import CreateAppointment from '@/components/Appointment/CreateAppointment.vue'
import AppointmentView from '@/components/Appointment/AppointmentView.vue'
import AppointmentEdit from '@/components/Appointment/AppointmentEdit.vue'



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView,
    },
    {
      path: '/appointments',
      name: 'Appointments',
      component: AppointmentList,
    },
    {
      path: '/appointments/:id/edit',
      name: 'AppointmentEdit',
      component: AppointmentEdit,
    },
    {
      path: '/appointments/create',
      name: 'CreateAppointment',
      component: CreateAppointment,
    },
    {
      path: '/appointments/:id',
      name: 'AppointmentView',
      component: AppointmentView,
    }

  ],
})

export default router
