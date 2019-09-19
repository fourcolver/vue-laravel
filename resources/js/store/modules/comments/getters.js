export default {
    get: (state) => (id, commentable) => {
        return state[commentable][id]
    }
}