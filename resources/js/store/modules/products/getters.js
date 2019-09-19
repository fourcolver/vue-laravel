export default {
    products(state, getters, rootState) {
        const {application: {constants: {products: productConstants}}} = rootState;

        const products = state.products.data ? state.products.data : [];

        return products.map(product => {
            product.status_label = productConstants.status[product.status];
            product.visibility_label = productConstants.visibility[product.visibility];
            product.type_label = productConstants.type[product.type];
            return product;
        });
    },
    productsMeta({products}) {
        return _.omit(products, 'data');
    }
}
