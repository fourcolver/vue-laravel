import { auth, tenant } from 'middlewares'

export default {
    path: 'my',
    component: {
        template: '<router-view />'
    },
    meta: {
        middleware: [auth, tenant]
    },
    children: [{
        path: 'personal',
        component: () =>
            import ( /* webpackChunkName: "tenant/my/personal" */ 'views/Tenant/My/Personal'),
        name: 'tenantMyPersonal',
        meta: {
            title: 'My personal data'
        }
    }, {
        path: 'contracts',
        component: () =>
            import ( /* webpackChunkName: "tenant/my/contracts" */ 'views/Tenant/My/Contracts'),
        name: 'tenantMyContracts',
        meta: {
            title: 'My recent contracts'
        }
    }, {
        path: 'documents',
        component: () =>
            import ( /* webpackChunkName: "tenant/my/documents" */ 'views/Tenant/My/Documents'),
        name: 'tenantMyDocuments',
        meta: {
            title: 'My documents'
        }
    }, {
        path: 'contacts',
        component: () =>
            import ( /* webpackChunkName: "tenant/my/contacts" */ 'views/Tenant/My/Contacts'),
        name: 'tenantMyContacts',
        meta: {
            title: 'My contact persons'
        }
    }]
}