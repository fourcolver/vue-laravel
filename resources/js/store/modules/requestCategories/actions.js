import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getRequestCategories({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('requestCategories', payload))
                .then(({data: r}) => (r && commit('SET_REQUEST_CATEGORIES', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createRequestCategory({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post('requestCategories', payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    updateRequestCategory({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`requestCategories/${payload.id}`, payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    getRequestCategory({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`requestCategories/${payload.id}`).then((response) => {
                resolve(response.data.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    deleteRequestCategory({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requestCategories/${payload.id}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.request.deleted'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    getRequestCategoriesTree({}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('requestCategories/tree', payload))
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));

    }
}
