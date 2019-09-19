import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    async get({commit, rootState}, {id, inserting, ...payload}) {
        try {
            const request = buildFetchUrl('comments', {
                id,
                commentable: 'product',
                ...payload
            });
            const {data} = await axios.get(request);

            data.data.productId = id;
            data.data.inserting = inserting;
            data.data.data.forEach(comment => {
                const loggedInUser = rootState.users.loggedInUser;

                comment.reversed = loggedInUser.id !== comment.user_id;
            });

            commit('SET_PRODUCT_COMMENTS', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async children({}, {id, ...payload}) {
        try {
            const request = buildFetchUrl(`comments/${id}`, {
                commentable: 'product',
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
            const request = buildFetchUrl(`products/${id}/comments`, {
                ...payload
            });
            const {data} = await axios.post(request);

            data.data.productId = id;
            data.data.reversed = false;

            commit('SAVE_PRODUCT_COMMENT', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    },
    async update({commit}, payload) {
        try {
            const request = buildFetchUrl(`comments/${payload.modelId}`, {
                comment: payload.comment
            });
            const {data} = await axios.put(request);

            data.data.productId = payload.postId;

            commit('UPDATE_PRODUCT_COMMENT', data.data);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err);
        }
    }
}
