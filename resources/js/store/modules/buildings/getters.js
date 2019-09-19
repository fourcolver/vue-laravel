export default {
    buildings({buildings: {data = []}}) {
        return data.map(building => {
            building.basement = building.basement ? 'Yes' : 'No';

            if (building.address) {
                building.address_row = `${building.address.street} ${building.address.street_nr}`;
                building.address_zip = `${building.address.zip} ${building.address.city}`;
            }

            return building;
        });
    },
    buildingsMeta({buildings}) {
        return _.omit(buildings, 'data');
    }
}
