<template>
    <form
        :method="spoofMethod"
        :action="action"
        @submit="handleSubmission"
        @input="clearValidationError"
        @change="clearValidationError"
        @keyup.ctrl.enter="triggerSubmit"
    >
        <input
            type="hidden"
            name="_method"
            v-if="spoofMethod !== upperMethod"
            :value="upperMethod"
        />
        <input
            type="hidden"
            name="_token"
            v-if="upperMethod !== 'GET'"
            :value="csrfToken"
        />

        <slot
            :verrors="verrors"
            :loading="loading"
            :submitButton="submitButton"
        ></slot>

        <transition name="fade">
            <div v-if="loading && loader" class="absolute pin flex justify-center items-center bg-white-70 z-30">
                <div class="text-center">
                    <span class="loader w-8 h-8"></span>
                    <p class="mt-2">{{ loader }}</p>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="error" class="fixed flex justify-center items-center w-full h-full bg-black bg-opacity-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50">
                <div class="flex justify-center items-center bg-white border-2 border-black rounded-lg p-5 max-w-full">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold flex justify-center items-center text-danger-500">
                            <img :src="'/images/svg/error.svg'" class="w-7 mr-2" />
                            Error Occurred
                        </h4>
                        <div class="max-w-md overflow-auto max-h-64 mx-auto py-4 ">
                            <p>{{ error }}</p>
                            <pre class="mt-2 text-left" v-if="errorDetail" v-html="errorDetail"></pre>
                        </div>
                        <button type="button" @click="dismissError" class="mt-4 btn btn-primary w-32 p-2 mx-auto">
                            Dismiss
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </form>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import qs from 'qs';
import ErrorBag from './../libs/ErrorBag';
import axios from 'axios';

export default {
    props: {
        method: {
            type: String,
            default: "POST"
        },
        action: String,
        redirect: [String, Boolean],
        ajax: {
            type: Boolean,
            default: false
        },
        config: {
            type: Object,
            default: () => ({})
        },
        loader: {
            type: String,
            default: null
        },
        permanentLoader: Boolean,
        clearForm: Boolean,
        validator: Function,
        skipErrorHandling: [Boolean, Array],
        customHandler: Function,
        invalidStatuses: {
            type: Array,
            default: () => [422, 400] // 确保这里包含需要特殊处理的状态码
        },
        redirectTemplate: String // 新增属性
    },
    setup(props, { emit }) {
        const continueLoading = ref(!!props.permanentLoader);
        const error = ref(null);
        const errorDetail = ref(null);
        const verrors = reactive(new ErrorBag());
        const loading = ref(false);
        const submitButton = ref(null);
        const clearFormSubmission = ref(!!props.clearForm);

        const spoofMethod = computed(() => {
            return upperMethod.value === "GET" || upperMethod.value === "POST" ? upperMethod.value : "POST";
        });

        const upperMethod = computed(() => props.method.toUpperCase());

        const csrfToken = computed(() => {
            return document.head.querySelector('meta[name="csrf-token"]').content;
        });

        const handleSubmission = (e) => {
            e.preventDefault(); // 确保在调用 ajaxSubmission 时阻止默认行为

            if (props.validator && !props.validator()) {
                return false;
            }

            if (props.customHandler) {
                return props.customHandler(e);
            }

            if (document.activeElement !== document.body) {
                document.activeElement.blur();
            }

            loading.value = true;

            if (props.ajax) {
                ajaxSubmission(e);
            }
        };

        const clearValidationError = (e) => {
            let name = e.target.name;
            if (!name) return;

            if (name.endsWith("[]")) {
                name = name.slice(0, -2);
            }
            if (verrors.has(name)) {
                verrors.clear(name);
            }
        };

        const dismissError = () => {
            error.value = null;
            errorDetail.value = null;
        };

        const ajaxSubmission = (e) => {
            let currentFormData = new FormData(e.target);
            let formData = new FormData();

            for (let [name, value] of currentFormData.entries()) {
                if (value instanceof File && value.size) {
                    formData.append(name, value);
                } else {
                    formData.append(name, value);
                }
            }

            let options = {
                url: props.action,
                method: spoofMethod.value,
                data: formData,
                ...props.config
            };

            if (spoofMethod.value === "GET") {
                options.params = qs.parse(decodeURI(new URLSearchParams(formData).toString()));
            }

            if (submitButton.value && submitButton.value.name) {
                formData.append(submitButton.value.name, submitButton.value.value);
            }

            verrors.clear();
            emit("pre-submit", options, formData);

            axios(options)
            .then(response => {
                console.log('AJAX request successful:', response.data); // 调试信息
                error.value = false;
                emit("success", response.data, formData);
                emit("post-submit", response, formData);
                if (!continueLoading.value) {
                    loading.value = false;
                }
                if (clearFormSubmission.value) {
                    e.target.reset();
                }
            })
            .catch(err => {
                console.log('AJAX request Error:', err); // 调试信息
                const { message, response } = err;
                console.log('AJAX request Error1:', err); // 调试信息
                console.log('AJAX request Error2:', message); // 调试信息
                console.log('AJAX request Error3:', response); // 调试信息
                if (response && props.invalidStatuses.includes(response.status)) {
                    verrors.record(response.data.data);
                    emit("invalid", verrors, formData);
                    loading.value = false;
                } else {
                    if (!props.skipErrorHandling || (Array.isArray(props.skipErrorHandling) && !props.skipErrorHandling.includes(response.status))) {
                        error.value = message || "Unknown Error Occurred";
                        errorDetail.value = err.detail;
                    }
                }
                emit(`error-${response.status}`, err, formData);
                emit("failed", err, formData);
                emit("post-submit", response, formData);
                loading.value = false;
            });
        };

        const triggerSubmit = (e) => {
            let submitButtonElement = e.target.querySelector("[type=submit][name]");
            if (submitButtonElement) {
                submitButtonElement.click();
            } else {
                submitButtonElement = document.createElement("button");
                submitButtonElement.type = "submit";
                e.target.appendChild(submitButtonElement);
                submitButtonElement.click();
                submitButtonElement.remove();
            }
        };

        const handleSuccess = (data) => {
            console.log('Form submission successful:', data);
            // 根据响应数据生成重定向 URL
            if (props.redirectTemplate) {
                const redirectUrl = props.redirectTemplate.replace(':code', data.meta.attendance.code); // 使用正确的字段路径
                console.log('Redirecting to:', redirectUrl);
                window.location.href = redirectUrl;
            }
        };

        onMounted(() => {
            if (props.ajax) {
                document.querySelectorAll("[type=submit]").forEach(button =>
                    button.addEventListener("click", function () {
                        submitButton.value = this;
                    })
                );
            }
        });

        return {
            spoofMethod,
            upperMethod,
            csrfToken,
            handleSubmission,
            clearValidationError,
            dismissError,
            ajaxSubmission,
            triggerSubmit,
            handleSuccess,
            continueLoading,
            error,
            errorDetail,
            verrors,
            loading,
            submitButton,
            clearFormSubmission
        };
    }
};
</script>