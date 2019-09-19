export default function auth(ctx) {
    const {next, router} = ctx;

    if (!localStorage.getItem('token')) {
        return next();
    }

    return router.push({name: 'tenantDashboard'});
};
