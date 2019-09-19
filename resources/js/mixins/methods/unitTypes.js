export const types = {
    1: 'apartment',
    2: 'business'
};

export default {
    computed: {
        unitTypes() {
            return [{
                type: 1,
                label: 'apartment'
            }, {
                type: 2,
                label: 'business'
            }]
        },
        isBusiness() {
            return this.model.type === 2;
        }
    }
}
