// plugins/alert.js
import { useAlertStore } from '../stores/alert';
import { redirect } from '../libs/helpers'; // Assuming redirect function is imported

export const useAlert = {
  install: (app) => {
    const alert = {
      show(message, type = 'info', redirectTo = null) {
        const store = useAlertStore();

        if (redirectTo) {
          // Store the alert in sessionStorage before redirecting
          sessionStorage.setItem('alert', JSON.stringify({ message, type }));

          // Perform the redirection
          setTimeout(() => {
            redirect(redirectTo);
          }, 0);
        }

        // If no redirection is needed, just show the alert
        setTimeout(() => {
          store.addAlert(message, type, 3000); // Default duration set to 3000
        }, 500); // Small delay to ensure the alert is processed first
      },

      // plugins/alert.js
      confirm(options, onCancel = null) {
        const {
          title = '',
          text = '',
          icon = 'question',  // 改为 warning 更适合确认场景
          confirmButtonText = 'Confirm',  // 更正式的按钮文本
          cancelButtonText = 'Cancel',
          redirectTo = null,
          buttonId = null, 
        } = options || {};

        return Swal.fire({
          title,
          text,
          icon,
          showCancelButton: true,
          confirmButtonText,
          cancelButtonText,
          // 优化样式类
          customClass: {
            popup: 'bg-bg-dark shadow-xl rounded-xl p-6',
            title: 'text-xl font-semibold dark:text-white mb-2',
            htmlContainer: 'text-base dark:text-gray-300 my-4',
            actions: 'mt-6',  // 增加按钮区域间距
            confirmButton: 'btn bg-red-500 hover:bg-red-600 text-white mr-3',  // 危险操作使用红色
            cancelButton: 'btn bg-gray-200 hover:bg-gray-300 text-gray-700',
          },
          buttonsStyling: false,
          // 增加动画效果
          showClass: {
            popup: 'animate__animated animate__fadeIn animate__faster'
          },
          hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            if (buttonId) {
              const submitButton = document.getElementById(buttonId);
              if (submitButton) {
                submitButton.click();
              }
            }

            if (redirectTo) {
              redirect(redirectTo);
            }
            return true;  // 返回确认结果
          } else if (result.isDismissed && onCancel) {
            onCancel();
          }
          return false;  // 返回取消结果
        });
      },

      success(message, text = null, redirectTo = null) {
        return this.show(message, 'success', redirectTo);
      },

      error(message, text = null, redirectTo = null) {
        return this.show(message, 'error', redirectTo);
      },

      warning(message, text = null, redirectTo = null) {
        return this.show(message, 'warning', redirectTo);
      },

      info(message, text = null, redirectTo = null) {
        return this.show(message, 'info', redirectTo);
      },

      clear() {
        const store = useAlertStore();
        store.clearAlerts();
        sessionStorage.removeItem('alert'); // Clear the alert from sessionStorage
      }
    }

    app.config.globalProperties.$alert = alert;
    app.provide('alert', alert);
  }
}
