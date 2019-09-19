import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'property-managers',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminPropertyManagers',
        component: () =>
            import ( /* : "admin/propertyManagers/index" */ 'views/Admin/PropertyManagers'),
        props: {
            title: 'List property manager'
        },
        meta: {
            title: 'Users',
            middleware: [auth, admin],
            permission: permissions.list.propertyManager,
            breadcrumb: 'Users',
        }
    }, {
        path: 'add',
        name: 'adminPropertyManagersAdd',
        component: () =>
            import ( /* : "admin/propertyManagers/add" */ 'views/Admin/PropertyManagers/Add'),
        props: {
            title: 'Add property manager'
        },
        meta: {
            title: 'Add Property Manager',
            middleware: [auth, admin],
            permission: permissions.create.propertyManager,
            breadcrumb: 'Add property manager'
        }
    }, {
        path: ':id',
        name: 'adminPropertyManagersEdit',
        component: () =>
            import ( /* : "admin/propertyManagers/edit" */ 'views/Admin/PropertyManagers/Edit'),
        props: {
            title: 'Edit property manager'
        },
        meta: {
            title: 'Edit Property Manager',
            middleware: [auth, admin],
            permission: permissions.update.propertyManager,
            breadcrumb: 'Edit property manager'
        }
    }]
}];