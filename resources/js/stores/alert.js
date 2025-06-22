// stores/alert.js
import { defineStore } from 'pinia'

export const useAlertStore = defineStore('alert', {
  state: () => {
    // 加载保存的alerts
    let savedAlerts = JSON.parse(sessionStorage.getItem('alerts')) || [];
    
    // 清理过期的通知
    if (savedAlerts.length > 0) {
      const now = Date.now();
      savedAlerts = savedAlerts.filter(alert => {
        // 检查通知是否有结束时间，如果有，则判断是否已过期
        if (alert.expiresAt && alert.expiresAt <= now) {
          return false; // 已过期，过滤掉
        }
        
        // 如果没有结束时间但有时间戳，则使用默认时长3秒检查是否过期
        if (!alert.expiresAt && alert.timestamp) {
          return (now - alert.timestamp) < 3000;
        }
        
        return true; // 保留其他通知
      });
      
      // 更新sessionStorage中的通知
      sessionStorage.setItem('alerts', JSON.stringify(savedAlerts));
    }
    
    return {
      alerts: savedAlerts
    }
  },
  
  getters: {
    sortedAlerts: (state) => {
      return [...state.alerts].sort((a, b) => b.timestamp - a.timestamp)
    }
  },
  
  actions: {
    addAlert(message, type = 'info', duration = 3000) {
      const id = Date.now() + Math.random();
      const timestamp = Date.now();
      const expiresAt = timestamp + duration;
      
      const alert = { 
        id, 
        message, 
        type, 
        timestamp,
        expiresAt // 添加精确的过期时间戳
      };
      
      this.alerts.push(alert);

      // 持久化保存到sessionStorage
      sessionStorage.setItem('alerts', JSON.stringify(this.alerts));

      if (duration > 0) {
        setTimeout(() => {
          this.removeAlert(id);
        }, duration);
      }
      
      return id;
    },
    
    removeAlert(id) {
      // 查找要删除的通知
      const index = this.alerts.findIndex(alert => alert.id === id);
      
      // 如果找到了，从数组中删除并更新sessionStorage
      if (index > -1) {
        this.alerts.splice(index, 1);
        
        // 确保立即更新sessionStorage
        try {
          sessionStorage.setItem('alerts', JSON.stringify(this.alerts));
        } catch (e) {
          console.error('Failed to update sessionStorage:', e);
        }
      }
    },
    
    clearAlerts() {
      this.alerts = [];
      sessionStorage.removeItem('alerts'); // 清除sessionStorage
    },
    
    // 每次页面加载时检查并清理过期通知
    cleanExpiredAlerts() {
      const now = Date.now();
      const alertsBefore = this.alerts.length;
      
      // 过滤掉过期的通知
      this.alerts = this.alerts.filter(alert => {
        if (alert.expiresAt) {
          return alert.expiresAt > now;
        }
        return (now - alert.timestamp) < 3000;
      });
      
      // 如果数量有变化，更新sessionStorage
      if (alertsBefore !== this.alerts.length) {
        sessionStorage.setItem('alerts', JSON.stringify(this.alerts));
      }
    }
  }
});
