import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
import Home from '@/views/Home.vue'
import About from '@/views/About.vue'
import Services from '@/views/Services.vue'
import Project from '@/views/Projects.vue'
import Contact from '@/views/Contact.vue'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'

/*
|--------------------------------------------------------------------------
| Dashboard & Common Pages
|--------------------------------------------------------------------------
*/
import Dashboard from '@/views/Pages/Dashboard.vue'
import Attendace from '@/views/Pages/Attendance.vue'
import Communications from '@/views/Pages/Communications/Communications.vue'
import PendingApproval from '@/views/Pages/PendingApprovals.vue'
import PendingRequests from '@/views/Pages/PendingRequests.vue'
import Reports from '@/views/Pages/Reports.vue'
import EmployeeNotifications from '@/views/Pages/Employee/Notifications.vue'
import EmployeePayslipView from '@/views/Pages/Employee/PayslipView.vue'

/*
|--------------------------------------------------------------------------
| HR
|--------------------------------------------------------------------------
*/
import Accounts from '@/views/Pages/HR/Accounts/Accounts.vue'
import AccountDetail from '@/views/Pages/HR/Accounts/AccountDetail.vue'
import CreateAccount from '@/views/Pages/HR/Accounts/CreateAccount.vue'

import Recruitment from '@/views/Pages/HR/Recruitment/Recruitment.vue'
import RecruitmentWizardPage from '@/views/Pages/HR/Recruitment/RecruitmentWizardPage.vue'
import RecruitmentRequestDetail from '@/views/Pages/HR/Recruitment/RecruitmentRequestDetail.vue'

import Disciplinary from '@/views/Pages/HR/Disciplinary/Disciplinary.vue'
import DisciplinaryCreate from '@/views/Pages/HR/Disciplinary/DisciplinaryCreate.vue'
import DisciplinaryDetail from '@/views/Pages/HR/Disciplinary/DisciplinaryDetail.vue'
import EmployeeComplaints from '@/views/Pages/HR/Disciplinary/EmployeeComplaintsV2.vue'
import EmployeeComplaintsDetail from '@/views/Pages/HR/Disciplinary/EmployeeComplaintsDetailV2.vue'

import EmployeeClearance from '@/views/Pages/HR/Clearance/EmployeeClearance.vue'
import EmployeeClearanceDetail from '@/views/Pages/HR/Clearance/EmployeeClearanceDetail.vue'
import UpcomingExits from '@/views/Pages/HR/Clearance/UpcomingExits.vue'

import Timekeeping from '@/views/Pages/HR/Timekeeping/Timekeeping.vue'
import TimekeepingDetail from '@/views/Pages/HR/Timekeeping/TimekeepingDetail.vue'
import QRScanner from '@/components/HR/Attendance/QRScanner.vue'

import Payroll from '@/views/Pages/HR/Payroll/Payroll.vue'
import PayrollRunDetail from '@/views/Pages/HR/Payroll/PayrollRunDetail.vue'
import ComputedPayroll from '@/views/Pages/HR/Payroll/ComputedPayroll.vue'
import PayrollConfiguration from '@/views/Pages/HR/Payroll/PayrollConfiguration.vue'

import LeaveAndAbsence from '@/views/Pages/HR/Leave and Absence/LeaveAndAbsence.vue'
import LeaveRequestDetail from '@/views/Pages/HR/Leave and Absence/LeaveRequestDetail.vue'
import LeaveConfiguration from '@/views/Pages/HR/Leave and Absence/LeaveConfiguration.vue'

import PreviewReport from '@/views/Pages/HR/Reports/PreviewReport.vue'

/*
|--------------------------------------------------------------------------
| Finance
|--------------------------------------------------------------------------
*/

import GeneralLedger from '@/views/Pages/FRM/General Ledger/GeneralLedger.vue'
import Liabilities from '@/views/Pages/FRM/Liabilities/Liabilities.vue'

import Payables from '@/views/Pages/FRM/Payables/Payables.vue'

import Receivables from '@/views/Pages/FRM/Receivables/Receivables.vue'

import CashFlow from '@/views/Pages/FRM/Cashflow/CashFlow.vue'

import ProjectsList from '@/views/Pages/FRM/Project Finance/ProjectsList.vue'
import ProjectFinancialsView from '@/views/Pages/FRM/Project Finance/ProjectFinancialsView.vue'

import FinancialReportPreview from '@/views/Pages/FRM/Reports/FinancialReportPreview.vue'
import InvoicePaymentHistoryView from '@/views/Pages/FRM/InvoicePaymentHistoryView.vue'

