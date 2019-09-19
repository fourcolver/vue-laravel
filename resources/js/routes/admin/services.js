import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'services',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminServices',
        component: () =>
            import ( /* webpackChunkName: "admin/services/index" */ 'views/Admin/Services'),
        props: {
            title: 'Add service'
        },
        meta: {
            title: 'Users',
            middleware: [auth, admin],
            permission: permissions.list.provider,
            breadcrumb: 'Users',
        }
    }, {
        path: 'add',
        name: 'adminServicesAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/services/add" */ 'views/Admin/Services/Add'),
        props: {
            title: 'Add service'
        },
        meta: {
            title: 'Add Service',
            middleware: [auth, admin],
            permission: permissions.create.provider,
            breadcrumb: 'Add service'
        }
    }, {
        path: ':id',
        name: 'adminServicesEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/services/edit" */ 'views/Admin/Services/Edit'),
        props: {
            title: 'Edit service'
        },
        meta: {
            title: 'Edit Service',
            middleware: [auth, admin],
            permission: permissions.update.provider,
            breadcrumb: 'Edit service'
        }
    }]
}];