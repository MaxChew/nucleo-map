import qs from 'qs';
import _ from 'lodash';
import { route as ziggyRoute } from 'ziggy-js';
import { Ziggy } from './../ziggy';

export function matchUrlPattern(pattern, url) {
    const regex = new RegExp("^" + pattern.split("*").join("[^/]+") + "[/]?$");
    return regex.test(url);
}

export function redirect(url, notification, notificationType) {
    if (!url) return;
    if (url === true) return window.location.reload();

    if (notification) {
        const query = qs.stringify({ notification: notificationType || 'info', message: notification });
        url = (url.indexOf('?') == -1) ? `${url}?${query}` : `${url}&${query}`;
    }
    return window.location = url;
}

export function passport_url(url, params) {
    url = window.app.passport_base_url + _.trimStart(url, '/');
    if (params) {
        let query = qs.stringify(params);
        url += (url.indexOf('?') == -1) ? `?${query}` : `&${query}`;
    }
    return url;
}

export function dd(data) {
    console.log(data);
}

export function route(name, params, absolute, customZiggy) {
    return ziggyRoute(name, params, absolute, customZiggy || Ziggy);
}

/**
 * 处理全局点击事件，用于自动关闭下拉菜单
 * @param {Event} e - 点击事件对象
 */
export function onDocumentClick(e) {
    // 检查点击的元素是否在下拉菜单内
    const dropdownContainers = document.querySelectorAll('.dropdown-container');
    let isInsideDropdown = false;
    
    // 检查所有下拉菜单
    for (let i = 0; i < dropdownContainers.length; i++) {
        if (dropdownContainers[i].contains(e.target)) {
            isInsideDropdown = true;
            break;
        }
    }
    
    // 如果点击在下拉菜单外部，关闭所有下拉菜单
    if (!isInsideDropdown) {
        closeAllDropdowns();
    }
}

/**
 * 关闭所有下拉菜单
 */
export function closeAllDropdowns() {
    console.log("closeAllDropdowns");
    // 查找所有下拉菜单组件并关闭它们
    const dropdownComponents = document.querySelectorAll('.dropdown-container');
    
    for (let i = 0; i < dropdownComponents.length; i++) {
        const dropdown = dropdownComponents[i];
        
        // 获取下拉菜单的父元素，它可能包含组件实例
        const dropdownWrapperEl = dropdown.parentElement;
        
        try {
            // 尝试访问Vue 3的 __vnode 或 Vue 2的 __vue__
            const componentInstance = 
                // Vue 3
                (dropdownWrapperEl && dropdownWrapperEl.__vnode && dropdownWrapperEl.__vnode.component && dropdownWrapperEl.__vnode.component.exposed) ||
                // 另一种Vue 3访问方式
                (dropdown && dropdown.__vnode && dropdown.__vnode.component && dropdown.__vnode.component.exposed) ||
                // Vue 2
                (dropdownWrapperEl && dropdownWrapperEl.__vue__);
            
            // 如果找到了组件实例，调用其toggle方法
            if (componentInstance && typeof componentInstance.toggle === 'function') {
                componentInstance.toggle(false);
            }
        } catch (e) {
            console.error('尝试关闭下拉菜单时出错:', e);
        }
    }
    
    // 发送ESC键盘事件，这对于许多下拉菜单组件都有效
    try {
        document.dispatchEvent(new KeyboardEvent('keydown', {
            key: 'Escape',
            code: 'Escape',
            keyCode: 27,
            which: 27,
            bubbles: true
        }));
    } catch (e) {
        console.error('发送ESC键事件失败:', e);
    }
}

/**
 * 格式化代码，适应不同屏幕宽度
 * @param {string} code - 代码
 * @returns {string} - 格式化后的代码
 */
export function formatCode(code) {
    if (!code) return '';
    
    // 如果代码长度大于10个字符，使用缩写格式
    if (code.length > 12) {
        // 保留前3个字符和最后4个字符，中间用...替代
        return code.substring(0, 3) + '...' + code.substring(code.length - 4);
    }
    
    return code;
}

/**
 * 格式化姓名，适应不同屏幕宽度
 * @param {string} name - 姓名
 * @returns {string} - 格式化后的姓名
 */
export function formatName(name) {
    if (!name) return '';
    
    // 如果姓名长度大于15个字符，使用缩写格式
    if (name.length > 15) {
        // 保留前10个字符和最后5个字符，中间用...替代
        return name.substring(0, 10) + '...' + name.substring(name.length - 5);
    }
    
    return name;
}

// 为了向后兼容，保留原来的函数名
export const formatStudentCode = formatCode;
export const formatStudentName = formatName;