import axios from '@/axios'

export default {
    async like ({commit, rootGetters}, {id, type, ...payload}) {
        try {
            if (!['posts', 'products'].includes(type)) {
                return Promise.reject(`Invalid type ${type}`)
            }
    
            const request = `${type}/${id}/like`
            const {data} = await axios.post(request, payload)

            const getters = {posts: 'posts2/getById', products: 'products2/getById'}
            const commits = {posts: 'posts2/PARTIAL_UPDATE', products: 'products2/PARTIAL_UPDATE'}

            const {likes, likes_count} = rootGetters[getters[type]](id)

            commit(commits[type], {
                id,
                data: {
                    liked: true,
                    likes: likes.concat([data.data]),
                    likes_count: likes_count + 1
                }
            }, {root: true})

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    },
    async unlike ({rootGetters, commit}, {id, type, ...payload}) {
        try {
            if (!['posts', 'products'].includes(type)) {
                return Promise.reject(`Invalid type ${type}`)
            }
    
            const request = `${type}/${id}/unlike`
            const {data} = await axios.post(request, payload)

            const getters = {posts: 'posts2/getById', products: 'products2/getById'}
            const commits = {posts: 'posts2/PARTIAL_UPDATE', products: 'products2/PARTIAL_UPDATE'}

            const {likes, likes_count} = rootGetters[getters[type]](id)

            commit(commits[type], {
                id,
                data: {
                    liked: false,
                    likes: likes.filter(like => like.id !== data.data.id),
                    likes_count: likes_count - 1
                }
            }, {root: true})

            return Promise.resolve(data)
        } catch (err) {
            return Promise.reject(err)
        }
    }
}