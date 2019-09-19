export default {
    propertyManagers(state) {
        return state.propertyManagers.data;
    },
    propertyManagersMeta(state) {
        return _.omit(state.propertyManagers, 'data');
    }
}