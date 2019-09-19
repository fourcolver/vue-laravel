import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';

export default [{
    path: 'units',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminUnits',
        component: () =>
            import ( /* webpackChunkName: "admin/units/index */ 'views/Admin/Units'),
        meta: {
            title: 'Units',
            middleware: [auth, admin],
            permission: permissions.list.unit
        }
    }, {
        path: 'add',
        name: 'adminUnitsAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/units/add" */ 'views/Admin/Units/Add'),
        props: {
            title: 'Add units'
        },
        meta: {
            title: 'Add Unit',
            middleware: [auth, admin],
            permission: permissions.create.unit
        }
    }, {
        path: ':id',
        name: 'adminUnitsEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/units/edit" */ 'views/Admin/Units/Edit'),
        props: {
            title: 'Edit units'
        },
        meta: {
            title: 'Edit Unit',
            middleware: [auth, admin],
            permission: permissions.update.unit
        }
    }]
}];