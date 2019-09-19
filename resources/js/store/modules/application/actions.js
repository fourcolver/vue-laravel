import axios from '@/axios'
import queryString from 'query-string'
import { buildFetchUrl } from 'helpers/url';

export default {
    async constants({ commit }, payload = {}) {
        try {
            const request = 'constants'

            if (Object.keys(payload).length) {
                request += `?${queryString.stringify(payload)}`
            }

            const { data } = await axios.get(request);

            commit('set_constants', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async fetchAudits({ commit }, { auditable_id, auditable_type, ...restPayload }) {
        try {
            const { data } = await axios.get(`audits?${queryString.stringify({auditable_id, auditable_type, ...restPayload})}`)

            commit('set_audits', {
                id: auditable_id,
                type: auditable_type,
                data: data.data
            })

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    }
}