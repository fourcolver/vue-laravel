import {checkTenancy} from "helpers/checkTenancy";

export default function tenant(ctx) {
    const {next, router, loggedInUser} = ctx;
    const isTenant = checkTenancy(loggedInUser.roles);

    if (!isTenant) {
        return next();
    }

    return router.push({name: 'tenantDashboard'});
}
