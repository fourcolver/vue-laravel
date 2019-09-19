import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getUnits({commit}, payload) {
        return new Promise((resolve, reject) => 
            axios.get(buildFetchUrl('units', payload))
                 .then(({data: r}) => (r && commit('SET_UNITS', r.data), resolve(r)))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    getUnit(_, {id}) {
        return new Promise((resolve, reject) => 
            axios.get(`units/${id}`)
                 .then(({data: r}) => resolve(r.data))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    createUnit(_, payload) {
        return new Promise((resolve, reject) => 
            axios.post('units', payload)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    updateUnit(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) => 
            axios.put(`units/${id}`, restPayload)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    deleteUnit(_, {id}) {
        return new Promise((resolve, reject) => 
            axios.delete(`units/${id}`)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    }
}
