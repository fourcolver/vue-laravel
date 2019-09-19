import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'posts',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminPosts',
        component: () =>
            import ( /* webpackChunkName: "admin/posts/index" */ 'views/Admin/Posts'),
        meta: {
            title: 'Posts',
            middleware: [auth, admin],
            permission: permissions.list.post,
            breadcrumb: 'Posts'
        }
    }, {
        path: 'add',
        name: 'adminPostsAdd',
        component: () =>
            import ( /* webpackChunkName: "admin/posts/add" */ 'views/Admin/Posts/Add'),
        meta: {
            title: 'Add Pinned Post',
            middleware: [auth, admin],
            permission: permissions.create.post,
            breadcrumb: 'Add Posts'
        }
    }, {
        path: ':id',
        name: 'adminPostsEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/posts/edit" */ 'views/Admin/Posts/Edit'),
        meta: {
            title: 'Edit Post',
            middleware: [auth, admin],
            permission: permissions.update.post,
            breadcrumb: 'Edit Posts'
        }
    }]
}];