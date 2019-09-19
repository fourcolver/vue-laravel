export default {
    methods: {
        prepareCategories(categories) {
            let tempCategories = [];

            categories.forEach((category) => {
                tempCategories.push(_.omit(category, 'categories'));
                if (category.categories) {
                    category.categories.forEach((subCategory) => {
                        subCategory.name = `-- ${subCategory.name}`;
                        tempCategories.push(subCategory)
                    });
                }
            });

            return tempCategories;
        }
    }
}
