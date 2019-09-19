import { auth, tenant } from 'middlewares'

export default {
    path: 'requests',
    component: () =>
        import ( /* webpackChunkName: "tenant/requests/index" */ 'views/Tenant/Requests'),
    name: 'tenantRequests',
    meta: {
        title: 'Requests',
        middleware: [auth, tenant]
    }
}