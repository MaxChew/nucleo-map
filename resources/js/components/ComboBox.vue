<template>
  <div ref="comboBox">
    <input type="hidden" :name="name" :value="state.inputValue">
    <input v-if="labelName" type="hidden" :name="labelName" :value="inputLabel">
    <div class="input-addon input-addon-lg w-full mt-2">
      <input type="text"
        class="form-control !mt-0"
        v-bind="$attrs"
        v-model="state.inputLabel"
        @keydown.enter.prevent="dropDownOrSelectFocusingOption"
        @keydown.up.prevent="focusPreviousOption"
        @keydown.down.prevent="focusNextOption"
        @blur="closeDropdownIfFocusOutside"
        @keydown.esc="toggleDropdown(false)"
        @keyup="handleInputKeyup"
        @click="dropDownAndAutoFocusOption"
        @change="autoSelectOption"
        ref="inputBox"
      >
      <div class="addon" v-if="state.loading">
        <span class="loader w-6 h-6"></span>
      </div>
    </div>
    <ul v-show="state.dropdown" v-if="matchingNormalizedOptions.length"
      class="list-reset z-10 absolute left-0 top-full shadow-sm border border-gray-300 dark:border-gray-600 border-focus text-dark -mt-px bg-bg max-h-80 overflow-auto rounded-md custom-scrollbar"
      :class="{
        'w-full pin-r pin-l': position === 'fit',
        'pin-l min-w-full whitespace-no-wrap': position === 'left',
        'pin-r min-w-full whitespace-no-wrap': position === 'right',
      }"
      @mouseout="state.focusingIndex = null"
      @scroll="checkLoadNextPage"
      ref="dropdownMenu"
    >
      <li v-if="state.loading" class="px-3 py-2 truncate">
        <span class="italic">Searching <strong v-text="state.inputLabel"></strong>...</span>
      </li>
      <template v-else-if="groupBy" v-for="(options, groupLabel) in matchingNormalizedGroups" :key="groupLabel">
        <li v-if="groupLabel">
          <div class="px-3 py-2 font-semibold" v-text="groupLabel"></div>
          <ul class="list-reset">
            <li v-for="(option, optionIndex) in options"
              class="pl-8 pr-3 py-2"
              :key="option.value"
              @click="selectOptionAndFocus(option, $event)"
              @mousedown="handleStopPropagation"
              @mouseover="state.focusingIndex = parseInt(optionIndex)"
              :class="{'bg-gray-300': state.focusingIndex === parseInt(optionIndex)}"
              :ref="el => optionRefs[optionIndex] = el"
            >
              <slot v-if="option && option.value" :option="option" :input="state.inputLabel" :highlight="highlight">
                <span v-html="highlight(option.label)"></span>
              </slot>
              <span v-else class="italic">Add <strong v-text="option.label"></strong>...</span>
            </li>
          </ul>
        </li>
        <li v-else
          v-for="(option, optionIndex) in options"
          class="px-3 py-2"
          :key="option.value"
          @click="selectOptionAndFocus(option, $event)"
          @mousedown="handleStopPropagation"
          @mouseover="state.focusingIndex = parseInt(optionIndex)"
          :class="{'bg-gray-300': state.focusingIndex === parseInt(optionIndex)}"
          :ref="el => optionRefs[optionIndex] = el"
        >
          <slot v-if="option && option.value" :option="option" :input="state.inputLabel" :highlight="highlight">
            <span v-html="highlight(option.label)"></span>
          </slot>
          <span v-else class="italic">Add <strong v-text="option.label"></strong>...</span>
        </li>
      </template>
      <li v-else
        v-for="(option, optionIndex) in matchingNormalizedOptions"
        class="px-3 py-2"
        :key="option.value"
        @click="selectOptionAndFocus(option, $event)"
        @mousedown="handleStopPropagation"
        @mouseover="state.focusingIndex = parseInt(optionIndex)"
        :class="{'bg-gray-300': state.focusingIndex === parseInt(optionIndex)}"
        :ref="el => optionRefs[optionIndex] = el"
      >
        <slot v-if="option && option.value" :option="option" :input="state.inputLabel" :highlight="highlight">
          <span v-html="highlight(option.label)"></span>
        </slot>
        <span v-else class="italic">Add <strong v-text="option.label"></strong>...</span>
      </li>
      <li v-if="url && state.remoteNextPage">
        <button type="button" :disabled="state.loadingMore"
          class="btn btn-link btn-block"
          ref="loadNextPageButton"
          v-text="state.loadingMore ? 'Loading...' : 'Load More'"
          @click="loadNextPage"
        ></button>
      </li>
    </ul>
  </div>
