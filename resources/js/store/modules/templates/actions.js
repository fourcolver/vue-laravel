import axios from '@/axios';
import {buildFetchUrl} from 'helpers/url';

export default {
    getTemplates({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get(buildFetchUrl('templates', payload))
                .then(({data: r}) => (r && commit('SET_TEMPLATES', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getTemplate(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.get(`templates/${id}`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    },
    createTemplate(_, payload) {
        return new Promise((resolve, reject) =>
            axios.post('templates', payload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    updateTemplate(_, {id, ...restPayload}) {
        return new Promise((resolve, reject) =>
            axios.put(`templates/${id}`, restPayload)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    deleteTemplate(_, {id}) {
        return new Promise((resolve, reject) =>
            axios.delete(`templates/${id}`)
                .then(({data: r}) => resolve(r))
                .catch(({response: {data: err}}) => reject(err)));
    },
    getTemplateCategories(_, payload) {
        return new Promise((resolve, reject) =>
            axios.get(`templates/categories`)
                .then(({data: r}) => resolve(r.data))
                .catch(({response: {data: err}}) => reject(err)));
    }
}
