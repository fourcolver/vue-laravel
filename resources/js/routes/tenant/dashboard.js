import { auth, tenant } from 'middlewares'

export default {
    path: 'dashboard',
    component: () =>
        import ( /* webpackChunkName: "tenant/dashboard/index" */ 'views/Tenant/Dashboard'),
    name: 'tenantDashboard',
    meta: {
        middleware: [auth, tenant]
    }
}