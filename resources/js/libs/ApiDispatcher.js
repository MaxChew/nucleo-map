import axios from 'axios';
import { redirect } from './helpers';
import ErrorBag from './ErrorBag';

export default class ApiDispatcher {
    constructor(app) {
        this.app = app;
        this.alert = app.config.globalProperties.$alert;
        console.log('ApiDispatcher initialized with alert:', this.alert); // 调试日志
    }

    request(method, url, data, redirectTo, event) {
        const options = method.toLowerCase() === 'get' ?
            { url, method, params: data } : { url, method, data };
        let element = event ? event.currentTarget || event.target : false;
        element && element.classList.add('loading');

        console.log('API request options:', options); // 添加日志记录请求选项

        return axios(options).then((response) => {
            //console.log('API response:', response); // 添加日志记录响应
            const { data } = response;
            if (data.message) {
                this.alert.success(data.message, null, redirectTo);
            } else if (redirectTo) {
                return redirect(redirectTo);
            }
            element && element.classList.remove('loading');
            return data; // 确保返回数据
        }).catch((error) => {
            console.error('API request failed:', error); // 添加日志记录错误
            if (error.response && error.response.status === 422) {
                const message = (new ErrorBag(error.response.data.data)).toListItem();
                this.alert.error(message || 'Unknown Error Occurred');
            } else {
                this.alert.error(error.message || 'Unknown Error Occurred');
            }
            element && element.classList.remove('loading');
            return Promise.reject(error);
        });
    }

    get(url, params, redirect, event) {
        return this.request('get', url, params, redirect, event);
    }

    post(url, data, redirect, event) {
        return this.request('post', url, data, redirect, event);
    }

    patch(url, data, redirect, event) {
        return this.request('patch', url, data, redirect, event);
    }

    put(url, data, redirect, event) {
        return this.request('put', url, data, redirect, event);
    }

    destroy(url, data, redirect, event) {
        return this.request('delete', url, data, redirect, event);
    }
}