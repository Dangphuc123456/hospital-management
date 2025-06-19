import { createRouter, createWebHistory } from 'vue-router'

// Layouts
import MainLayout from '../components/layouts/MainLayout.vue'
import AuthLayout from '../components/layouts/AuthLayout.vue'


import Dashboard from '../pages/Dashboard.vue'
import Login from '../pages/auth/Login.vue'
import Register from '../pages/auth/Register.vue'
import AppointmentList from '../pages/appointments/AppointmentList.vue'
import DepartmentList from '../pages/departments/DepartmentList.vue'
import ScheduleList from '../pages/schedules/ScheduleList.vue'
import MedicalRecordList from '../pages/medical-records/MedicalRecordList.vue'
import InvoiceList from '../pages/invoices/InvoiceList.vue'
import DoctorList from '../pages/doctors/DoctorList.vue'
import PatientList from '../pages/patients/PatientList.vue'
import PrescriptionList from '../pages/prescriptions/PrescriptionList.vue'
import UserList from '../pages/users/UserList.vue'
import PaymentList from '../pages/payments/PaymentList.vue'
import MedicationList from '../pages/medications/MedicationList.vue'
import PatientDetail from '../pages/patients/PatientDetail.vue'
// import các trang khác nếu cần

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth layout
    {
      path: '/auth',
      component: AuthLayout,
      children: [
        { path: 'login', name: 'login', component: Login },
        { path: 'register', name: 'register', component: Register },
      ],
    },

    // Main layout
    {
      path: '/',
      component: MainLayout,
      children: [
        { path: '', name: 'dashboard', component: Dashboard },
        { path: 'appointments', name: 'appointments', component: AppointmentList },
        { path: 'departments', name: 'departments', component: DepartmentList },
        { path: 'schedules', name: 'schedules', component: ScheduleList },
        { path: 'medical-records', name: 'medical-records', component: MedicalRecordList },
        { path: 'invoices', name: 'invoices', component: InvoiceList },
        { path: 'doctors', name: 'doctors', component: DoctorList },
        { path: 'patients', name: 'patients', component: PatientList },
        { path: 'patients/:id', name: 'patient-detail', component: PatientDetail, props: true },
        { path: 'prescriptions', name: 'prescriptions', component: PrescriptionList },
        { path: 'users', name: 'users', component: UserList },
        { path: 'payments', name: 'payments', component: PaymentList },
        { path: 'medications', name: 'medications', component: MedicationList },
        
      ],
    },

    // Redirect all unknown routes to dashboard
    {
      path: '/:pathMatch(.*)*',
      redirect: '/',
    },
  ],
})

export default router
