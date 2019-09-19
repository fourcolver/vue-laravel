export default {
    get (state) {
        return state
    },
    getById: state => id => state.data.find(post => post.id == id)
}