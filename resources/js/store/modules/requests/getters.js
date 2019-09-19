import {format} from 'date-fns'

export default {
    requests(state, getters, rootState) {
        const {application: {constants: {service_requests}}} = rootState;
        const requests = state.requests.data ? state.requests.data : [];

        return requests.map((request) => {
            request.created_at = format(new Date(request.created_at), 'DD.MM.YYYY');
            request.priority_label = service_requests.priority[request.priority];
            request.status_label = service_requests.status[request.status];
            request.qualification_label = service_requests.qualification[request.qualification];

            request.tenant_name = request.tenant ? `${request.tenant.first_name} ${request.tenant.last_name}` : '';
            request.category_name = request.category.name;
            request.parent_category_name = request.category.parent_id ? request.category.parentCategory.name : '';

            const assignedUsers = [...request.assignedUsers];

            request.assignedUsersCount = 0;
            if (assignedUsers.length) {
                request.assignedUsers = request.assignedUsers.splice(0, 2);
                if (assignedUsers.length > 2) {
                    request.assignedUsersCount = assignedUsers.length - 2;
                }
            }

            if (request.tenant && request.tenant.building && request.tenant.building.address) {
                request.address = `${request.tenant.building.address.street} ${request.tenant.building.address.street_nr}`;
                request.zip = `${request.tenant.building.address.zip} ${request.tenant.building.address.city}`;
            }

            return request;
        });
    },
    requestsMeta(state) {
        return _.omit(state.requests, 'data');
    },
    getRequestTemplatesWithId: state => id => state.templates[id]
}
