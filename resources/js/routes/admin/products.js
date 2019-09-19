import { auth, admin } from 'middlewares';
import permissions from 'middlewares/permissions';


export default [{
    path: 'products',
    component: {
        template: '<router-view />'
    },
    children: [{
        path: '/',
        name: 'adminProducts',
        component: () =>
            import ( /* webpackChunkName: "admin/products/index" */ 'views/Admin/Products'),
        meta: {
            title: 'Products',
            middleware: [auth, admin],
            permission: permissions.list.product,
            breadcrumb: 'Products'
        }
    }, {
        path: ':id',
        name: 'adminProductsEdit',
        component: () =>
            import ( /* webpackChunkName: "admin/products/edit" */ 'views/Admin/Products/Edit'),
        meta: {
            title: 'Edit Product',
            middleware: [auth, admin],
            permission: permissions.update.product,
            breadcrumb: 'Products'
        }
    }]
}];