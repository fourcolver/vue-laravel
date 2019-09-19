import axios from '@/axios';

export default {
    getReqStatus({commit}, payload) {
        return new Promise((resolve, reject) =>
            axios.get('requests/statistics')
                .then(({data: r}) => (r && commit('SET_REQSTATUS', r.data), resolve(r)))
                .catch(({response: {data: err}}) => reject(err)));
    }    
}
