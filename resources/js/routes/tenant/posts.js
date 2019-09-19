import { auth, tenant } from 'middlewares'

export default {
    path: 'news',
    component: {
        template: '<router-view />'
    },
    meta: {
        middleware: [auth, tenant]
    },
    children: [{
        path: '/',
        name: 'tenantPosts',
        component: () =>
            import ( /* webpackChunkName: "tenant/posts/index" */ 'views/Tenant/Posts'),
        meta: {
            title: 'Posts'
        }
    }, {
        path: ':id',
        name: 'tenantPost',
        component: () =>
            import ( /* webpackChunkName: "tenant/posts/detail" */ 'views/Tenant/Posts/Detail'),
        meta: {
            title: 'Post'
        }
    }]
}