</template>

<script>
import { ref, watch, onMounted, nextTick, reactive, computed } from 'vue';
import axios from 'axios';
import _ from 'lodash';
import { onClickOutside } from '@vueuse/core';
import HasOptions from './mixins/HasOptions';

export default {
  inheritAttrs: false,
  mixins: [HasOptions],
  props: {
    name: String,
    labelName: String,
    modelValue: {}, // Vue 3 推荐的 v-model 绑定属性名
    value: {}, // 向后兼容
    valueLabel: String,
    groupBy: String,
    url: String,
    hideAddOption: Boolean,
    disableAdd: Boolean,
    filterSuggestions: Function,
    position: {
      type: String,
      default: 'fit',
    }
  },
  setup(props, { emit, attrs }) {
    
    const state = reactive({
      options: [],
      inputValue: (props.modelValue !== undefined) ? props.modelValue : props.value,
      inputLabel: props.valueLabel,
      lastLabel: props.valueLabel,
      dropdown: false,
      loading: false,
      focusingIndex: null,
      internalChange: false,
      lastKeyword: null,
      remoteNextPage: null,
    });

    const optionRefs = ref([]);
    const comboBox = ref(null);
    const inputBox = ref(null);
    const dropdownMenu = ref(null);
    const loadNextPageButton = ref(null);
    
    const url = computed(() => {
      console.log('url computed property accessed:', props.url);
      return props.url;
    });

    const structureValues = (newValue) => {
      let label = null, value = null;
      if (newValue instanceof Object) {
        value = props.valueKey && newValue[props.valueKey] !== undefined ? newValue[props.valueKey] : null;
        label = props.labelKey && newValue[props.labelKey] !== undefined ? newValue[props.labelKey] : null;
      } else {
        value = newValue;
      }
      
      if (value && !label) {
        const normalizedOptions = props.url ? normalizedRemoteOptions.value : state.options;
        const matched = normalizedOptions.find(option => option.value == value);
        
        if (matched) {
          label = matched.label;
        } else {
          label = value;
        }
      }
      
      return { value, label };
    };

    const normalizedRemoteOptions = computed(() => {
      return _.map(state.options, option => {
        const values = _.values(option);
        return {
          value: option[props.valueKey] || values[0],
          label: option[props.labelKey] || values[1] || option[props.valueKey] || values[0],
          data: option
        };
      });
    });

    const matchingNormalizedOptions = computed(() => {
      let normalizedOptions = props.url ? normalizedRemoteOptions.value : state.options;
      if (props.filterSuggestions) {
        normalizedOptions = props.filterSuggestions(normalizedOptions, state.inputLabel);
      }
      if (!state.inputLabel) {
        return normalizedOptions;
      }
      let matchingOption = normalizedOptions;
      if (!props.url) {
        matchingOption = matchingOption.filter(({ label }) => {
          return (label + '').toLowerCase().includes((state.inputLabel + '').toLowerCase());
        });
      }
      if (!props.disableAdd && !props.hideAddOption && !fullMatchedOption.value) {
        matchingOption.unshift({ value: null, label: state.inputLabel });
      }
      return matchingOption;
    });

    const fullMatchedOption = computed(() => {
      if (state.inputLabel) {
        if (props.url) {
          return normalizedRemoteOptions.value.find(({ label }) =>
            (label + '').toLowerCase() === (state.inputLabel + '').trim().toLowerCase()
          );
        } else {
          return state.options.find(({ label }) =>
            (label + '').toLowerCase() === (state.inputLabel + '').trim().toLowerCase()
          );
        }
      }
    });

    const matchingNormalizedGroups = computed(() => {
      return _(matchingNormalizedOptions.value)
        .map((option, index) => _.assign(option, { index }))
        .groupBy(option => _.get(option.data, props.groupBy, '') || '')
        .mapValues(options => _.keyBy(options, 'index'))
        .value();
    });

    watch(() => props.valueLabel, (newLabel) => {
      if (state.internalChange) return;
      if (newLabel !== state.inputLabel) {
        state.lastLabel = state.inputLabel = newLabel || null;
        if (inputBox.value) {
          inputBox.value.value = state.inputLabel;
        }
      }
    }, { immediate: true });
    
    watch(() => props.options, (newOptions) => {
      if (!state.inputLabel && !state.inputValue) return;
      if (state.internalChange) return;
      if (_.isEqual(newOptions, state.options)) return;
      state.options = _.cloneDeep(newOptions || []);
      state.lastLabel = state.inputLabel = null;
      state.inputValue = null;
      state.internalChange = true;
      if (inputBox.value) {
        inputBox.value.value = state.inputLabel;
      }
      emit('input', null, null, null);
      emit('update:valueLabel', null, null, null);
      emit('select', null);
      nextTick(() => state.internalChange = false);
    });

    watch(() => props.value, (newValue) => {
      if (state.internalChange) return;
      if (!_.isEqual(newValue, state.inputValue)) {
        let { value, label } = structureValues(newValue);
        state.internalChange = true;
        state.inputValue = value;
        if (props.valueLabel === undefined) {
          state.lastLabel = state.inputLabel = label;
        }
        if (inputBox.value) {
          inputBox.value.value = state.inputLabel;
        }
        nextTick(() => state.internalChange = false);
      }
    }, { immediate: true });

    watch(() => props.url, (newUrl) => {
      if (newUrl) {
        fetchOptions(state.inputLabel);
      }
    });

    watch(() => props.modelValue, (newValue) => {
      if (state.internalChange) return;
      if (!_.isEqual(newValue, state.inputValue)) {
        let { value, label } = structureValues(newValue);
        state.internalChange = true;
        state.inputValue = value;
        if (props.valueLabel === undefined) {
          state.lastLabel = state.inputLabel = label;
        }
        if (inputBox.value) {
          inputBox.value.value = state.inputLabel;
        }
        nextTick(() => state.internalChange = false);
      }
    }, { immediate: true });

    const toggleDropdown = (stateValue) => {
      state.dropdown = stateValue === undefined ? !state.dropdown : stateValue;
    };

    const handleStopPropagation = (e) => {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
    };

    const handleInputKeyup = (e) => {
      if (e.code === 'Tab' || e.code === 'Enter' || e.code === 'ArrowUp' || 
          e.code === 'ArrowDown' || e.code === 'Escape') {
        return;
      }
      
      const inputValue = e.target.value;
      state.inputLabel = inputValue;
      
      handleInputLabel(inputValue);
    };

    const handleInputLabel = (keyword) => {
      if (typeof keyword !== 'string') {
        if (keyword && keyword.target && typeof keyword.target.value === 'string') {
          keyword = keyword.target.value;
        } else {
          console.warn('Input value type error, expected string, received:', typeof keyword, keyword);
          return;
        }
      }
      
      
      keyword = keyword || '';
      
      if (state.lastKeyword === keyword) {
        return;
      }
      
      state.lastKeyword = keyword;
      
      if (props.url) {
        state.loading = true;
        state.remoteNextPage = null;
        debounceFetchOptions(keyword);
      } else {
        console.warn('URL not configured, unable to perform API search');
      }
      
      dropDownAndAutoFocusOption();
    };

    const dropDownAndAutoFocusOption = () => {
      if (!state.dropdown) {
        toggleDropdown(true);
      }
      if (state.focusingIndex === null || state.focusingIndex >= matchingNormalizedOptions.value.length) {
        state.focusingIndex = 0;
      }
    };

    const selectOption = (option) => {
      
      if (option instanceof Event) {
        return;
      }
      
      const oldValue = state.inputValue,
        oldLabel = state.lastLabel;
      if (props.disableAdd && (!option || !option.value)) {
        state.inputLabel = null;
        state.inputValue = null;
      } else {
        state.inputLabel = option ? option.label : state.inputLabel;
        state.inputValue = option ? option.value : null;
      }
      
      if (inputBox.value) {
        inputBox.value.value = state.inputLabel;
      }
      
      if (oldValue === state.inputValue && oldLabel === state.inputLabel) return;
      state.internalChange = true;
      
      emit('input', (state.inputValue || state.inputLabel), state.inputLabel, option);
      emit('update:modelValue', state.inputValue);
      emit('update:value', state.inputValue);
      emit('update:valueLabel', state.inputLabel, state.inputValue, option);
      emit('select', option);
      
      nextTick(() => state.internalChange = false);
    };

    const selectOptionAndFocus = (option, event) => {
      if (event) {
        event.stopPropagation();
        event.stopImmediatePropagation();
      }
      selectOption(option);
      state.lastKeyword = inputBox.value.value;
      inputBox.value.focus();
      toggleDropdown(false);
    };

    const closeDropdownIfClickOutside = (event) => {
      toggleDropdown(false);
    };

    const closeDropdownIfFocusOutside = () => {
      if (state.dropdown) {
        nextTick(() => {
          if (document.activeElement === document.body || comboBox.value.contains(document.activeElement)) {
          } else {
            toggleDropdown(false);
          }
        });
      }
    };

    const focusPreviousOption = () => {
      let prevIndex = state.focusingIndex === null ? 0 : parseInt(state.focusingIndex) - 1;
      state.focusingIndex = (prevIndex < 0) ? 0 : prevIndex;
      maybeAdjustScroll();
    };

    const focusNextOption = () => {
      let nextIndex = state.focusingIndex === null ? 0 : parseInt(state.focusingIndex) + 1,
        lastIndex = matchingNormalizedOptions.value.length - 1;
      state.focusingIndex = (nextIndex > lastIndex) ? lastIndex : nextIndex;
      maybeAdjustScroll((nextIndex > lastIndex));
    };

    const maybeAdjustScroll = (toBottom = false) => {
      if (state.focusingIndex === null || !optionRefs.value[state.focusingIndex]) return;
      let option = optionRefs.value[state.focusingIndex],
        optionHeight = option.offsetHeight,
        optionTop = option.offsetTop,
        optionBottom = optionTop + optionHeight,
        viewport = dropdownMenu.value,
        viewportHeight = viewport.offsetHeight,
        viewportTop = viewport.scrollTop,
        viewportBottom = viewportHeight + viewportTop;
      if (toBottom) {
        return scrollTo(viewportBottom);
      }
      if (optionTop <= viewportTop) {
        return scrollTo(optionTop);
      } else if (optionBottom >= viewportBottom) {
        return scrollTo(optionBottom - viewportHeight);
      } else if (state.focusingIndex === 0) {
        return scrollTo(0);
      }
    };

    const scrollTo = (position) => {
      if (dropdownMenu.value) {
        dropdownMenu.value.scrollTop = position;
      }
    };

    const dropDownOrSelectFocusingOption = () => {
      if (!state.dropdown) {
        toggleDropdown(true);
      } else if (state.focusingIndex !== null) {
        selectOptionAndFocus(matchingNormalizedOptions.value[state.focusingIndex]);
      }
    };

    const autoSelectOption = () => {
      if (state.dropdown) {
        toggleDropdown(false);
      }
      if (state.internalChange) {
        return;
      }
      if (fullMatchedOption.value) {
        selectOption(fullMatchedOption.value);
      } else if (!props.disableAdd) {
        selectOption(state.inputLabel ? { value: null, label: state.inputLabel } : null);
      } else {
        state.inputLabel = state.lastLabel;
        if (inputBox.value) {
          inputBox.value.value = state.inputLabel;
        }
      }
    };

    const fetchOptions = (keyword) => {
      if (!props.url) {
        console.error('fetchOptions: URL未配置，无法执行API请求');
        return;
      }
      
      let page = state.remoteNextPage;
      const params = keyword ? { page, filters: { keyword: '~' + keyword } } : { page };
      
      
      axios.get(props.url, { params })
        .then(({ data }) => {
          
          state.remoteNextPage = null;
          if (data.meta && data.meta.last_page) {
            state.remoteNextPage = (data.meta.current_page < data.meta.last_page) ?
              (data.meta.current_page + 1) : null;
          }
          if (page) {
            state.options.push(...data.data);
          } else {
            state.options = data.data;
          }
          
          state.loading = false;
          state.loadingMore = false;
        })
        .catch((error) => {
          console.error('API request failed:', error);
          console.log('Attempting to retry with backup axios instance');
          
          const axiosInstance = axios.create();
          axiosInstance.get(props.url, { params })
            .then(({ data }) => {
              
              state.remoteNextPage = null;
              if (data.meta && data.meta.last_page) {
                state.remoteNextPage = (data.meta.current_page < data.meta.last_page) ?
                  (data.meta.current_page + 1) : null;
              }
              if (page) {
                state.options.push(...data.data);
              } else {
                state.options = data.data;
              }
              
              state.loading = false;
              state.loadingMore = false;
            })
            .catch(err => {
              console.error('Backup axios request also failed:', err);
              state.loading = false;
              state.loadingMore = false;
            });
        });
    };

    const debounceFetchOptions = _.debounce(function (keyword) {
      fetchOptions(keyword);
    }, 300);

    const highlight = (text, cssClass = 'font-bold') => {
      if (state.inputLabel && state.inputLabel.trim()) {
        return text.replace(new RegExp(_.escapeRegExp(state.inputLabel), 'gi'), `<em class="${cssClass}">$&</em>`);
      }
      return text;
    };

    const checkLoadNextPage = () => {
      // 实现滚动加载更多的逻辑
    };

    const loadNextPage = () => {
      state.loadingMore = true;
      fetchOptions(state.inputLabel);
    };

    onMounted(() => {
      
      if (props.modelValue && !state.inputValue) {
        let { value, label } = structureValues(props.modelValue);
        state.inputValue = value;
      }
      
      if (props.url) {
        fetchOptions(state.inputLabel);
      }
      
      if (inputBox.value && state.inputLabel !== inputBox.value.value) {
        inputBox.value.value = state.inputLabel;
      }
      
      onClickOutside(comboBox, closeDropdownIfClickOutside);
    });

    return {
      url,
      state,
      optionRefs,
      comboBox,
      inputBox,
      dropdownMenu,
      loadNextPageButton,
      normalizedRemoteOptions,
      matchingNormalizedOptions,
      fullMatchedOption,
      matchingNormalizedGroups,
      structureValues,
      toggleDropdown,
      handleStopPropagation,
      handleInputKeyup,
      dropDownAndAutoFocusOption,
      selectOption,
      selectOptionAndFocus,
      closeDropdownIfClickOutside,
      closeDropdownIfFocusOutside,
      focusPreviousOption,
      focusNextOption,
      maybeAdjustScroll,
      scrollTo,
      dropDownOrSelectFocusingOption,
      autoSelectOption,
      fetchOptions,
      debounceFetchOptions,
      highlight,
      checkLoadNextPage,
      loadNextPage,
    };
  }
};
</script>