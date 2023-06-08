import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = '/api'
window.axios.interceptors.response.use(
    response => response,
    error => {
        if(error.status === 403){
            location.href = 'admin/login'
        }

        return Promise.reject(error);
    }
);
