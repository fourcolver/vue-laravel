export default {
    SET_REQUESTS(state, requests) {
        state.requests = requests;
    },
    SAVE_REQUEST_TEMPLATES (state, {id, data}) {
        state.templates = Object.assign({}, state.templates, {
            [id]: data
        })
    }
}