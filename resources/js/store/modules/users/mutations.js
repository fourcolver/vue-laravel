export default {
    SET_USERS(state, users) {
        state.users = users;
    },
    SET_LOGGED_IN(state, loggedIn) {
        state.loggedIn = loggedIn;
    },
    SET_LOGGED_IN_USER(state, user) {
        state.loggedInUser = user;
    },
    SET_USERS_META(state, meta) {
        state.usersMeta = meta;
    },
    SET_REAL_ESTATE(state, realEstate) {
        state.realEstate = realEstate;
    }
}
