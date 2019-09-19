import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';

export default [{
    path: 'request-categories',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminRequestCategories',
        component: () =>
            import ( /* webpackChunkName: "admin/requestCategories/index" */ 'views/Admin/RequestCategories'),
        meta: {
            title: 'Request Categories',
            middleware: [auth, admin],
            permission: permissions.list.requestCategory
        }
    }]
}];