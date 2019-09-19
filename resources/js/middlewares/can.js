import store from '@/store';

export default (permission) => {
    const loggedInUser = store.getters['loggedInUser'];
    const userPermissions = _.map(_.flatten(_.map(loggedInUser.roles, 'perms')), 'name');

    let hasPermission = false;

    if (!Array.isArray(permission)) {
        hasPermission = userPermissions.includes(permission);
    } else {
        hasPermission = permission.some(perm => userPermissions.includes(perm));
    }

    return hasPermission;
};
