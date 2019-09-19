export default {
    get: (state, getters, rootState) => id => {
        return state[id];
    },
    meta: (state) => id => {
        const {data, ...rest} = state[id];
        return rest;
    }
}
