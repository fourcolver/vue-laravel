export default {
    set_constants(state, payload) {
        state.constants = payload;
    },
    set_audits(state, { id, type, data }) {
        if (id) {
            state.audits[type][id] = data
        } else {
            state.audits[type] = data
        }
    }
}