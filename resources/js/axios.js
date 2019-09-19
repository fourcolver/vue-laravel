import axios from "axios";
import router from './routes';
import {API_BASE_URL} from './config';

const base = axios.create({
    baseURL: API_BASE_URL,
});

base.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    // const {page, per_page, ...otherParams} = router.currentRoute.query;
    //
    //
    //
    // if (config.method === 'get' && page && per_page) {
    //     config.params = {page, per_page, ...otherParams};
    // }

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
}, err => Promise.reject(err));

base.interceptors.response.use(config => {
    if (config.config.method === 'get') {
        const data = config.data.data;

        if (data) {
            const {current_page, last_page} = data;

            if (current_page && last_page && current_page > last_page) {
                router.replace({
                    name: router.name,
                    query: {
                        ...router.currentRoute.query,
                        page: last_page
                    },
                    params: {
                        ...router.currentRoute.params
                    }
                });
            }
        }
    }

    return config;
}, err => Promise.reject(err));

export default base;
