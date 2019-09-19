export default {
    get (state) {
        return state
    },
    getById: state => id => state.data.find(product => product.id === id)
}