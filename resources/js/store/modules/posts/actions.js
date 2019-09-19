import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getPosts({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('posts', payload))
                .then(({data: r}) => (r && commit('SET_POSTS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getPost(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`posts/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createPost(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('posts', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updatePost(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`posts/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deletePost(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`posts/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    likePost(_, id) {
        return new Promise((resolve, reject) =>
            axios.post(`posts/${id}/like`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    unlikePost(_, id) {
        return new Promise((resolve, reject) =>
            axios.post(`posts/${id}/unlike`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    changePostPublish(_, {id, ...body}) {
        return new Promise((resolve, reject) =>
            axios.post(`posts/${id}/publish`, body)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    uploadPostMedia(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.post(`posts/${id}/media`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deletePostMedia({}, {id, media_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`posts/${id}/media/${media_id}`).then((resp) => {
                resolve(resp.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    getArticlePosts({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('posts', {...payload, type: 1}))
                .then(({data: r}) => (r && commit('SET_POSTS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getPostsTruncated({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('posts', payload))
                .then(({data: r}) => {
                    r.data.data = r.data.data.map((post) => {
                        post.preview = `${post.content.substring(0, 50)}...`;
                        return post;
                    });
                    resolve(r)
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    assignPostDistrict({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`posts/${payload.id}/districts/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    assignPostBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`posts/${payload.id}/buildings/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    unassignPostBuilding({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`posts/${payload.id}/buildings/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.post.unassigned.building'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    unassignPostDistrict({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`posts/${payload.id}/districts/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.post.unassigned.district'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    getPostAssignments({}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(buildFetchUrl(`posts/${payload.post_id}/locations`, payload)).then((response) => {
                response.data.data.data = response.data.data.data.map((building) => {
                    if (building.type === 'building') {
                        building.aType = 1;
                        building.assignmentType = 'building';
                    } else {
                        building.aType = 2;
                        building.assignmentType = 'district';
                    }
                    return building;
                });
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    assignPostProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`posts/${payload.id}/providers/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    unassignPostProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`posts/${payload.id}/providers/${payload.toAssignId}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.post.unassigned.provider'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
}
