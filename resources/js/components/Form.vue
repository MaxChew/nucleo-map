<template>
  <form
    ref="formRef"
    :method="methodAttributes.spoofMethod"
    :action="action"
    @submit="handleSubmission"
    @input="clearValidationError"
    @change="clearValidationError"
    @keyup.ctrl.enter="triggerSubmit"
  >
    <input
      type="hidden"
      name="_method"
      v-if="methodAttributes.spoofMethod !== methodAttributes.upperMethod"
      :value="methodAttributes.upperMethod"
    />
    <input
      type="hidden"
      name="_token"
      v-if="methodAttributes.upperMethod !== 'GET'"
      :value="methodAttributes.csrfToken"
    />

    <slot
      :verrors="verrors"
      :loading="loading"
      :submitButton="submitButton"
    ></slot>

    <transition name="fade">
      <div
        v-if="loading && loader"
        class="absolute pin flex justify-center items-center bg-white-70 z-30"
      >
        <div class="text-center">
          <span class="loader w-8 h-8"></span>
          <p class="mt-2" v-text="loader"></p>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div
        v-if="error"
        class="fixed flex justify-center items-center w-full h-full bg-black bg-opacity-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50"
      >
        <div class="flex justify-center items-center bg-white border-2 border-black rounded-lg p-5 max-w-full">
          <div class="text-center">
            <h4 class="text-lg font-semibold flex justify-center items-center text-danger-500">
              <img :src="'/images/svg/error.svg'" class="w-7 mr-2" />Error Occurred
            </h4>
            <div class="max-w-md overflow-auto max-h-64 mx-auto py-4 text-black">
              <p v-text="error"></p>
              <pre class="mt-2 text-left" v-if="errorDetail" v-html="errorDetail"></pre>
            </div>
            <button
              type="button"
              @click="dismissError"
              class="mt-4 btn btn-primary w-32 p-2 mx-auto"
            >
              Dismiss
            </button>
          </div>
        </div>
      </div>
    </transition>
  </form>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue';
import qs from 'qs';
import ErrorBag from './../libs/ErrorBag';
import _ from 'lodash';
import axios from 'axios';

