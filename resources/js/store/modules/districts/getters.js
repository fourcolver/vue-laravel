export default {
    districts({districts}) {
        return districts.data;
    },
    districtsMeta ({districts}) {
        return _.omit(districts, 'data');
    }
}