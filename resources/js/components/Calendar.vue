<template>
  <div>
      <div class="flex items-center justify-center">
          <div v-if="! noScroll" class="flex-1 flex items-center">
              <button type="button" class="border border-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 p-2 mr-2" tabindex="-1"
                  :class="{'cursor-not-allowed': focusedDate.isSameOrBefore(this.minDate, 'month')}"
                  :disabled="focusedDate.isSameOrBefore(this.minDate, 'month')"
                  @click="prevMonth">
                  <svg class="h-3 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.34 13.88"><defs></defs><title>Icons_Arrow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M19.91,6.51H1.38L7.22.67A.39.39,0,0,0,6.94,0a.38.38,0,0,0-.28.12L.12,6.66a.39.39,0,0,0,0,.55l6.55,6.55a.39.39,0,0,0,.56-.56L1.37,7.37H19.91a.43.43,0,1,0,0-.85Z"></path></g></g></svg>
              </button>
              <button type="button" class="border border-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 p-2" tabindex="-1"
                  :class="{'cursor-not-allowed': focusedDate.isSameOrAfter(this.maxDate)}"
                  :disabled="focusedDate.isSameOrAfter(this.maxDate)"
                  @click="nextMonth">
                  <svg class="h-3 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.34 13.88"><defs></defs><title>Icons_Arrow</title><g id="Layer_2" data-name="Layer 2"><g transform="translate(20, 0) scale(-1, 1) " id="Layer_1-2" data-name="Layer 1"><path d="M19.91,6.51H1.38L7.22.67A.39.39,0,0,0,6.94,0a.38.38,0,0,0-.28.12L.12,6.66a.39.39,0,0,0,0,.55l6.55,6.55a.39.39,0,0,0,.56-.56L1.37,7.37H19.91a.43.43,0,1,0,0-.85Z"></path></g></g></svg>
              </button>
          </div>
          <div style="flex:2" :class="{'text-center': noScroll || navYear}" class="text-lg font-bold" v-text="focusedDate.format('MMMM YYYY')"></div>
          <div v-if="navYear" class="flex-1 flex items-center justify-end">
              <button type="button" class="border border-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 p-2 mr-2" tabindex="-1"
                  :class="{'cursor-not-allowed': focusedDate.isSameOrBefore(this.minDate, 'month')}"
                  :disabled="focusedDate.isSameOrBefore(this.minDate, 'month')"
                  @click="prevYear">
                  <svg class="h-3 w-4 fill-current" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20"><path fill="#000000" d="M14 20c0.128 0 0.256-0.049 0.354-0.146 0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646 8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-9 9c-0.195 0.195-0.195 0.512 0 0.707l9 9c0.098 0.098 0.226 0.146 0.354 0.146z"></path></svg>
              </button>
              <button type="button" class="border border-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 p-2" tabindex="-1"
                  :class="{'cursor-not-allowed': focusedDate.isSameOrAfter(this.maxDate)}"
                  :disabled="focusedDate.isSameOrAfter(this.maxDate)"
                  @click="nextYear">
                  <svg class="h-3 w-4 fill-current" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20"><path fill="#000000" d="M5 20c-0.128 0-0.256-0.049-0.354-0.146-0.195-0.195-0.195-0.512 0-0.707l8.646-8.646-8.646-8.646c-0.195-0.195-0.195-0.512 0-0.707s0.512-0.195 0.707 0l9 9c0.195 0.195 0.195 0.512 0 0.707l-9 9c-0.098 0.098-0.226 0.146-0.354 0.146z"></path></svg>
              </button>
          </div>
      </div>
      <table class="w-full my-6 text-sm" style="table-layout: auto;border-spacing: 0;border-collapse: collapse;">
          <thead >
              <tr>
                  <td class="text-center text-xs p-3 w-1/7 " v-for="weekday in weekdays" :key="weekday">
                      <span v-text="weekday"></span>
                  </td>
              </tr>
          </thead>
          <tbody>
              <tr v-for="(week, index) in displayDaysInWeeks" :key="index">
                  <template v-for="day in week">
                      <td v-if="isOutside(day)" :key="`outside-${day.format('YYYY-MM-DD')}`"></td>
                      <td v-else :key="day.format('YYYY-MM-DD')"
                          class="text-center p-3.5 md:px-4 md:py-3"
                          :class="{
                              'font-semibold': isToday(day),
                              'cursor-not-allowed border opacity-25': ! isSelectable(day),
                              'cursor-pointer border border-gray-300 dark:border-gray-600 hover:bg-primary dark:hover:bg-secondary hover:text-white': isSelectable(day),
                              'bg-primary text-white hover:bg-primary-darker': isHighlighted(day),
                              'bg-grey-30': isSubHighlighted(day),
                          }"
                          @click.stop="isSelectable(day) ? selectDate(day) : undefined"
                          @mouseover="isSelectable(day) ? hoverDate(day) : undefined">
                          <span class="select-none" v-text="day.format('D')"></span>
                      </td>
                  </template>
              </tr>
          </tbody>
      </table>
  </div>
