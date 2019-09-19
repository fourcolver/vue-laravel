export default function auth(ctx) {
    const {next, router} = ctx;

    if (!localStorage.getItem('token')) {
        return router.push({name: 'login'});
    }

    return next();
};
