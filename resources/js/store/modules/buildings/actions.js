import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getBuildings({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('buildings', payload))
                .then(({data: r}) => (r && commit('SET_BUILDINGS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getBuilding(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`buildings/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createBuilding(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('buildings', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateBuilding(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`buildings/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteBuilding(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`buildings/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getBuildingStatistics(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`buildings/${id}/statistics`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadBuildingFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/${payload.id}/media`, {...payload})
                .then((resp) => {
                    resolve({
                        success: true,
                        message: 'models.building.document.uploaded',
                        media: resp.data.data
                    });
                }).catch((error) => reject(error));
        })
    },
    deleteBuildingFile(_, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`buildings/${payload.id}/media/${payload.media_id}`)
                .then((resp) => {
                    resolve({
                        success: true,
                        message: 'models.building.document.deleted'
                    });
                }).catch((error) => reject(error));
        });
    },
    deleteBuildingService(_, {building_id, id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`buildings/${building_id}/service/${id}`).then((resp) => {
                resolve({
                    success: true,
                    message: 'models.building.service.deleted'
                });
            }).catch((error) => reject(error));
        });
    },
    batchAssignUsersToBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/${payload.id}/propertyManagers`, {...payload})
                .then((resp) => {
                    resolve({
                        success: true,
                        message: 'models.building.managers_assigned'
                    });
                }).catch((error) => reject(error));
        })
    },
    unassignBuildingManager(_, {building_id, id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`buildings/${building_id}/propertyManagers/${id}`).then((resp) => {
                resolve({
                    success: true,
                    message: 'models.building.manager.unassigned'
                });
            }).catch((error) => reject(error));
        });
    },
    deleteBuildingWithIds({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/deletewithids`, {...payload}).then((resp) => {                
                resolve(resp.data);
            }).catch((error) => reject(error));
        });
    },
    checkUnitRequestWidthIds({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`buildings/checkunitrequest`, {...payload}).then((resp) => {                
                resolve(resp.data);
            }).catch((error) => reject(error));
        });
    }    
}
