import axios from '@/axios'
import queryString from 'query-string'

export default {
    async get({commit, getters}, {parent_id, ...payload}) {
        if (getters.get(payload.id, payload.commentable)) {
            const currentComments = getters.get(payload.id, payload.commentable).data;
            const newComments = currentComments.filter(comment => comment.new);

            payload.exclude = currentComments.slice(0, newComments.length).reduce((acc, {id}) => {
                acc.push(id);

                return acc
            }, [])
        }

        let params = payload, request = 'comments';

        if (payload.id && parent_id) {
            const {id, ...restParams} = params;

            const comment = getters.get(payload.id, payload.commentable).data.find(({id}) => id === parent_id);

            params = restParams;

            if (comment) {
                const children = comment.children.data;
                const newChildren = children.filter(c => c.new);

                params.exclude = children.slice(0, newChildren.length).reduce((acc, {id}) => {
                    acc.push(id);

                    return acc
                }, [])
            }

            request = `comments/${parent_id}`
        }

        try {
            const {data} = await axios.get(`${request}?${queryString.stringify(params, {arrayFormat: 'bracket'})}`);

            commit('set', {
                parent_id,
                id: payload.id,
                data: data.data,
                commentable: payload.commentable
            });

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },
    async create({commit, rootGetters}, {id, ...payload}) {
        try {
            const request = {post: 'posts', product: 'products', request: 'requests', conversation: 'conversations'};

            const {data} = await axios.post(`${request[payload.commentable]}/${id}/comments`, payload);

            commit('create', {id, parent_id: payload.parent_id, commentable: payload.commentable, data: data.data});

            // TODO - delete products2 after used everywhere later
            if (request[payload.commentable] === 'products') {
                let {comments_count} = rootGetters['products2/getById'](id);

                commit('products2/PARTIAL_UPDATE', {
                    id,
                    parentId: payload.parent_id,
                    data: {comments_count: comments_count + 1}
                }, {root: true})
            }

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },
    async update({commit}, {id, parent_id, child_id, ...payload}) {
        try {
            const {data} = await axios.put(`comments/${child_id ? child_id : parent_id}?${queryString.stringify(child_id ? {parent_id, ...payload} : payload)}`);

            commit('update', {id, parent_id, child_id, commentable: payload.commentable, data: data.data})

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },
    async delete ({commit, dispatch}, {id, parent_id, child_id, ...payload}) {
        try {
            const {data} = await axios.delete(`comments/${child_id ? child_id : parent_id}?${queryString.stringify(child_id ? {parent_id, ...payload} : payload)}`)

            commit('delete', {id, parent_id, child_id, commentable: payload.commentable, data: data.data})

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },

    clear({commit}, commentable) {
        commit('clear', commentable)
    }
}
