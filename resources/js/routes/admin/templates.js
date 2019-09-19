import {auth, admin} from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'templates',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminTemplates',
        component: () =>
            import ( /* webpackChunkName: "admin/templates/index" */ 'views/Admin/Templates'),
        meta: {
            title: 'Templates',
            middleware: [auth, admin],
            permission: permissions.list.template
        }
    }, {
        path: ':id',
        name: 'adminTemplatesEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/templates/edit" */ 'views/Admin/Templates/Edit'),
        props: {
            title: 'Edit template'
        },
        meta: {
            title: 'Edit Building',
            middleware: [auth, admin],
            permission: permissions.update.template
        }
    }]
}];
