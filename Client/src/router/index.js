import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AppointmentList from '@/components/Appointment/AppointmentList.vue'
import CreateAppointment from '@/components/Appointment/CreateAppointment.vue'
import AppointmentView from '@/components/Appointment/AppointmentView.vue'
import AppointmentEdit from '@/components/Appointment/AppointmentEdit.vue'
import ConsultationList from '@/components/Consultation/ConsultationList.vue'
import ConsultationView from '@/components/Consultation/ConsultationView.vue'
import ConsultationEdit from '@/components/Consultation/ConsultationEdit.vue'
import CreateConsultation from '@/components/Consultation/CreateConsultation.vue'
import LaboratoryList from '@/components/Laboratory/LaboratoryList.vue'
import LaboratoryView from '@/components/Laboratory/LaboratoryView.vue'
import LaboratoryEdit from '@/components/Laboratory/LaboratoryEdit.vue'
import CreateLaboratory from '@/components/Laboratory/CreateLaboratory.vue'


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
    }, 
    {
      path: '/consultations',
      name: 'Consultations',
      component: ConsultationList,
    },
    {
      path: '/consultations/:id',
      name: 'ConsultationView',
      component: ConsultationView,
    },
    {
      path: '/consultations/:id/edit',
      name: 'ConsultationEdit',
      component: ConsultationEdit,
    },
    {
      path: '/consultations/create',
      name: 'CreateConsultation',
      component: CreateConsultation,
    },
    {
      path: '/laboratories',
      name: 'Laboratories',
      component: LaboratoryList,
    },
    {
      path: '/laboratories/:id',
      name: 'LaboratoryView',
      component: LaboratoryView,
    },
    {
      path: '/laboratories/:id/edit',
      name: 'LaboratoryEdit',
      component: LaboratoryEdit,
    },
    {
      path: '/laboratories/create',
      name: 'CreateLaboratory',
      component: CreateLaboratory,
    },

  ],
})

export default router