export default {
  props: {
    method: {
      type: String,
      default: 'POST',
    },
    action: {
      type: String,
      required: true,
    },
    redirect: {
      type: [String, Boolean],
      default: null,
    },
    ajax: {
      type: Boolean,
      default: false,
    },
    config: {
      type: Object,
      default: () => ({}),
    },
    loader: {
      type: String,
      default: null,
    },
    permanentLoader: {
      type: Boolean,
      default: false,
    },
    clearForm: {
      type: Boolean,
      default: false,
    },
    validator: {
      type: Function,
      default: null,
    },
    skipErrorHandling: {
      type: [Boolean, Array],
      default: false,
    },
    customHandler: {
      type: Function,
      default: null,
    },
    invalidStatuses: {
        type: Array,
        default: () => [422, 400] // 确保这里包含需要特殊处理的状态码
    },
    redirectTemplate: String // 新增属性
  },
  setup(props, { emit }) {
    const formRef = ref(null);
    const continueLoading = ref(!!props.permanentLoader);
    const error = ref(null);
    const errorDetail = ref(null);
    const verrors = ref(new ErrorBag());
    const loading = ref(false);
    const submitButton = ref(null);
    const clearFormSubmission = ref(!!props.clearForm);

    const methodAttributes = computed(() => {
      const upperMethod = props.method.toUpperCase();
      const spoofMethod = ['GET', 'POST', 'PUT', 'DELETE'].includes(upperMethod) ? upperMethod : 'POST';
      const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
      return { upperMethod, spoofMethod, csrfToken };
    });

    const handleSubmission = (e) => {
      if (props.validator && !props.validator(methodAttributes.value)) {
        e.preventDefault();
        return false;
      }

      if (props.customHandler) {
        return props.customHandler(e, methodAttributes.value);
      }

      if (document.activeElement !== document.body) {
        document.activeElement.blur();
      }

      loading.value = true;

      if (props.ajax) {
        e.preventDefault();
        ajaxSubmission();
      }
    };

    const clearValidationError = (e) => {
      let name = e.target.name;
      if (!name) return;
      if (name.endsWith('[]')) name = name.replace(/\[\]$/, '');
      if (verrors.value.has(name)) verrors.value.clear(name);
    };

    const dismissError = () => {
      error.value = null;
      errorDetail.value = null;
    };

    const handleErrorResponse = (error, formData) => {
      console.log('AJAX request Error:', error); // 调试信息
      const { message, detail, response } = error;
      console.log('Error response:', response);

      if (response && props.invalidStatuses.includes(response.status)) {
        if (response.data && response.data.errors) {
          console.log('response.data.errors:', response.data.errors);
          verrors.value.record(response.data.errors);
        } else {
          console.error('response.data.errors is undefined:', response.data);
          verrors.value.record({ general: 'Unknown error occurred' });
        }
        emit('invalid', verrors.value, formData, methodAttributes.value);
        loading.value = false;
      } else {
        if (!props.skipErrorHandling || (Array.isArray(props.skipErrorHandling) && !props.skipErrorHandling.includes(response.status))) {
          error.value = message || 'Unknown Error Occurred';
          errorDetail.value = detail;
        }
        emit(`error-${response.status}`, error, formData, methodAttributes.value);
        emit('failed', error, formData, methodAttributes.value);
        loading.value = false;
      }
      emit('post-submit', response, formData, methodAttributes.value);
    };

    const ajaxSubmission = () => {
      const currentFormData = new FormData(formRef.value);
      const formData = new FormData();

      for (let [name, value] of currentFormData.entries()) {
          if (value instanceof File && value.size) {
              formData.append(name, value);
          } else {
              formData.append(name, value);
          }
      }

      // 将FormData转换为普通对象
      const plainFormData = Object.fromEntries(formData.entries());
      console.log('Submitted form data:', plainFormData);

      const options = _.merge(
        {
          url: props.action,
          method: methodAttributes.value.spoofMethod,
          data: formData,
          headers: {
                'Content-Type': 'multipart/form-data'
            }
        },
        props.config
      );

      

      if (methodAttributes.value.spoofMethod === 'GET') {
        options.params = qs.parse(decodeURI(new URLSearchParams(formData).toString()));
      }

      if (submitButton.value && submitButton.value.name) {
        formData.append(submitButton.value.name, submitButton.value.value);
      }

      verrors.value.clear();
      emit('pre-submit', options, formData, methodAttributes.value);

      axios(options)
        .then(response => {
          console.log('AJAX request successful:', response.data); // 调试信息
          error.value = false;
          emit("success", response.data, formData);
          emit("post-submit", response, formData);
          if (!continueLoading.value) loading.value = false;
          if (clearFormSubmission.value) formRef.value.reset();
        })
        .catch(error => handleErrorResponse(error, formData));
    };

    const triggerSubmit = () => {
      let submitButtonElement = formRef.value.querySelector('[type=submit][name]');
      if (submitButtonElement) submitButtonElement.click();
      else {
        submitButtonElement = document.createElement('button');
        submitButtonElement.type = 'submit';
        formRef.value.appendChild(submitButtonElement);
        submitButtonElement.click();
        submitButtonElement.remove();
      }
    };

    onMounted(async () => {
      await nextTick();
      if (props.ajax) {
        Array.from(formRef.value.querySelectorAll('[type=submit]')).forEach((button) =>
          button.addEventListener('click', function () {
            submitButton.value = this;
          })
        );
      }
      emit('submit', triggerSubmit);
    });

    return {
      formRef,
      handleSubmission,
      clearValidationError,
      dismissError,
      methodAttributes,
      loading,
      verrors,
      error,
      errorDetail,
      submitButton,
    };
  },
};
</script>