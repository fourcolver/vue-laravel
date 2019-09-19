import { auth, tenant } from 'middlewares'

export default {
    path: 'cleanify',
    component: () =>
        import ( /* webpackChunkName: "tenant/cleanify/index" */ 'views/Tenant/Cleanify'),
    name: 'cleanifyRequest',
    meta: {
        title: 'Cleanify',
        middleware: [auth, tenant]
    }
}