</template>
<script>
import { ref, computed, watch } from 'vue';
import moment from 'moment';
import _ from 'lodash';

export default {
  props: {
    min: {
      default: () => moment().subtract(1, 'years').startOf('year')
    },
    max: {
      default: () => moment().add(1, 'years').endOf('year')
    },
    focusDate: {
      default: () => moment()
    },
    disableDates: {
      type: Array,
      default: () => []
    },
    highlight: {
      default: () => []
    },
    subHighlight: {
      default: () => []
    },
    noScroll: Boolean,
    navYear: Boolean,
  },
  setup(props, { emit }) {
    const today = ref(moment());
    const minDate = ref(moment(props.min));
    const maxDate = ref(moment(props.max));
    const weekdays = ref(moment.weekdaysMin());
    const focusedDate = ref(moment(props.focusDate));

    const setFocusedDate = (date) => {
      if (date.isBefore(minDate.value)) date = moment(minDate.value);
      if (date.isAfter(maxDate.value)) date = moment(maxDate.value);
      focusedDate.value = date;
      emit('scroll', focusedDate.value);
    };

    watch(() => props.focusDate, (newFocusDate, oldFocusDate) => {
      if (!newFocusDate.isSame(oldFocusDate, 'month')) {
        setFocusedDate(newFocusDate);
      }
    });

    const daysInMonth = computed(() => {
      const startDay = moment(focusedDate.value).startOf('month');
      const endDay = moment(focusedDate.value).endOf('month');
      const totalDays = endDay.diff(startDay, 'days') + 1;
      return _.times(totalDays, (day) => moment(startDay).add(day, 'd'))
        .filter(day => isSelectable(day));
    });

    const displayDaysInMonth = computed(() => {
      const startDay = moment(focusedDate.value).startOf('month').startOf('week');
      const endDay = moment(focusedDate.value).endOf('month').endOf('week');
      const totalDays = endDay.diff(startDay, 'days') + 1;
      return _.times(totalDays, (day) => moment(startDay).add(day, 'd'));
    });

    const displayDaysInWeeks = computed(() => {
      return _.chunk(displayDaysInMonth.value, 7);
    });

    const disableDatesWithin = computed(() => {
      return props.disableDates.filter(date => {
        return moment(date).isSame(focusedDate.value, 'month');
      });
    });

    const findDateArray = (needle, haystack) => {
      if (!(haystack instanceof Array)) haystack = [haystack];
      return haystack.map(date => moment(date)).findIndex(date => date.isSame(needle, 'day'));
    };

    const isWeekend = (day) => {
      return day.format('dd') === 'Su' || day.format('dd') === 'Sa';
    };

    const isOutside = (day) => {
      return day < moment(focusedDate.value).startOf('month') ||
        day > moment(focusedDate.value).endOf('month');
    };

    const isPast = (day) => {
      return day.startOf('day').isBefore(moment(today.value).startOf('day'));
    };

    const isToday = (day) => {
      return day.isSame(today.value, 'day');
    };

    const isWithinMinMax = (day) => {
      return !(day.isBefore(minDate.value) || day.isAfter(maxDate.value));
    };

    const isDisabled = (day) => {
      return findDateArray(day, disableDatesWithin.value) >= 0;
    };

    const isSelectable = (day) => {
      return isWithinMinMax(day) && !isDisabled(day);
    };

    const isHighlighted = (day) => {
      return findDateArray(day, props.highlight) >= 0;
    };

    const isSubHighlighted = (day) => {
      return findDateArray(day, props.subHighlight) >= 0;
    };

    const prevMonth = () => {
      setFocusedDate(moment(focusedDate.value).subtract(1, 'month'));
    };

    const nextMonth = () => {
      setFocusedDate(moment(focusedDate.value).add(1, 'month'));
    };

    const prevYear = () => {
      setFocusedDate(moment(focusedDate.value).subtract(1, 'year'));
    };

    const nextYear = () => {
      setFocusedDate(moment(focusedDate.value).add(1, 'year'));
    };

    const selectDate = (date) => {
      emit('select', date);
    };

    const hoverDate = (date) => {
      emit('hover', date);
    };

    return {
      today,
      minDate,
      maxDate,
      weekdays,
      focusedDate,
      daysInMonth,
      displayDaysInMonth,
      displayDaysInWeeks,
      disableDatesWithin,
      findDateArray,
      isWeekend,
      isOutside,
      isPast,
      isToday,
      isWithinMinMax,
      isDisabled,
      isSelectable,
      isHighlighted,
      isSubHighlighted,
      setFocusedDate,
      prevMonth,
      nextMonth,
      prevYear,
      nextYear,
      selectDate,
      hoverDate
    };
  }
};
</script>
