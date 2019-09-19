import { auth, tenant } from 'middlewares'

export default {
    path: 'settings',
    component: () =>
        import ( /* webpackChunkName: "tenant/settings/index" */ 'views/Tenant/Settings'),
    name: 'tenantSettings',
    meta: {
        title: 'Settings',
        middleware: [auth, tenant]
    }
}