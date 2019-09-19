import {auth, admin} from 'middlewares';
import permissions from 'middlewares/permissions';
import Layout from 'layouts/AdminLayout';
import Dashboard from 'views/Admin';
import Profile from 'views/Admin/Profile';
import Settings from 'views/Admin/Settings';

import BuildingsRoutes from './buildings';
import UnitsRoutes from './units';
import TenantsRoutes from './tenants';
import UsersRoutes from './users';
import ServicesRoutes from './services';
import PostsRoutes from './posts';
import DistrictsRoutes from './districts';
import RequestsRoutes from './requests';
import PropertyManagersRoutes from './propertyManagers';
import ProductsRoutes from './products';
import TemplatesRoutes from './templates';


export default [{
    path: '/admin',
    component: Layout,
    children: [{
        path: '/',
        name: 'admin',
        component: Dashboard,
        meta: {
            middleware: [auth, admin],
            breadcrumb: 'Home'
        },
    }, {
        path: 'dashboard',
        name: 'adminDashboard',
        component: Dashboard,
        meta: {
            middleware: [auth, admin],
            breadcrumb: 'Home'
        },
    }, {
        path: 'profile',
        name: 'adminProfile',
        component: Profile,
        meta: {
            middleware: [auth, admin],
            breadcrumb: 'Profile'
        }
    }, {
        path: 'settings',
        name: 'adminSettings',
        component: Settings,
        meta: {
            middleware: [auth, admin],
            permission: permissions.view.realEstate,
            breadcrumb: 'Settings'
        },
    },
        ...UsersRoutes,
        ...ServicesRoutes,
        ...TenantsRoutes,
        ...BuildingsRoutes,
        ...PostsRoutes,
        ...UnitsRoutes,
        ...DistrictsRoutes,
        ...RequestsRoutes,
        ...PropertyManagersRoutes,
        ...ProductsRoutes,
        ...TemplatesRoutes,
    ]
}];
