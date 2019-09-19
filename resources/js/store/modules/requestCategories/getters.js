export default {
    requestCategories(state) {
        return state.requestCategories.map((category) => {
            category.name = category.parent_id ? `----- ${category.name}` : category.name;
            return category;
        });
    },
    requestCategoriesMeta(state) {
        return _.omit(state.requestCategories, 'data');
    }
}