import PendingApprovalsFinancePrebiddings from '@/views/Pages/FRM/Pending Approvals/PendingApprovalDetailPrebiddings.vue'

/*
|--------------------------------------------------------------------------
| CRM
|--------------------------------------------------------------------------
*/
import Clients from '@/views/Pages/CRM/Clients/Clients.vue'
import CreateClient from '@/views/Pages/CRM/Clients/CreateClient.vue'
import ClientDetail from '@/views/Pages/CRM/Clients/ClientDetail.vue'

import PreBiddings from '@/views/Pages/CRM/Prebiddings/PreBiddings.vue'
import CreatePreBidding from '@/views/Pages/CRM/Prebiddings/CreatePreBidding.vue'
import PreBiddingDetails from '@/views/Pages/CRM/Prebiddings/PreBiddingDetails.vue'

/*
|--------------------------------------------------------------------------
| Operations
|--------------------------------------------------------------------------
*/

import Projects from '@/views/Pages/Operations/Projects/Projects.vue'
import ProjectDetail from '@/views/Pages/Operations/Projects/ProjectDetail.vue'
import ProjectContractSetup from '@/views/Pages/Operations/Projects/ProjectContractSetup.vue'
import ProjectMonitoring from '@/views/Pages/Operations/Projects/ProjectMonitoring.vue'
import ProjectAttendanceView from '@/views/Pages/Operations/Projects/ProjectAttendanceView.vue'
import ProjectAttendanceDayView from '@/views/Pages/Operations/Projects/ProjectAttendanceDayView.vue'
import ProjectProgressReportView from '@/views/Pages/Operations/Projects/ProjectProgressReportView.vue'

import PendingApprovalsOperationsPrebiddings from '@/views/Pages/Operations/Pending Approvals/PendingApprovalDetailPrebiddings.vue' 

/*
|--------------------------------------------------------------------------
| Inventory / SCM
|--------------------------------------------------------------------------
*/
import Inventory from '@/views/Pages/SCM/Inventory/Inventory.vue'
import InventoryDetail from '@/views/Pages/SCM/Inventory/InventoryDetail.vue'

import Suppliers from '@/views/Pages/SCM/Suppliers/Suppliers.vue'

import MaterialRequests from '@/views/Pages/SCM/Material Request/MaterialRequests.vue'
import MaterialRequestDetail from '@/views/Pages/SCM/Material Request/MaterialRequestDetail.vue'

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

