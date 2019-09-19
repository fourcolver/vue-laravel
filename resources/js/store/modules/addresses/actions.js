import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getAddresses({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('addresses', payload))
                 .then(({data: r}) => (r && commit('SET_ADDRESSES', r.data), resolve(r)))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    getAddress(_, {id}){
        return new Promise((resolve, reject) =>
            axios.get(`addresses/${id}`)
                 .then(({data: r}) => resolve(r.data))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    createAddress(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('addresses', payload)
                    .then(({data: r}) => resolve(r))
                    .catch(({response: {data: err}}) => reject(err)));
    },
    updateAddress(_, {id, ...restPayload}){
        return new Promise((resolve, reject) =>
            axios.put(`addresses/${id}`, restPayload)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    deleteAddress(_, {id}) {
        return new Promise((resolve, reject) => 
            axios.delete(`addresses/${id}`)
                 .then(({data: r}) => resolve(r))
                 .catch(({response: {data: err}}) => reject(err)));
    },
    
    // TODO - test filters with states
    getStates({commit}, payload) {
        return new Promise((resolve, reject) => 
            axios.get('states')
                 .then(({data: r}) => (r && commit('SET_STATES', r.data), resolve(r)))
                 .catch(({response: {data: err}}) => reject(err)));
    }
}
