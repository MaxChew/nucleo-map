<template>
  <div class="relative"
       :class="{'bg-bg-dark opacity-50 cursor-not-allowed': disabled}"
       ref="datepicker">
    <input type="hidden" :disabled="disabled"
           :name="name"
           :value="selectedDate ? selectedDate.format('YYYY-MM-DD') : null">
    <div class="relative">
      <input type="text" class="form-control pr-12 cursor-pointer"
             autocomplete="off"
             :class="{'border-focus': isOpened}"
             :disabled="disabled"
             @click="toggleCalendar"
             :placeholder="placeholder"
             :value="selectedDate ? selectedDate.format('YYYY-MM-DD') : null"
             @input="parseSelectedDateInput"
             @blur="closePicker"
             v-bind="$attrs"
             @update="$emit('update', $event)"
             ref="input">
      <span class="absolute inset-y-0 right-0 flex items-center pr-4 !cursor-pointer"
            @click="toggleCalendar">
        <i class="fas fa-calendar-alt"></i>
      </span>
    </div>
    <div class="min-w-full md:min-w-fit bg-bg-dark border absolute shadow-lg z-30" v-if="isOpened"
         :class="{
           'pin-r': position === 'right',
           'pin-l': position === 'left',
           'pin-l pin-r': position === 'fit',
           'pin-t-full': !dropUp,
           'pin-b-full': dropUp,
         }">
      <div class="p-2 md:p-4">
        <div v-if="twoMonths" class="relative">
          <button type="button" class="border p-2 absolute pin-l pin-t" tabindex="-1"
                  :class="{'cursor-not-allowed': focusDate.isSameOrBefore(minDate, 'month')}"
                  :disabled="focusDate.isSameOrBefore(minDate, 'month')"
                  @click="prevMonth">
            <svg class="fill-current h-3 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.34 13.88"><defs></defs><title>Icons_Arrow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M19.91,6.51H1.38L7.22.67A.39.39,0,0,0,6.94,0a.38.38,0,0,0-.28.12L.12,6.66a.39.39,0,0,0,0,.55l6.55,6.55a.39.39,0,0,0,.56-.56L1.37,7.37H19.91a.43.43,0,1,0,0-.85Z"></path></g></g></svg>
          </button>
          <button type="button" class="border p-2 absolute pin-r pin-t" tabindex="-1"
                  :class="{'cursor-not-allowed': focusDate.isSameOrAfter(maxDate.subtract(1, 'month'))}"
                  :disabled="focusDate.isSameOrAfter(maxDate.subtract(1, 'month'))"
                  @click="nextMonth">
            <svg class="fill-current h-3 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.34 13.88"><defs></defs><title>Icons_Arrow</title><g id="Layer_2" data-name="Layer 2"><g transform="translate(20, 0) scale(-1, 1)" id="Layer_1-2" data-name="Layer 1"><path d="M19.91,6.51H1.38L7.22.67A.39.39,0,0,0,6.94,0a.38.38,0,0,0-.28.12L.12,6.66a.39.39,0,0,0,0,.55l6.55,6.55a.39.39,0,0,0,.56-.56L1.37,7.37H19.91a.43.43,0,1,0,0-.85Z"></path></g></g></svg>
          </button>
          <div class="flex">
            <calendar class="mr-4" :focus-date="focusDate"
                      @scroll="setFocusDate"
                      no-scroll
                      :min="minDate"
                      :max="maxDate.subtract(1, 'months').endOf('month')"
                      :disable-dates="disableDates"
                      @select="selectDate"
                      :highlight="[selectedDate].filter(date => !!date)">
            </calendar>
            <calendar :focus-date="focusDate.add(1, 'months')"
                      no-scroll
                      :min="minDate.add(1, 'months').startOf('month')"
                      :max="maxDate"
                      :disable-dates="disableDates"
                      @select="selectDate"
                      :highlight="[selectedDate].filter(date => !!date)">
            </calendar>
          </div>
        </div>
        <calendar v-else
                  :focus-date="focusDate"
                  @scroll="setFocusDate"
                  :min="minDate"
                  :max="maxDate"
                  :disable-dates="disableDates"
                  @select="selectDate"
                  :highlight="[selectedDate].filter(date => !!date)"
                  :nav-year="navYear">
        </calendar>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import Calendar from './Calendar.vue';
