import {types} from "mixins/methods/unitTypes";

export default {
    units({units}) {
        let unitsArr = units.data ? units.data : [];
        return unitsArr.map((unit) => {
            unit.typeLabel = types[unit.type];
            return unit;
        });
    },
    unitsMeta({units}) {
        return _.omit(units, 'data');
    }
}
