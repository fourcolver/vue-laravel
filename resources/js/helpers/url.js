export const buildFetchUrl = (start, filters) => {
    let url = `${start}?`;

    if (filters) {

        const {searchFields, ...customFilters} = filters;

        if (searchFields) {
            url += `searchFields=${searchFields.join(';')}`;
        }

        for (let [name, value] of Object.entries(customFilters)) {
            if (Array.isArray(value)) {
                value.forEach((p) => {
                    url += `&${name}[]=${p}`;
                })
            } else {
                url += `&${name}=${value}`;
            }
        }
    }

    return `${url}`;
};
