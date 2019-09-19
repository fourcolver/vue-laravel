import { auth, tenant } from 'middlewares'

export default {
    path: 'marketplace',
    component: () =>
        import ( /* webpackChunkName: "tenant/marketplace/index" */ 'views/Tenant/Marketplace'),
    name: 'tenantMarketplace',
    meta: {
        title: 'Marketplace',
        middleware: [auth, tenant]
    }
}