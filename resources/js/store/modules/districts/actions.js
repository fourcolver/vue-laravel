import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getDistricts({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('districts', payload))
                .then(({data: r}) => (r && commit('SET_DISTRICTS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getDistrict(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`districts/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createDistrict(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('districts', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateDistrict(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`districts/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteDistrict(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`districts/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    }
}
