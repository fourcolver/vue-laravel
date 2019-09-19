export default {
    set (state, payload) {
        if (payload.data) {
            Object.assign(state, payload)
        } else {
            state.data.push(payload)
        }
    },
    SAVE (state) {

    },
    UPDATE (state, payload) {
        Object.assign(state.data.find(({id}) => id === payload.id), payload)
    },
    PARTIAL_UPDATE (state, {id, data}) {
        Object.assign(state.data.find(product => id === product.id), data)
    },
    DELETE (state) {

    }
}