import { onClickOutside } from '@vueuse/core';
import moment from 'moment';

export default {
  inheritAttrs: false,
  components: { Calendar },
  props: {
    modelValue: {},
    name: String,
    position: { type: String, default: 'left' },
    disabled: Boolean,
    twoMonths: Boolean,
    placeholder: String,
    navYear: Boolean,
    min: { default: () => moment().subtract(100, 'years').startOf('year') },
    max: { default: () => moment().add(50, 'years').endOf('year') },
    defaultFocusDate: { default: () => moment() },
    parsableFormats: { type: Array, default: () => ([moment.ISO_8601, 'YYYY-MM-DD', 'DD-MM-YYYY', 'D MMM YYYY']) },
    disableDates: { type: Array, default: () => [] },
    dropUp: Boolean,
  },
  setup(props, { emit, attrs }) {
    const isOpened = ref(false);
    const focusDate = ref(props.defaultFocusDate);
    const selectedDate = ref(null);
    const minDate = ref(moment(props.min));
    const maxDate = ref(moment(props.max));

    const structuredValue = computed(() => selectedDate.value ? selectedDate.value.format('YYYY-MM-DD') : null);

    const toggleCalendar = () => {
      //console.log('toggleCalendar called with state:', state); // Log toggleCalendar state
      isOpened.value = !isOpened.value; 
    };

    const parseValidDate = (input) => {
      let parsedDate = moment(input, props.parsableFormats);
      if (parsedDate.isValid()) {
        if (parsedDate.isBefore(minDate.value)) parsedDate = moment(minDate.value);
        if (parsedDate.isAfter(maxDate.value)) parsedDate = moment(maxDate.value);
        if (props.disableDates.some(date => moment(date).isSame(parsedDate, 'day'))) return null;
        return parsedDate;
      }
      return null;
    };

    const parseSelectedDateInput = (e) => {
      selectedDate.value = parseValidDate(e.target.value);
      if (selectedDate.value) setFocusDate(selectedDate.value);
      emit('update:modelValue', structuredValue.value);
    };

    const selectDate = (date) => {
      selectedDate.value = moment(date);
      emit('update:modelValue', selectedDate.value.format('YYYY-MM-DD'));
      toggleCalendar();
    };

    const setFocusDate = (date) => {
      focusDate.value = moment(date);
    };

    const prevMonth = () => {
      setFocusDate(moment(focusDate.value).subtract(1, 'month'));
    };

    const nextMonth = () => {
      setFocusDate(moment(focusDate.value).add(1, 'month'));
    };

    const closePicker = () => {
      nextTick(() => {
        if (document.activeElement !== document.body && !document.activeElement.closest('.relative')) {
          console.log('closePicker called'); // Log closePicker call
          toggleCalendar;
        }
      });
    };
    const closePickerFromOutside = (e) => {
  console.log('closePickerFromOutside called, event:', e); // Log closePickerFromOutside event
  if (isOpened.value) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    console.log('closePickerFromOutside: closing calendar'); // Log closePickerFromOutside call
    toggleCalendar();
  }
};

const datepickerRef = ref(null);

onMounted(() => {
  nextTick(() => {
    if (datepickerRef.value) {
      onClickOutside(datepickerRef, closePickerFromOutside);
    }
  });
});

watch(() => props.modelValue, (newValue) => {
  if (structuredValue.value === newValue) return;
  selectedDate.value = newValue ? moment(newValue) : null;
  if (selectedDate.value) setFocusDate(selectedDate.value);
}, { deep: true, immediate: true });

return {
  isOpened,
  focusDate,
  selectedDate,
  minDate,
  maxDate,
  structuredValue,
  toggleCalendar,
  parseValidDate,
  parseSelectedDateInput,
  selectDate,
  setFocusDate,
  prevMonth,
  nextMonth,
  closePicker,
  closePickerFromOutside,
  datepickerRef,
  ...attrs
};
},
};
</script>