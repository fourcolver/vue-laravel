import axios from '@/axios'

export default {
    async upload ({}, {id, type, ...payload}) {
        try {
            if (!['buildings', 'products', 'posts', 'tenants', 'requests'].includes(type)) {
                throw new Error('Invalid type')
            }
    
            const request = `${type}/${id}/media`
            const {data} = await axios.post(request, payload)

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },
    async delete ({}, {id, media_id, type, ...payload}) {
        try {
            if (!['buildings', 'products', 'posts', 'tenants', 'requests'].includes(type)) {
                throw new Error('Invalid type')
            }
    
            const request = `${type}/${id}/media/${media_id}`
            const {data} = await axios.delete(request, payload)

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    }
}