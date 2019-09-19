import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    async get({commit, rootState}, {id, inserting, ...payload}) {
        try {
            const request = buildFetchUrl('comments', {
                id,
                commentable: 'post',
                ...payload
            });
            const {data} = await axios.get(request);

            data.data.postId = id;
            data.data.inserting = inserting;
            data.data.data.forEach(comment => {
                const loggedInUser = rootState.users.loggedInUser;

                comment.reversed = loggedInUser.id !== comment.user_id;
            });

            commit('SET_POST_COMMENTS', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async children({}, {id, ...payload}) {
        try {
            const request = buildFetchUrl(`comments/${id}`, {
                commentable: 'post',
                ...payload
            });
            const {data} = await axios.get(request);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async create({commit}, {id, ...payload}) {
        try {
            const request = buildFetchUrl(`posts/${id}/comments`, {
                comment: payload.comment
            });
            const {data} = await axios.post(request);

            data.data.postId = id;
            data.data.reversed = false;

            commit('SAVE_POST_COMMENT', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    update({commit}, payload) {
        console.log('update', payload)
        return new Promise((resolve, reject) => {
            axios.put(buildFetchUrl(`comments/${payload.modelId}`, {
                comment: payload.comment
            })).then(({data}) => {
                data.data.postId = payload.postId;

                commit('UPDATE_POST_COMMENT', data.data);
                resolve(data.data);
            }).catch(err => {
                reject(err)
            });
        });
    }
}
