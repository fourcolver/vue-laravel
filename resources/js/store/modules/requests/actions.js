import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';
import queryString from 'query-string'

export default {
    getRequests({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('requests', payload))
                .then(({data: r}) => (r && commit('SET_REQUESTS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.post('requests', payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    updateRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.put(`requests/${payload.id}`, payload).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    getRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.get(`requests/${payload.id}`).then((response) => {
                resolve(response.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    deleteRequest({commit}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${payload.id}`).then((response) => {
                resolve({
                    success: true,
                    message: 'models.request.deleted'
                })
            }).catch((error) => {
                reject(error.response.data)
            })
        })
    },
    async addRequestComment({}, {id, ...payload}) {
        try {
            const {data} = await axios.post(`requests/${id}/comments`, payload);

            return Promise.resolve(data);
        } catch (err) {
            return Promise.reject(err.response.data);
        }
    },
    uploadRequestMedia({}, {id, ...payload}) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${id}/media`, payload).then((resp) => {
                resolve(resp.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    deleteRequestMedia({}, {id, media_id}) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${id}/media/${media_id}`).then((resp) => {
                resolve(resp.data);
            }).catch((error) => {
                reject(error.response.data);
            });
        });
    },
    sendServiceRequestMail({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/notify`, payload).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    assignManager({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/assignees/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    assignProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`requests/${payload.request}/providers/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    unassignProvider({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${payload.request}/providers/${payload.toAssignId}`).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    unassignManager({}, payload) {
        return new Promise((resolve, reject) => {
            axios.delete(`requests/${payload.request}/assignees/${payload.toAssignId}`, {}).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    },
    getAssignees({commit}, payload) {
        return new Promise((resolve, reject) => {
                axios.get(buildFetchUrl(`requests/${payload.request_id}/assignees`, payload))
                    .then(({data: r}) => {
                        if (!Array.isArray(r.data.data)) {
                            r.data.data = Object.values(r.data.data);
                        }

                        r.data.data = r.data.data.map((user) => {
                            if (user.type == 'provider') {
                                user.uType = 1;
                            } else {
                                user.uType = 2;
                            }
                            return user;
                        });

                        resolve(r)
                    })
                    .catch(({response: {data: err}}) => reject(err));
            }
        );
    },
    getRequestConversations({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('conversations', {
                ...payload,
                conversationable: 'request',
                get_all: true
            }))
                .then(({data: r}) => {
                    resolve(r);
                })
                .catch(({response: {data: err}}) => reject(err)));
    },
    async getRequestTemplates ({commit}, {id}) {
        const {data} = await axios.get(`requests/${id}/communicationTemplates`)

        commit('SAVE_REQUEST_TEMPLATES', {id, data: data.data})
    }
}