import AdminPendingApprovalDetail from '@/views/Pages/Admin/Pending Approvals/PendingApprovalDetail.vue'

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
*/
const routes = [
    // Public
    { path: '/', name: 'home', component: Home },
    { path: '/about', name: 'about', component: About },
    { path: '/services', name: 'services', component: Services },
    { path: '/project', name: 'project', component: Project },
    { path: '/contact', name: 'contact', component: Contact },
    { path: '/login', name: 'login', component: Login },
    { path: '/register', name: 'register', component: Register },

    // Dashboard & Common Pages
    { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/attendance', name: 'attendance', component: Attendace, meta: { requiresAuth: true } },
    { path: '/communications', name: 'communications', component: Communications, meta: { requiresAuth: true } },
    { path: '/pending-approvals', name: 'pending-approvals', component: PendingApproval , meta: { requiresAuth: true } },
    { path: '/pending-requests', name: 'pending-requests', component: PendingRequests, meta: { requiresAuth: true } },
    { path: '/reports', name: 'reports', component: Reports, meta: { requiresAuth: true } },
    { path: '/notifications', name: 'employee_notifications', component: EmployeeNotifications, meta: { requiresAuth: true } },
    { path: '/my-payslip/:id', name: 'my_payslip', component: EmployeePayslipView, meta: { requiresAuth: true } },

    /*
    |--------------------------------------------------------------------------
    | HR Routes
    |--------------------------------------------------------------------------
    */

    // Accounts
    { path: '/accounts', name: 'accounts', component: Accounts, meta: { requiresAuth: true, permission: 'Accounts.view' } },
    { path: '/accounts/create', name: 'create_account', component: CreateAccount, meta: { requiresAuth: true, permission: 'Accounts.manage' } },
    { path: '/accounts/:id', name: 'account_detail', component: AccountDetail, meta: { requiresAuth: true, permission: 'Accounts.view' } },

    // Recruitment 
    { path: '/recruitment', name: 'recruitment', component: Recruitment, meta: { requiresAuth: true } },
    { path: '/recruitment/:requestId', name: 'recruitment_request_detail', component: RecruitmentRequestDetail, meta: { requiresAuth: true } },
    { path: '/recruitment/:requestId/wizard', name: 'recruitment_wizard', component: RecruitmentWizardPage, meta: { requiresAuth: true } },

    // Disciplinary
    { path: '/disciplinary', name: 'disciplinary', component: Disciplinary, meta: { requiresAuth: true } },
    { path: '/disciplinary/add', name: 'disciplinary_add', component: DisciplinaryCreate, meta: { requiresAuth: true } },
    { path: '/disciplinary/:id', name: 'disciplinary_detail', component: DisciplinaryDetail, meta: { requiresAuth: true } },
    { path: '/disciplinary/complaints', name: 'employee_complaints', component: EmployeeComplaints, meta: { requiresAuth: true } },
    { path: '/disciplinary/complaints/:id', name: 'employee_complaints_detail', component: EmployeeComplaintsDetail, meta: { requiresAuth: true } },
    
    // Employee Clearance
    { path: '/employee-clearance', name: 'employee_clearance', component: EmployeeClearance, meta: { requiresAuth: true } },
    { path: '/employee-clearance/:id', name: 'employee_clearance_detail', component: EmployeeClearanceDetail, meta: { requiresAuth: true } },
    { path: '/employee-clearance/upcoming-exits', name: 'upcoming_exits', component: UpcomingExits, meta: { requiresAuth: true } },

    // Timekeeping
    { path: '/timekeeping', name: 'timekeeping', component: Timekeeping, meta: { requiresAuth: true } },
    { path: '/timekeeping/:date', name: 'timekeeping_detail', component: TimekeepingDetail, meta: { requiresAuth: true } },
    { path: '/attendance/scan', name: 'attendance_scan', component: QRScanner, meta: { requiresAuth: true } },

    // Payroll
    { path: '/payroll', name: 'payroll', component: Payroll, meta: { requiresAuth: true } },
    { path: '/payroll/configuration', name: 'payroll_configuration', component: PayrollConfiguration, meta: { requiresAuth: true } },
    { path: '/payroll/runs/:id', name: 'payroll_run_detail', component: PayrollRunDetail, meta: { requiresAuth: true } },
    { path: '/payroll/runs/:id/view', name: 'payroll_run_view', component: PayrollRunDetail, meta: { requiresAuth: true } },
    { path: '/payroll/runs/:id/computed', name: 'computed_payroll', component: ComputedPayroll, meta: { requiresAuth: true } },

    // Leave & Absence
    { path: '/leave-and-absence', name: 'leave_and_absence', component: LeaveAndAbsence, meta: { requiresAuth: true } },
    { path: '/leave-and-absence/configuration', name: 'leave_configuration', component: LeaveConfiguration, meta: { requiresAuth: true } },
    { path: '/leave-and-absence/:id', name: 'leave_request_detail', component: LeaveRequestDetail, meta: { requiresAuth: true } },

    // Reports 
    { path: '/hr/reports/:reportId/preview', name: 'hr_report_preview', component: PreviewReport, meta: { requiresAuth: true, department: 'HR' } },

    /*
    |--------------------------------------------------------------------------
    | Finance Routes
    |--------------------------------------------------------------------------
    */

    // Project Finance
    { path: '/project-finance', name: 'project_finance', component: ProjectsList, meta: { requiresAuth: true } },
    { path: '/project-finance/:id', name: 'project_financials_view', component: ProjectFinancialsView, meta: { requiresAuth: true } },

    // General Ledger
    { path: '/general-ledger', name: 'general_ledger', component: GeneralLedger, meta: { requiresAuth: true } },

    // Liabilities
    { path: '/liabilities', name: 'liabilities', component: Liabilities, meta: { requiresAuth: true } },
    
    // Payables
    { path: '/payables', name: 'payables', component: Payables, meta: { requiresAuth: true } },
    
    // Receivables
    { path: '/receivables', name: 'receivables', component: Receivables, meta: { requiresAuth: true } },

    // Invoice Payment History
    { path: '/finance/invoice-payments/:module/:invoiceNo', name: 'invoice_payment_history', component: InvoicePaymentHistoryView, meta: { requiresAuth: true } },
    
    // Cash Flow
    { path: '/cashflow', name: 'cashflow', component: CashFlow, meta: { requiresAuth: true } },

    // Reports
    { path: '/finance/reports/:reportId/preview', name: 'financial_report_preview', component: FinancialReportPreview, meta: { requiresAuth: true, department: 'Finance' } },

    // Pending Approvals 
    { path: '/finance/pending-approvals/prebiddings/:id', name: 'finance_pending_approval_detail', component: PendingApprovalsFinancePrebiddings, meta: { requiresAuth: true, department: 'Finance' } },

    /*
    |--------------------------------------------------------------------------
    | CRM Routes
    |--------------------------------------------------------------------------
    */

    // Prebiddings
    { path: '/prebiddings', name: 'prebiddings', component: PreBiddings, meta: { requiresAuth: true, permission: 'Prebiddings.view' } },
    { path: '/prebiddings/create', name: 'create_prebidding', component: CreatePreBidding, meta: { requiresAuth: true, permission: 'Prebiddings.manage' } },
    { path: '/prebiddings/:id', name: 'prebidding_view', component: PreBiddingDetails, meta: { requiresAuth: true, permission: 'Prebiddings.view' } },

    // Clients
    { path: '/clients', name: 'clients', component: Clients, meta: { requiresAuth: true, permission: 'Clients.view' } },
    { path: '/clients/create', name: 'create_client', component: CreateClient, meta: { requiresAuth: true, permission: 'Clients.manage' } },
    { path: '/clients/view/:id', name: 'view_client', component: ClientDetail, meta: { requiresAuth: true, permission: 'Clients.view' } },

    /*
    |--------------------------------------------------------------------------
    | Operations Routes
    |--------------------------------------------------------------------------
    */

    // Projects
    { path: '/projects', name: 'projects', component: Projects, meta: { requiresAuth: true, permission: 'Projects.view' } },
    { path: '/projects/:id', name: 'project_detail', component: ProjectDetail, meta: { requiresAuth: true, permission: 'Projects.view' } },
    { path: '/projects/:id/monitoring', name: 'project_monitoring', component: ProjectMonitoring, meta: { requiresAuth: true, permission: 'Projects.view' } },
    { path: '/projects/:id/attendance', name: 'project_attendance', component: ProjectAttendanceView, meta: { requiresAuth: true, permission: 'Projects.view' } },
    { path: '/projects/:id/attendance/:dayId', name: 'project_attendance_day', component: ProjectAttendanceDayView, meta: { requiresAuth: true, permission: 'Projects.view' } },
    { path: '/projects/:id/progress-reports/:reportId', name: 'project_progress_report', component: ProjectProgressReportView, meta: { requiresAuth: true, permission: 'Projects.view' } },
    { path: '/projects/:id/setup', name: 'project_contract_setup', component: ProjectContractSetup, meta: { requiresAuth: true, permission: 'Projects.manage' } },
 
    // Pending Approvals 
    { path: '/operations/pending-approvals/prebiddings/:id', name: 'operations_pending_approval_detail', component: PendingApprovalsOperationsPrebiddings, meta: { requiresAuth: true, department: 'Operations' } },

    /*
    |--------------------------------------------------------------------------
    | SCM Routes
    |--------------------------------------------------------------------------
    */

    // Inventory
    { path: '/inventory', name: 'inventory', component: Inventory, meta: { requiresAuth: true } },
    { path: '/inventory/:id', name: 'inventory_detail', component: InventoryDetail, meta: { requiresAuth: true } },
    
    // Material Requests
    { path: '/material-requests', name: 'material_requests', component: MaterialRequests, meta: { requiresAuth: true, } },
    { path: '/material-requests/:id', name: 'material_request_detail', component: MaterialRequestDetail, meta: { requiresAuth: true, } },

    // Suppliers
    { path: '/suppliers', name: 'suppliers', component: Suppliers, meta: { requiresAuth: true, permission: 'Suppliers.view' } },

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    // Pending Approvals
    { path: '/admin/pending-approvals/:id', name: 'admin_pending_approval_detail', component: AdminPendingApprovalDetail, meta: { requiresAuth: true, role: 'admin' } },
]   

/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL || '/'),
    routes,
    scrollBehavior(_, __, savedPosition) {
        return savedPosition
            ? { ...savedPosition, behavior: 'smooth' }
            : { top: 0, left: 0, behavior: 'smooth' }
    },
})

/*
|--------------------------------------------------------------------------
| Guards
|--------------------------------------------------------------------------
*/
router.beforeEach((to, from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return next({ name: 'login' })
    }

    if (to.name === 'login' && auth.isAuthenticated) {
        return next({ name: 'dashboard' })
    }

    if (to.meta.permission && !auth.permissions.includes(to.meta.permission)) {
        alert('You do not have permission to access this page.')
        return next({ name: 'dashboard' })
    }

    if (to.meta.department && auth.department !== to.meta.department) {
        alert('You do not have permission to access this page.')
        return next({ name: 'dashboard' })
    }

    if (to.meta.role && auth.user.role !== to.meta.role) {
        alert('You do not have permission to access this page.')
        return next({ name: 'dashboard' })
    }

    next()
})

export default router
