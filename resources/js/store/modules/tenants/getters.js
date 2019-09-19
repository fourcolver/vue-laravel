import {format} from 'date-fns';

export default {
    tenants({tenants: {data = []}}) {
        return data.map(tenant => {
            tenant.name = `${tenant.first_name} ${tenant.last_name}`;
            tenant.birth_date = format(new Date(tenant.birth_date), 'DD.MM.YYYY');
            tenant.user_email = tenant.user.email;

            if (tenant.building && tenant.building.address) {
                tenant.building_address_row = `${tenant.building.address.street} ${tenant.building.address.street_nr}`;
                tenant.building_address_zip = `${tenant.building.address.zip} ${tenant.building.address.city}`;
            }

            tenant.unit_name = '';
            if (tenant.unit) {
                tenant.unit_name = tenant.unit.name;
            }

            return tenant;
        });
    },
    tenantsMeta({tenants}) {
        return _.omit(tenants, 'data');
    }
}
