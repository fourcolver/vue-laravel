import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getTenants({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('tenants', payload))
                .then(({data: r}) => {
                    commit('SET_TENANTS', r.data);

                    if (!payload.get_all) {
                        r.data.data = r.data.data.map((tenant) => {
                            tenant.name = `${tenant.first_name} ${tenant.last_name}`;
                            return tenant;
                        });
                    }

                    resolve(r)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    getTenant(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`tenants/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createTenant(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('tenants', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateTenant(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`tenants/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteTenant(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`tenants/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    myTenancy(_, payload) {
        return new Promise((resolve, reject) => {
            axios.get('tenants/me')
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err))
        });
    },
    updateMyTenancy(_, payload) {
        return new Promise((resolve, reject) => {
            axios.put('tenants/me', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err))
        });
    },
    uploadMediaFile(_, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`tenants/${id}/media`, payload)
                .then(({data}) => resolve(data))
                .catch(({response: {data: err}}) => reject(err));
        });
    },
    deleteMediaFile(_, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.delete(`tenants/${id}/media/${payload.media_id}`)
                .then((resp) => {
                    resolve({
                        success: true,
                        message: 'models.building.document.deleted'
                    });
                }).catch((error) => reject(error));
        });
    },
    changeTenantStatus(_, {id, status}) {
        return new Promise((resolve, reject) => {
            axios.put(`tenants/${id}/status`, {status})
                .then((resp) => {
                    resolve(resp.data)
                }).catch((error) => reject(error));
        });
    },
    downloadTenantCredentials(_, {id}) {
        return new Promise((resolve, reject) => {
            axios.post(`tenants/${id}/download-credentials`, {}, {
                responseType: 'arraybuffer',
                headers: {
                    'Accept': 'application/pdf'
                }
            })
                .then((resp) => resolve(resp))
                .catch(({response: {data: err}}) => reject(err));
        });
    },
    sendTenantCredentials(_, {id}) {
        return new Promise((resolve, reject) => {
            axios.post(`tenants/${id}/send-credentials`)
                .then((resp) => resolve(resp))
                .catch(({response: {data: err}}) => reject(err));
        });
    }
}
