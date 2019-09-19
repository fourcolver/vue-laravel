import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';

export default [{
    path: 'districts',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminDistricts',
        component: () =>
            import ( /* webpackChunkName: "admin/districts/index" */ 'views/Admin/Districts'),
        meta: {
            title: 'Districts',
            middleware: [auth, admin],
            permission: permissions.list.district
        }
    }]
}];