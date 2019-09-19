export default {
    SET_PRODUCT_COMMENTS (state, {productId, inserting, ...payload}) {
        if (inserting) {
            let product = state[productId];
            let props = Object.keys(payload).filter(prop => prop !== 'data');

            for (let prop of props) {
                if (product[prop] !== payload[prop]) {
                    product[prop] = payload[prop];
                }
            }

            product.data = [...payload.data, ...product.data];
        } else {
            state[productId] = {...state[productId], ...payload};
        }
    },
    SAVE_PRODUCT_COMMENT (state, {productId, ...payload}) {
        state[productId].total++;
        state[productId].data.push(payload);
    },
    UPDATE_PRODUCT_COMMENT (state, {productId, ...payload}) {
        let comment = state[productId].data.find(c => c.id === payload.id);

        Object.assign(comment, payload);
    }
};
