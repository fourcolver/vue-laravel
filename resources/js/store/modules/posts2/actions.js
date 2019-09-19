// TODO - will eventually replace the old "products"
import axios from '@/axios'
import queryString from 'query-string'

export default {
    async get ({commit}, payload = {}) {
        try {
            const {id, ...restPayload} = payload
            
            let request = id ? `posts/${id}`: 'posts'
            
            if (Object.keys(restPayload).length) {
                request += '?' + queryString.stringify(restPayload)
            }

            const {data} = await axios.get(request)

            commit('set', data.data)

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },
    // async create ({commit}, payload) {
    //     try {
    //         const data = await axios.post('products', payload)

    //         commit('SAVE', data.data)

    //         return Promise.resolve(data)
    //     } catch (err) {
    //         return Promise.reject(err)
    //     }
    // },
    // async update ({commit}, {id, ...payload}) {
    //     try {
    //         const data = await axios.put(`products/${id}`, payload)

    //         commit('UPDATE', data)

    //         return Promise.resolve(data)
    //     } catch (err) {
    //         return Promise.reject(err)
    //     }
    // },
    // async delete ({commit}, {id}) {
    //     try {
    //         const data = await axios.delete(`products/${id}`)

    //         commit('DELETE', data.data)

    //         return Promise.resolve(data)
    //     } catch (err) {
    //         return Promise.reject(err)
    //     }
    // }
}