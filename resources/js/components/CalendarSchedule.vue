<template>
  <div class="rounded-lg h-[88vh] md:h-[92vh] w-full justify-center items-center relative flex flex-col bg-bg py-2 md:py-4">
    <!-- Loading Indicator -->
    <div v-if="loading" class="absolute inset-0 flex flex-col justify-center items-center bg-white bg-opacity-90 dark:bg-gray-900 dark:bg-opacity-90 z-10">
      <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 rounded-lg shadow-lg px-6 py-4">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ loadingMessage }}</div>
      </div>
    </div>
    <!-- Calendar Header -->
    <div class="flex justify-between items-center mb-4 w-full text-xs md:text-base px-2 md:px-4">
      <div class="flex md:gap-2">
        <button @click="previousMonth" class="btn !px-2" :disabled="isMonthLocked" :class="{ 'opacity-50 cursor-not-allowed': isMonthLocked }">
          <i class="fas fa-angle-left"></i>
        </button>
        <!-- Today Button -->
        <button @click="goToToday" class="btn !px-2" :disabled="isMonthLocked" :class="{ 'opacity-50 cursor-not-allowed': isMonthLocked }">
          Today
        </button>
        <button @click="nextMonth" class="btn !px-2" :disabled="isMonthLocked" :class="{ 'opacity-50 cursor-not-allowed': isMonthLocked }">
          <i class="fas fa-angle-right"></i>
        </button>
      </div>
      <h2 class="text-base lg:text-lg font-semibold">
        {{ currentMonthName }} {{ currentYear }}
      </h2>
      <a :href="`/${$root.app_domain}/lessons`" class="btn !px-2">
        All Classes
      </a>
    </div>
    <!-- Calendar Table -->
    <div class="flex-grow">
      <table class="table-fixed w-full h-full">
        <thead>
          <tr>
            <th v-for="day in isMobileScreen ? daysOfWeekMobile : daysOfWeek" :key="day"
              class="border border-gray-300 dark:border-gray-500 py-1 font-bold text-xs text-center">
              {{ day }}
            </th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="week in calendar" :key="week[0].date">
            <td v-for="day in week" :key="day.date" 
              class="border border-gray-300 dark:border-gray-500 text-gray-400 aspect-square relative bg-slate-100 dark:bg-[#1c203a]"
              :class="{ 
                '!bg-bg !text-black dark:!text-white': day.isCurrentMonth,
                'drop-zone-active': isDragOver && dragOverDate === day.formattedDate && day.isCurrentMonth
              }" 
              @mouseenter="day.isCurrentMonth && isAddAllowed(day) && showAddButton(day)" 
              @mouseleave="hideAddButton()"
              @dragover.prevent="handleDragOver(day)"
              @dragleave="handleDragLeave()"
              @drop="handleDrop(day, $event)">
              <!-- Main Date Display -->
              <div class="flex flex-col items-center justify-start h-full">
                <span class="text-xs md:text-sm font-semibold flex items-center justify-center" :class="{
                  'today-circle': isToday(day.date),
                }">
                  {{ day.date.getDate() }}
                </span>

                <!-- Add Button (shown on hover) -->
                <div v-if="allowAddSchedule && hoveredDate === day.formattedDate && day.isCurrentMonth && isAddAllowed(day)" 
                  class="absolute right-2 bottom-2 z-10">
                  <button @click.prevent="$emit('add-schedule', day.formattedDate)" 
                    class="bg-blue-500 hover:bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                    <i class="fas fa-plus text-xs"></i>
                  </button>
                </div>

                <!-- Conditionally Rendered Content -->
                <a href="#" @click.prevent="
                  $root.isDrawerOpen = true;
                $root.selectedDate = day.formattedDate;
                $root.dateSchedule = calendarData[day.formattedDate];
                " v-if="flattenedClasses(day.formattedDate).length > 0">
                  <div v-if="!isMobileScreen"
                    class="text-[11px] absolute top-5 inset-x-2 whitespace-nowrap space-y-1">
                    <!-- Display the first three class details -->
                    <div v-for="(classItem, index) in flattenedClasses(day.formattedDate).slice(0, 3)" :key="index"
                      class="flex items-center class-item rounded-md w-full overflow-hidden"
                      :draggable="rescheduleUrl !== null"
                      @dragstart="handleDragStart(classItem, day.formattedDate, $event)"
                      @dragend="handleDragEnd()"
                      :class="{ 'cursor-move': rescheduleUrl !== null, 'dragging': isDragging && draggedItem?.id === classItem.id }">
                      <div class="flex-1 text-xs px-1.5 py-0.5 truncate flex items-center bg-gray-100/90 dark:bg-gray-700/90 text-black dark:text-white border border-gray-200 dark:border-gray-600">
                        <span class="w-2.5 h-2.5 rounded-full mr-1.5 flex-shrink-0" :class="getDotClass(classItem.status)"></span>
                        <span class="truncate">{{ classItem.subject_name }}</span>
                        <span class="text-[9px] ml-auto whitespace-nowrap">{{ classItem.start_time || "7 AM" }}</span>
                      </div>
                    </div>
                    <!-- Show Remaining Class Number -->
                    <div v-if="flattenedClasses(day.formattedDate).length > 3"
                      class="text-center text-[10px] text-gray-600 dark:text-gray-300 mt-1">
                      +{{ flattenedClasses(day.formattedDate).length - 3 }} more
                    </div>
                  </div>
                  <!-- For Mobile Screens -->
                  <button v-else :class="{
                    'absolute top-7 left-1/2 -translate-x-1/2 bg-warning-400/70 text-black rounded-full text-[11px] px-1.5 py-0.5':
                      isMobileScreen,
                  }">
                    {{ flattenedClasses(day.formattedDate).length }}
                  </button>
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Drag Preview -->
    <div v-if="isDragging" 
      class="fixed pointer-events-none z-50 bg-blue-100 dark:bg-blue-900 border-2 border-blue-500 rounded-md px-2 py-1 text-xs shadow-lg drag-preview"
      :style="{ left: dragPreviewPosition.x + 'px', top: dragPreviewPosition.y + 'px' }">
      <span class="w-2 h-2 rounded-full mr-1 inline-block" :class="getDotClass(draggedItem?.status)"></span>
      {{ draggedItem?.subject_name }}
      <div class="text-[10px] text-primary-600 dark:text-blue-300 mt-1">
        Drag to reschedule
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    role: {
      type: String,
      required: true,
    },
    url: {
      type: String,
      required: false,
      default: null,
    },
    allowAddSchedule: {
      type: Boolean,
      default: false
    },
    lockMonth: {
      type: String,
      default: null,
      // Format: 'YYYY-MM' (e.g. '2024-06')
    },
    rescheduleUrl: {
      type: String,
      required: false,
      default: null,
    }
  },
  components: {
  },
  data() {
    return {
      currentYear: new Date().getFullYear(),
      currentMonth: new Date().getMonth(),
      daysOfWeek: ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"],
      daysOfWeekMobile: ["S", "M", "T", "W", "T", "F", "S"],
      calendarData: {},
      isDrawerOpen: false,
      loading: false,
      loadingMessage: 'Loading...',
      selectedDate: null, // or some default value if needed
      hoveredDate: null, // 用于跟踪当前悬停的日期
      draggedItem: null,
      draggedFromDate: null,
      isDragOver: false,
      dragOverDate: null,
      dragPreviewPosition: { x: 0, y: 0 },
      isDragging: false,
      // 圆点颜色映射 - 使用App/Enums/Status.php中的颜色定义
      dotClasses: {
        'ACTIVE': 'bg-green-500',
        'LIVE': 'bg-green-600',
        'DONE': 'bg-gray-600',
        'DROP': 'bg-gray-400',
        'REF': 'bg-indigo-500',
        'DELETED': 'bg-red-700',
        'COMPLETED': 'bg-gray-700',
        'REDRAW': 'bg-blue-400',
        'INPROGRESS': 'bg-amber-500',
        'REJECTED': 'bg-danger-600',
        'CONFIRM': 'bg-blue-600',
        'WAITING': 'bg-yellow-500',
        'PENDING': 'bg-amber-500',
        'UNVERIFIED': 'bg-danger-500',
        'EXPIRED': 'bg-gray-500',
        'RESCHEDULING': 'bg-amber-600',
        'APPROVED': 'bg-green-600',
        'READY_GENERATING': 'bg-blue-400',
        'GENERATING': 'bg-blue-600',
        'ERROR': 'bg-red-800',
        'REJECTED_BY_TUTOR': 'bg-rose-600',
        'PENDING_REPORT': 'bg-yellow-500',
        'PENDING_APPROVED': 'bg-amber-600',
        'PENDING_PAYMENT': 'bg-amber-700',
        'PAID': 'bg-teal-600',
      }
    };
  },
  computed: {
    currentMonthName() {
      return new Date(this.currentYear, this.currentMonth).toLocaleString("default", { month: "long" });
    },
    calendar() {
      const startOfMonth = new Date(this.currentYear, this.currentMonth, 1);
      const endOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0);

      const startDay = startOfMonth.getDay();
      const totalDays = endOfMonth.getDate();

      const calendar = [];
      let week = [];

      // Fill in previous month's dates
      for (let i = 0; i < startDay; i++) {
        week.push({ date: new Date(this.currentYear, this.currentMonth, -startDay + i + 1), isCurrentMonth: false });
      }

      // Fill in current month's dates
      for (let day = 1; day <= totalDays; day++) {
        if (week.length === 7) {
          calendar.push(week);
          week = [];
        }
        week.push({
          date: new Date(this.currentYear, this.currentMonth, day),
          formattedDate: this.formatDate(new Date(this.currentYear, this.currentMonth, day)),
          isCurrentMonth: true,
        });
      }

      // Fill in next month's dates
      let nextMonthDay = 1;
      while (week.length < 7) {
        week.push({ date: new Date(this.currentYear, this.currentMonth + 1, nextMonthDay++), isCurrentMonth: false });
      }
      calendar.push(week);
      return calendar;
    },
    isMobileScreen() {
      return window.innerWidth < 768;
    },
    isMonthLocked() {
      return this.lockMonth !== null;
    },
    lockedMonthValue() {
      if (!this.lockMonth) return null;
      
      const parts = this.lockMonth.split('-');
      if (parts.length !== 2) return null;
      
      return {
        year: parseInt(parts[0]),
        month: parseInt(parts[1]) - 1 // JavaScript月份从0开始
      };
    }
  },
  methods: {
    formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    isToday(date) {
      const today = new Date();
      return (
        date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear()
      );
    },
    isAddAllowed(day) {
      if (!this.lockMonth) return true;
      
      if (!this.lockedMonthValue) return true;
      
      return (
        day.date.getFullYear() === this.lockedMonthValue.year &&
        day.date.getMonth() === this.lockedMonthValue.month
      );
    },
    getDotClass(status) {
      return this.dotClasses[status] || 'bg-blue-500';
    },
    async fetchCalendarData() {
      const month = String(this.currentMonth + 1).padStart(2, "0");
      this.loadingMessage = 'Loading calendar data...';
      this.loading = true;
      try {
        const apiUrl = this.url 
          ? `${this.url}/${month}` 
          : passport_url(`/${this.role}/private/schedules/${month}`);
        const response = await axios.get(apiUrl);
        console.log("response: ", response);
        this.calendarData = response.data?.data || response.data || {};
      } catch (error) {
        console.error("Error fetching calendar data:", error);
      } finally {
        this.loading = false;
      }
    },
    previousMonth() {
      if (this.isMonthLocked) return;
      
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
      this.fetchCalendarData();
    },
    nextMonth() {
      if (this.isMonthLocked) return;
      
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
      this.fetchCalendarData();
    },
    flattenedClasses(date) {
      const dayData = this.calendarData[date] || {};
      return Object.values(dayData)
        .flatMap(timeSlots =>
          Object.values(timeSlots).flat()
        );
    },
    goToToday() {
      if (this.isMonthLocked) return;
      
      const today = new Date();
      this.currentYear = today.getFullYear();
      this.currentMonth = today.getMonth();
      const formattedDate = this.formatDate(today);
      this.$root.isDrawerOpen = true;
      this.$root.selectedDate = formattedDate;
      this.$root.dateSchedule = this.calendarData[formattedDate] || {};
    },
    showAddButton(day) {
      this.hoveredDate = day.formattedDate;
    },
    hideAddButton() {
      this.hoveredDate = null;
    },
    handleDragStart(item, date, event) {
      // 检查是否允许重新安排
      if (!this.rescheduleUrl) {
        event.preventDefault();
        console.log('重新安排功能未启用：未提供rescheduleUrl');
        return;
      }
      
      this.draggedItem = item;
      this.draggedFromDate = date;
      this.isDragging = true;
      
      // 设置拖拽数据
      event.dataTransfer.setData('text/plain', JSON.stringify({
        item: item,
        fromDate: date
      }));
      
      // 隐藏默认拖拽图像
      const dragImage = new Image();
      dragImage.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs=';
      event.dataTransfer.setDragImage(dragImage, 0, 0);
      
      // 监听鼠标移动以更新预览位置
      document.addEventListener('dragover', this.updateDragPreview);
    },
    handleDragEnd() {
      this.draggedItem = null;
      this.draggedFromDate = null;
      this.isDragging = false;
      this.isDragOver = false;
      this.dragOverDate = null;
      document.removeEventListener('dragover', this.updateDragPreview);
    },
    handleDragOver(day) {
      if (day.isCurrentMonth && this.draggedItem) {
        this.dragOverDate = day.formattedDate;
        this.isDragOver = true;
      }
    },
    handleDragLeave() {
      this.isDragOver = false;
      this.dragOverDate = null;
    },
    async handleDrop(day, event) {
      event.preventDefault();
      this.isDragOver = false;
      
      // 检查是否允许重新安排
      if (!this.rescheduleUrl) {
        console.log('重新安排功能未启用：未提供rescheduleUrl');
        this.handleDragEnd();
        return;
      }
      
      if (!day.isCurrentMonth || !this.draggedItem) {
        this.handleDragEnd();
        return;
      }
      
      const fromDate = this.draggedFromDate;
      const toDate = day.formattedDate;
      
      // 如果拖拽到同一天，不执行任何操作
      if (fromDate === toDate) {
        this.handleDragEnd();
        return;
      }
      
      try {
        // 发送重新安排API请求
        await this.rescheduleLesson(this.draggedItem, fromDate, toDate);
        
        // 重新获取日历数据
        await this.fetchCalendarData();
        
        // 显示成功消息
        this.$emit('lesson-rescheduled', {
          lesson: this.draggedItem,
          fromDate: fromDate,
          toDate: toDate
        });
        
      } catch (error) {
        console.error('Failed to reschedule lesson:', error);
        this.$emit('reschedule-error', error);
      } finally {
        this.handleDragEnd();
      }
    },
    updateDragPreview(event) {
      if (this.isDragging) {
        this.dragPreviewPosition = {
          x: event.clientX + 10,
          y: event.clientY - 10
        };
      }
    },
    async rescheduleLesson(lesson, fromDate, toDate) {
      // 检查是否提供了重新安排URL
      if (!this.rescheduleUrl) {
        console.error('重新安排功能未启用：未提供rescheduleUrl');
        throw new Error('重新安排功能未启用');
      }
      
      // 控制台输出课程信息
      console.log('=== 重新安排课程 ===');
      console.log('课程信息:', lesson);
      console.log('从日期:', fromDate);
      console.log('到日期:', toDate);
      console.log('课程ID:', lesson.id);
      console.log('课程代码:', lesson.code || lesson.lesson_code);
      console.log('科目名称:', lesson.subject_name);
      console.log('开始时间:', lesson.start_time);
      
      // 双重确认对话框
      const confirmResult = await this.$alert.confirm({
        title: `Confirm reschedule lesson ${lesson.id}?`,
        text: `This action will reschedule the lesson from ${fromDate} to ${toDate}. Time: ${lesson.start_time || 'TBD'}`,
      });
      
      // 检查用户是否确认
      if (!confirmResult) {
        console.log('User cancelled the reschedule operation');
        throw new Error('用户取消操作');
      }
      
      console.log('用户确认重新安排，准备发送API请求...');
      
      // 开始加载状态
      this.loadingMessage = 'Rescheduling lesson...';
      this.loading = true;
      
      try {
        const rescheduleData = {
          lesson_id: lesson.id,
          from_date: fromDate,
          to_date: toDate,
          lesson_code: lesson.code || lesson.lesson_code
        };
        
        console.log('发送的数据:', rescheduleData);
        console.log('使用的重新安排URL:', this.rescheduleUrl);
        
        const response = await axios.post(this.rescheduleUrl + '/' + lesson.id + '/reschedule', rescheduleData);

        // 处理响应
        if (response?.data) {
          console.log('response.data: ', response.data);
          this.$alert.success(response.data.message, null, false);
        } else {
            throw new Error('Unexpected response structure');
        }
        
        return response.data;
      } catch (error) {
        console.error('重新安排API调用失败:', error);
        throw error;
      } finally {
        // 结束加载状态
        this.loading = false;
      }
    },
  },
  mounted() {
    // 如果设置了lockMonth，则初始化日历到指定月份
    if (this.lockMonth && this.lockedMonthValue) {
      this.currentYear = this.lockedMonthValue.year;
      this.currentMonth = this.lockedMonthValue.month;
    }
    
    this.fetchCalendarData();
  },
};
</script>

<style>
.class-item {
  margin-bottom: 1px;
  transition: all 0.2s;
}
.class-item:hover {
  transform: translateX(1px);
}

/* 拖拽相关样式 */
.class-item.dragging {
  opacity: 0.5;
  transform: scale(0.95);
}

.drop-zone-active {
  background-color: rgba(59, 130, 246, 0.1) !important;
  border: 2px dashed #3b82f6 !important;
}

.cursor-move {
  cursor: move;
}

.cursor-move:hover {
  background-color: rgba(59, 130, 246, 0.05);
}

/* 今天日期的圆圈样式 */
.today-circle {
  background-color: #FCD34D;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 3px;
  color: #000;
  font-weight: bold;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* 拖拽预览样式 */
.drag-preview {
  pointer-events: none;
  z-index: 1000;
  transform: rotate(5deg);
  animation: dragFloat 0.3s ease-in-out infinite alternate;
}

@keyframes dragFloat {
  0% { transform: rotate(5deg) translateY(0px); }
  100% { transform: rotate(5deg) translateY(-2px); }
}
</style>
