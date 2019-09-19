export default {
    addresses({addresses}) {
        return addresses;
    },
    addressesMeta ({addresses}) {
        return _.omit(addresses, 'data');
    },
    states ({states}) {
        return states;
    }
}