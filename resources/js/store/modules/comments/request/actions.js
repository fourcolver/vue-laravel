import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';
import queryString from 'query-string'

export default {
    async get ({commit, getters, rootState}, {id, inserting, ...payload}) {
        let params = {
            id,
            commentable: 'request',
            ...payload
        };

        if (inserting) {
            const currComments = getters.get(id).data
            const newComments = currComments.filter(c => c.new);

            params.exclude = currComments.slice(0, newComments.length).reduce((acc, val) => {
                acc.push(val.id)

                return acc
            }, [])
        }
        
        try {
            const request = 'comments?' + queryString.stringify(params, {
                arrayFormat: 'bracket'
            })
            const {data} = await axios.get(request);

            data.data.requestId = id;
            data.data.inserting = inserting;
            data.data.data.forEach(comment => {
                const loggedInUser = rootState.users.loggedInUser;

                comment.reversed = loggedInUser.id !== comment.user_id;
            });

            commit('SET_REQUEST_COMMENTS', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async create ({commit}, {id, ...payload}) {
        try {
            const {data} = await axios.post(`requests/${id}/comments`, payload);

            data.data.requestId = id;
            data.data.reversed = false;
            data.data.new = true;

            commit('SAVE_REQUEST_COMMENT', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async update ({commit}, {id, requestId, ...payload}) {
        try {
            const request = buildFetchUrl(`comments/${id}`, {
                commentable: 'request',
                ...payload
            });
            const {data} = await axios.put(request);

            data.data.requestId = requestId;

            commit('UPDATE_REQUEST_COMMENT', data.data);

            return Promise.resolve(data);
        } catch (err) {
            console.error(err);
            return Promise.reject(err);
        }
    }
}
