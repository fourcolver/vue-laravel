export const checkTenancy = (roles) => {
    const userRoles = _.map(roles, 'name');
    return _.indexOf(userRoles, 'registered') > -1;
};
