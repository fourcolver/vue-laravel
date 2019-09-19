import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'requests',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminRequests',
        component: () =>
            import ( /* webpackChunkName: "admin/serviceRequests/index" */ 'views/Admin/Requests'),
        props: {
            title: 'Requests'
        },
        meta: {
            title: 'Requests',
            middleware: [auth, admin],
            permission: permissions.list.request,
            breadcrumb: 'Requests',
        }
    }, {
        path: 'add',
        name: 'adminRequestsAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/serviceRequests/add" */ 'views/Admin/Requests/Add'),
        props: {
            title: 'Add request'
        },
        meta: {
            title: 'Add Request',
            middleware: [auth, admin],
            permission: permissions.create.request,
            breadcrumb: 'Add request'
        }
    }, {
        path: 'activity',
        name: 'adminRequestsActivity',
        component: () =>
            import ( /* webpackChunkName: "admin/serviceRequests/activity" */ 'views/Admin/Requests/Activity'),
        props: {
            title: 'Activity requests'
        },
        meta: {
            title: 'Activity requests',
            middleware: [auth, admin],
            breadcrumb: 'Activity requests'
        }
    }, {
        path: ':id',
        name: 'adminRequestsEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/serviceRequests/edit" */ 'views/Admin/Requests/Edit'),
        props: {
            title: 'Edit request'
        },
        meta: {
            title: 'Edit Request',
            middleware: [auth, admin],
            permission: [permissions.update.request, permissions.update.serviceRequest],
            breadcrumb: 'Edit request'
        }
    }]
}];