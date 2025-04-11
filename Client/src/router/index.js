import { createRouter, createWebHistory } from 'vue-router'
import WelcomePage from '../views/WelcomePage.vue'
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
import LabRequestList from '@/components/LabRequest/LabRequestList.vue'
import LabRequestView from '@/components/LabRequest/LabRequestView.vue'
import LabRequestEdit from '@/components/LabRequest/LabRequestEdit.vue'
import CreateLabRequest from '@/components/LabRequest/CreateLabRequest.vue'
import LabResultList from '@/components/LabResult/LabResultList.vue'
import LabResultView from '@/components/LabResult/LabResultView.vue'
import LabResultEdit from '@/components/LabResult/LabResultEdit.vue'
import CreateLabResult from '@/components/LabResult/CreateLabResult.vue'
import PrescriptionList from '@/components/Prescription/PrescriptionList.vue'
import PrescriptionView from '@/components/Prescription/PrescriptionView.vue'
import PrescriptionEdit from '@/components/Prescription/PrescriptionEdit.vue'
import CreatePrescription from '@/components/Prescription/CreatePrescription.vue'
import HomePage from '@/views/HomePage.vue'
import PaymentList from '@/components/Payment/PaymentList.vue'
import PaymentView from '@/components/Payment/PaymentView.vue'
import PaymentEdit from '@/components/Payment/PaymentEdit.vue'
import CreatePayment from '@/components/Payment/CreatePayment.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Welcome',
      component: WelcomePage,
    },
    {
      path: '/home',
      name: 'Home',
      component: HomePage,
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
    {
      path: '/lab-requests',
      name: 'LabRequests',
      component: LabRequestList,
    },
    {
      path: '/lab-requests/:id',
      name: 'LabRequestView',
      component: LabRequestView,
    },
    {
      path: '/lab-requests/:id/edit',
      name: 'LabRequestEdit',
      component: LabRequestEdit,
    },
    {
      path: '/lab-requests/create',
      name: 'CreateLabRequest',
      component: CreateLabRequest,
    },
    {
      path: '/lab-results',
      name: 'LabResults',
      component: LabResultList,
    },
    {
      path: '/lab-results/:id',
      name: 'LabResultView',
      component: LabResultView,
    },
    {
      path: '/lab-results/:id/edit',
      name: 'LabResultEdit',
      component: LabResultEdit,
    },
    {
      path: '/lab-results/create',
      name: 'CreateLabResult',
      component: CreateLabResult,
    },
    {
      path: '/prescriptions',
      name: 'Prescriptions',
      component: PrescriptionList,
    },
    {
      path: '/prescriptions/:id',
      name: 'PrescriptionView',
      component: PrescriptionView,
    },
    {
      path: '/prescriptions/:id/edit',
      name: 'PrescriptionEdit',
      component: PrescriptionEdit,
    },
    {
      path: '/prescriptions/create',
      name: 'CreatePrescription',
      component: CreatePrescription,
    }, {
      path: '/payments',
      name: 'Payments',
      component: PaymentList,
    },
    {
      path: '/payments/:id',
      name: 'PaymentView',
      component: PaymentView,
    },
    {
      path: '/payments/:id/edit',
      name: 'PaymentEdit',
      component: PaymentEdit,
    },
    {
      path: '/payments/create',
      name: 'CreatePayment',
      component: CreatePayment,
    },

  ],
})

export default router
