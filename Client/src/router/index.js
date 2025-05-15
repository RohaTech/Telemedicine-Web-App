import { createRouter, createWebHistory } from "vue-router";
import AppointmentList from "@/components/Appointment/AppointmentList.vue";
import CreateAppointment from "@/components/Appointment/CreateAppointment.vue";
import AppointmentView from "@/components/Appointment/AppointmentView.vue";
import AppointmentEdit from "@/components/Appointment/AppointmentEdit.vue";
import ConsultationList from "@/components/Consultation/ConsultationList.vue";
import ConsultationView from "@/components/Consultation/ConsultationView.vue";
import ConsultationEdit from "@/components/Consultation/ConsultationEdit.vue";
import CreateConsultation from "@/components/Consultation/CreateConsultation.vue";
import LaboratoryList from "@/components/Laboratory/LaboratoryList.vue";
import LaboratoryView from "@/components/Laboratory/LaboratoryView.vue";
import LaboratoryEdit from "@/components/Laboratory/LaboratoryEdit.vue";
import CreateLaboratory from "@/components/Laboratory/CreateLaboratory.vue";
import LabRequestList from "@/components/LabRequest/LabRequestList.vue";
import LabRequestView from "@/components/LabRequest/LabRequestView.vue";
import LabRequestEdit from "@/components/LabRequest/LabRequestEdit.vue";
import CreateLabRequest from "@/components/LabRequest/CreateLabRequest.vue";
import LabResultList from "@/components/LabResult/LabResultList.vue";
import LabResultView from "@/components/LabResult/LabResultView.vue";
import LabResultEdit from "@/components/LabResult/LabResultEdit.vue";
import CreateLabResult from "@/components/LabResult/CreateLabResult.vue";
import PrescriptionList from "@/components/Prescription/PrescriptionList.vue";
import PrescriptionView from "@/components/Prescription/PrescriptionView.vue";
import PrescriptionEdit from "@/components/Prescription/PrescriptionEdit.vue";
import CreatePrescription from "@/components/Prescription/CreatePrescription.vue";
import HomePage from "@/views/HomePage.vue";
import PaymentList from "@/components/Payment/PaymentList.vue";
import PaymentView from "@/components/Payment/PaymentView.vue";
import PaymentEdit from "@/components/Payment/PaymentEdit.vue";
import CreatePayment from "@/components/Payment/CreatePayment.vue";
import NotificationList from "@/components/Notification/NotificationList.vue";
import NotificationView from "@/components/Notification/NotificationView.vue";
import NotificationEdit from "@/components/Notification/NotificationEdit.vue";
import CreateNotification from "@/components/Notification/CreateNotification.vue";
import LaboratoryRegister from "@/views/Laboratory/LaboratoryRegister.vue";
import AdminHome from "@/views/Admin/AdminHome.vue";
import AdminLaboratory from "@/views/Admin/AdminLaboratory.vue";
import { useAuthStore } from "@/stores/auth";
import GetStarted from "@/views/Auth/GetStarted.vue";
import PatientRegisterPage from "@/views/Auth/PatientRegisterPage.vue";
import LoginPage from "@/views/Auth/LoginPage.vue";
import DoctorRegistration from "@/views/Doctor/DoctorRegistration.vue";
import DoctorStatusPage from "@/views/Doctor/DoctorStatusPage.vue";
import LaboratoryStatusPage from "@/views/Laboratory/LaboratoryStatusPage.vue";
import AdminDoctor from "@/views/Admin/AdminDoctor.vue";
import LaboratoryLoginPage from "@/views/Laboratory/LaboratoryLoginPage.vue";
import LaboratoryHome from "@/views/Laboratory/LaboratoryHome.vue";
import DoctorHomePage from "@/views/Doctor/DoctorHomePage.vue";
import DoctorAppointment from "@/views/Doctor/DoctorAppointment.vue";
import LaboratoryProfilePage from "@/views/Laboratory/LaboratoryProfilePage.vue";
import WelcomePage from "../views/WelcomePage.vue";
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "Welcome",
      component: WelcomePage,
      meta: { welcome: true },
    },
    {
      path: "/home",
      name: "Home",
      component: HomePage,
      meta: { auth: true, home: true },
    },
    {
      path: "/get-started",
      name: "GetStarted",
      component: GetStarted,
      meta: { GetStarted: true },
    },
    {
      path: "/patient-register",
      name: "PatientRegister",
      component: PatientRegisterPage,
      meta: { guest: true },
    },
    {
      path: "/doctor-register",
      name: "DoctorRegister",
      component: DoctorRegistration,
      meta: { guest: true },
    },
    {
      path: "/doctor-status",
      name: "DoctorStatus",
      component: DoctorStatusPage,
      meta: { auth: true },
    },
    {
      path: "/login",
      name: "Login",
      component: LoginPage,
      meta: { guest: true },
    },

    {
      path: "/appointments",
      name: "Appointments",
      component: AppointmentList,
    },
    {
      path: "/appointments/:id/edit",
      name: "AppointmentEdit",
      component: AppointmentEdit,
    },
    {
      path: "/appointments/create",
      name: "CreateAppointment",
      component: CreateAppointment,
    },
    {
      path: "/appointments/:id",
      name: "AppointmentView",
      component: AppointmentView,
    },
    {
      path: "/doctor/appointments",
      name: "DoctorAppointments",
      component: DoctorAppointment,
    },
    {
      path: "/consultations",
      name: "Consultations",
      component: ConsultationList,
    },
    {
      path: "/consultations/:id",
      name: "ConsultationView",
      component: ConsultationView,
    },
    {
      path: "/consultations/:id/edit",
      name: "ConsultationEdit",
      component: ConsultationEdit,
    },
    {
      path: "/consultations/create",
      name: "CreateConsultation",
      component: CreateConsultation,
    },
    {
      path: "/laboratories",
      name: "Laboratories",
      component: LaboratoryList,
    },
    {
      path: "/laboratories/profile/:id",
      name: "LaboratoryProfile",
      component: LaboratoryProfilePage,
      meta: { laboratory: true }
    },
    {
      path: "/laboratories/:id",
      name: "LaboratoryHome",
      component: LaboratoryHome,
      meta: { laboratory: true }
    },
    {
      path: "/laboratories/:id/edit",
      name: "LaboratoryEdit",
      component: LaboratoryEdit,
    },
    {
      path: "/laboratories/create",
      name: "CreateLaboratory",
      component: CreateLaboratory,
    },
    {
      path: "/lab-requests",
      name: "LabRequests",
      component: LabRequestList,
    },
    {
      path: "/lab-requests/:id",
      name: "LabRequestView",
      component: LabRequestView,
    },
    {
      path: "/lab-requests/:id/edit",
      name: "LabRequestEdit",
      component: LabRequestEdit,
    },
    {
      path: "/lab-requests/create",
      name: "CreateLabRequest",
      component: CreateLabRequest,
      meta: { laboratory: true }

    },
    {
      path: "/lab-results",
      name: "LabResults",
      component: LabResultList,
    },
    {
      path: "/lab-results/:id",
      name: "LabResultView",
      component: LabResultView,
    },
    {
      path: "/lab-results/:id/edit",
      name: "LabResultEdit",
      component: LabResultEdit,
    },
    {
      path: "/lab-results/create",
      name: "CreateLabResult",
      component: CreateLabResult,
    },
    {
      path: "/laboratory/register",
      name: "LaboratoryRegister",
      component: LaboratoryRegister,
      meta: { guest: true },
    },
    {
      path: "/laboratory/status",
      name: "LaboratoryStatusPage",
      component: LaboratoryStatusPage,
      meta: { laboratoryStatus: true },
    },
    {
      path: "/laboratory/register",
      name: "LaboratoryRegister",
      component: LaboratoryRegister,
    },
    {
      path: "/laboratory/login",
      name: "LaboratoryLoginPage",
      component: LaboratoryLoginPage,
    },
    {
      path: "/prescriptions",
      name: "Prescriptions",
      component: PrescriptionList,
    },
    {
      path: "/prescriptions/:id",
      name: "PrescriptionView",
      component: PrescriptionView,
    },
    {
      path: "/prescriptions/:id/edit",
      name: "PrescriptionEdit",
      component: PrescriptionEdit,
    },
    {
      path: "/prescriptions/create",
      name: "CreatePrescription",
      component: CreatePrescription,
    },
    {
      path: "/payments",
      name: "Payments",
      component: PaymentList,
    },
    {
      path: "/payments/:id",
      name: "PaymentView",
      component: PaymentView,
    },
    {
      path: "/payments/:id/edit",
      name: "PaymentEdit",
      component: PaymentEdit,
    },
    {
      path: "/payments/create",
      name: "CreatePayment",
      component: CreatePayment,
    },
    {
      path: "/notifications",
      name: "Notifications",
      component: NotificationList,
    },
    {
      path: "/notifications/:id",
      name: "NotificationView",
      component: NotificationView,
    },
    {
      path: "/notifications/:id/edit",
      name: "NotificationEdit",
      component: NotificationEdit,
    },
    {
      path: "/notifications/create",
      name: "CreateNotification",
      component: CreateNotification,
    },
    {
      path: "/admin/home",
      name: "AdminHome",
      component: AdminHome,
    },
    {
      path: "/admin/laboratories",
      name: "AdminLaboratories",
      component: AdminLaboratory,
    },
    {
      path: "/admin/doctors",
      name: "AdminDoctors",
      component: AdminDoctor,
    },
    {
      path: "/doctor/home",
      name: "DoctorHome",
      component: DoctorHomePage,
      meta: { requiresAuth: true, role: "doctor" },
    },
  ],
});

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore();

  await authStore.getUser();
  // console.log(authStore.user);

  if (authStore.user?.role === "admin" && to.meta.welcome) {
    return { name: "AdminHome" };
  }
  if (authStore.user?.role === "admin" && to.meta.auth) {
    return { name: "AdminHome" };
  }
  if (authStore.user?.role === "doctor" && to.meta.welcome) {
    return { name: "DoctorHome" };
  }
  if (authStore.user?.role === "doctor" && to.meta.home) {
    return { name: "DoctorHome" };
  }
  if (authStore.user?.status === "pending" && to.name !== "LaboratoryStatusPage") {
    return { name: "LaboratoryStatusPage" };
  }

  if (authStore.user?.status === "active" && authStore.user.tests && !to.meta.laboratory) {
    return { name: "LaboratoryHome", params: { id: authStore.user.id } };
  }

  if (authStore.user && to.meta.welcome) {
    return { name: "Home" };
  }
  if (authStore.user?.role === "admin" && to.meta.welcome) {
    return { name: "AdminHome" };
  }
  if (!authStore.user && to.meta.Home) {
    return { name: "Welcome" };
  }
  if (!authStore.user && to.meta.auth) {
    return { name: "GetStarted" };
  }
  if (!authStore.user && to.meta.laboratory) {
    return { name: "GetStarted" };
  }
});

export default router;
