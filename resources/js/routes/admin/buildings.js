import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'buildings',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminBuildings',
        component: () =>
            import ( /* webpackChunkName: "admin/buildings/index" */ 'views/Admin/Buildings'),
        meta: {
            title: 'Buildings',
            middleware: [auth, admin],
            permission: permissions.list.building
        }
    }, {
        path: 'add',
        name: 'adminBuildingsAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/buildings/add" */ 'views/Admin/Buildings/Add'),
        props: {
            title: 'Add building'
        },
        meta: {
            title: 'Add Building',
            middleware: [auth, admin],
            permission: permissions.create.building
        }
    }, {
        path: ':id',
        name: 'adminBuildingsEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/buildings/edit" */ 'views/Admin/Buildings/Edit'),
        props: {
            title: 'Edit building'
        },
        meta: {
            title: 'Edit Building',
            middleware: [auth, admin],
            permission: permissions.update.building
        }
    }, {
        path: ':id/units',
        name: 'adminBuildingUnits',
        component: () =>
            import ( /* webpackChunkName: "admin/units/index" */ 'views/Admin/Buildings/Units'),
        meta: {
            title: 'Building Units',
            middleware: [auth, admin],
            permission: permissions.list.unit
        }
    }, {
        path: ':id/units/add',
        name: 'adminBuildingUnitsAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/units/add" */ 'views/Admin/Buildings/Units/Add'),
        props: {
            title: 'Add unit'
        },
        meta: {
            title: 'Add Unit',
            middleware: [auth, admin],
            permission: permissions.create.unit
        }
    }, {
        path: ':buildingId/units/:id/edit',
        name: 'adminBuildingUnitsEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/units/edit" */ 'views/Admin/Buildings/Units/Edit'),
        props: {
            title: 'Edit unit'
        },
        meta: {
            title: 'Edit Unit',
            middleware: [auth, admin],
            permission: permissions.update.unit
        }
    }]
}];