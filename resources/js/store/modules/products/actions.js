import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getProducts({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('products', payload))
                .then(({data: r}) => (r && commit('SET_PRODUCTS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getProduct(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`products/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createProduct(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('products', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateProduct(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`products/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteProduct(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`products/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    likeProduct(_, id) {
        return new Promise((resolve, reject) =>
            axios.post(`products/${id}/like`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    unlikeProduct(_, id) {
        return new Promise((resolve, reject) =>
            axios.post(`products/${id}/unlike`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    changeProductPublish(_, {id, ...body}) {
        return new Promise((resolve, reject) =>
            axios.post(`products/${id}/publish`, body)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadProductMedia(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.post(`products/${id}/media`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteProductMedia({}, {id, media_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`products/${id}/media/${media_id}`).then((resp) => {
                resolve(resp.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    }
}
