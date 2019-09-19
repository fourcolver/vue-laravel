import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    async get ({commit}, payload) {
        try {
            const request = buildFetchUrl('notifications', payload);
            const {data} = await axios.get(request);

            if (payload) {
                data.data.inserting = payload.inserting;
            }

            commit('SET', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async markAsRead ({commit}, payload = {}) {
        const {id = null, ...restPayload} = payload;

        try {
            const request = id ? `notifications/${id}` : 'notifications';
            const {data} = await axios.post(request, restPayload);

            commit('SET_MARKED_AS_READ', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    }
}
