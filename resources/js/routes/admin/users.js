import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';

export default [{
    path: 'users',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminUsers',
        component: () =>
            import ( /* webpackChunkName: "admin/users/index */ 'views/Admin/Users'),
        meta: {
            title: 'Users',
            middleware: [auth, admin],
            permission: permissions.list.user,
            breadcrumb: 'Users',
        }
    }, {
        path: 'add',
        name: 'adminUsersAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/users/add" */ 'views/Admin/Users/Add'),
        props: {
            title: 'Add user'
        },
        meta: {
            title: 'Add User',
            middleware: [auth, admin],
            permission: permissions.create.user,
            breadcrumb: 'Add user'
        }
    }, {
        path: ':id',
        name: 'adminUsersEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/users/edit" */ 'views/Admin/Users/Edit'),
        props: {
            title: 'Edit user'
        },
        meta: {
            title: 'Edit User',
            middleware: [auth, admin],
            permission: permissions.update.user,
            breadcrumb: 'Edit user'
        }
    }]
}];