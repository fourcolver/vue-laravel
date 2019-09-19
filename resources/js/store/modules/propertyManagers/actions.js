import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getPropertyManagers({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('propertyManagers', payload))
                .then(({data: r}) => {
                    if (r && !payload.disableCommit) {
                        commit('SET_PROPERTY_MANAGERS', r.data)
                    }

                    if (!payload.get_all) {
                        r.data.data = r.data.data.map((manager) => {
                            manager.name = `${manager.first_name} ${manager.last_name}`;
                            return manager
                        });
                    }

                    resolve(r)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    createPropertyManager({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post('propertyManagers', payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    updatePropertyManager({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`propertyManagers/${payload.id}`, payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    getPropertyManager({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`propertyManagers/${payload.id}`).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    deletePropertyManager({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`propertyManagers/${payload.id}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.service.deleted'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    assignDistrict({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`propertyManagers/${payload.id}/districts/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    assignBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`propertyManagers/${payload.id}/buildings/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    unassignBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`propertyManagers/${payload.id}/buildings/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.propertyManager.unassigned.building'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    unassignDistrict({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`propertyManagers/${payload.id}/districts/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.propertyManager.unassigned.district'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    batchDeletePropertyManagers({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`propertyManagers/batchDelete`, {data: payload}).then((response) => {
                resolve({
                    success: true,
                    message: 'models.propertyManager.deleted'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    getAssignments({}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(buildFetchUrl(`propertyManagers/${payload.manager_id}/assignments`, payload)).then((response) => {
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
    },
    getIDsAssignmentsCount({}, payload) {       
        return new Promise((resolve, reject) => {
            axios.post(`propertyManagers/idsassignments`, payload).then((response) => {    
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    }
}
