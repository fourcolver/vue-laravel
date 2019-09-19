import axios from '@/axios';

export default {
    sendCleanifyRequest({}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`cleanify`, payload).then((resp) => {
                resolve(resp);
            }).catch((error) => {
                reject(error);
            })
        });
    }
}
