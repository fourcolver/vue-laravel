import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getServices({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('services', payload))
                .then(({data: r}) => (r && commit('SET_SERVICES', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getServicesGroupedByCategory({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get('services/category')
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createService({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post('services', payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    updateService({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`services/${payload.id}`, payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    getService({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`services/${payload.id}`).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    deleteService({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`services/${payload.id}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.service.deleted'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    assignServiceDistrict({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`services/${payload.id}/districts/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    assignServiceBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`services/${payload.id}/buildings/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    unassignServiceBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`services/${payload.id}/buildings/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.service.unassigned.building'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    unassignServiceDistrict({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`services/${payload.id}/districts/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.service.unassigned.district'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    getServiceAssignments({}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(buildFetchUrl(`services/${payload.provider_id}/locations`, payload)).then((response) => {
                response.data.data.data = response.data.data.data.map((building) => {
                    if (building.type === 'building') {
                        building.aType = 1;
                        building.assignmentType = 'building';
                    } else {
                        building.aType = 2;
                        building.assignmentType = 'district';
                    }
                    return building;
                });
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    }
}
