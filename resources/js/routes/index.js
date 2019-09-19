import Vue from 'vue';
import VueRouter from 'vue-router';
import Landing from 'views/Landing';
import AuthRoutes from './auth';
import TenantRoutes from './tenant';
import AdminRoutes from './admin';
import store from '@/store';
import can from 'middlewares/can';

Vue.use(VueRouter);

let originalTitle;

const routes = [{
    path: '/',
    component: Landing
}];

const router = new VueRouter({
    routes: [...routes, ...AuthRoutes, ...TenantRoutes, ...AdminRoutes],
    mode: 'history',
    linkActiveClass: 'active',
    scrollBehavior: (to, from, savedPosition) => savedPosition || {x: 0, y: 0}
});

const nextFactory = (context, middleware, index) => {
    const subsequentMiddleware = middleware[index];

    if (!subsequentMiddleware) return context.next;

    return (...parameters) => {
        context.next(...parameters);

        const nextMiddleware = nextFactory(context, middleware, index + 1);

        subsequentMiddleware({...context, next: nextMiddleware});
    };
};

router.beforeEach(async (to, from, next) => {
    const constants = store.getters['application/constants'];

    if (!Object.keys(constants).length || (localStorage.token && !store.getters.loggedIn)) {
        return;
    }

    Vue.prototype.$constants = constants;

    !originalTitle && (originalTitle = document.title);

    if (to.meta.title) {
        const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

        nearestWithTitle && (document.title = originalTitle + ' - ' + nearestWithTitle.meta.title);
    }

    if (to.meta.permission) {
        let canView = can(to.meta.permission);
        if (!canView) {
            return router.push({name: 'login'});
        }
    }

    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware)
            ? to.meta.middleware
            : [to.meta.middleware];

        const context = {from, next, router, to};
        const loggedInUser = store.getters['loggedInUser'];

        if (loggedInUser) {
            context.loggedInUser = loggedInUser;
        }

        const nextMiddleware = nextFactory(context, middleware, 1);

        return middleware[0]({...context, next: nextMiddleware});
    }

    return next();
});

export default router;
