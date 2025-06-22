import { redirect } from './helpers';
import Swal from 'sweetalert2';

export default class Alert {
    alert(type, title, html, redirectTo, iconHtml) {
        const promise = Swal.fire({
            icon: type,
            iconHtml: iconHtml,
            customClass: {
                icon: 'sweet-alert-css',
            },
            title: title,
            text: html,
        });

        if (redirectTo) {
            promise
                .then(() => { redirect(redirectTo); })
                .catch(() => { redirect(redirectTo); });
        }

        return promise;
    }

    confirm(title, text, redirectTo) {
        const promise = Swal.fire({
            icon: 'question',
            customClass: {
                confirmButton: '!text-white !bg-primary',
            },
            title: title,
            text: text,
            showCancelButton: true,
        });

        if (redirectTo) {
            promise
                .then(() => { redirect(redirectTo); })
                .catch(() => {}); // No operation
        }

        return promise;
    }

    warning(title, text, redirect) {
        const icon = '<img src="/images/svg/error.svg">';
        return this.alert('warning', title, text, redirect, icon);
    }

    info(title, text, redirect) {
        const icon = '<img src="/images/svg/correct.svg">';
        return this.alert('info', title, text, redirect, icon);
    }

    success(title, text, redirect, method = 'admin') {
        let icon = "";
        if (method === 'user' || method === 'admin') {
            icon = '<img src="/images/svg/correct.svg">';
        }

        return this.alert('success', title, text, redirect, icon);
    }

    error(title, text, redirect) {
        const icon = '<img src="/images/svg/error.svg">';
        return this.alert('error', title, text, redirect, icon);
    }
}

// 簡化版本 - 用於 map.js
export const SimpleAlert = {
    success(message, text = null) {
        return Swal.fire({
            icon: 'success',
            title: message,
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    },

    error(message, text = null) {
        return Swal.fire({
            icon: 'error',
            title: message,
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    },

    warning(message, text = null) {
        return Swal.fire({
            icon: 'warning',
            title: message,
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    },

    info(message, text = null) {
        return Swal.fire({
            icon: 'info',
            title: message,
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    }
};