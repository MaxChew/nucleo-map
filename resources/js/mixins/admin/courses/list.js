import BaseList from '../base-list';
import _ from 'lodash';


export default {
    mixins: [BaseList],
    data() {
        return {
            // 报告特定的数据
            filters: {
                keyword: '',
                status: '',
                students: [],
                tutors: [],
                subjects: [],
                clients: [],
                months: [],
                year: null
            },
            // 映射对象
            students: {}, 
            tutors: {}, 
            subjects: {}, 
            clients: {}, 
            months: {},
            year: {},
            // 当前选择的项目
            currentStudent: [],
            currentTutor: [],
            currentSubject: [],
            currentClient: [],
            currentMonths: [],
            currentYear: null,
            displayYear: new Date().getFullYear(),
            selectedMonths: []
        }
    },
    computed: {
        // 使用动态计算属性获取所有标签
        studentsLabel() {
            return this.getFilterLabel.call(this, 'students');
        },
        tutorsLabel() {
            return this.getFilterLabel.call(this, 'tutors');
        },
        subjectsLabel() {
            return this.getFilterLabel.call(this, 'subjects');
        },
        clientsLabel() {
            return this.getFilterLabel.call(this, 'clients');
        },
        yearMonthLabel() {
            if (this.currentYear && this.currentMonths && this.currentMonths.length) {
                const monthLabels = this.currentMonths
                    .filter(m => m && m.label)
                    .map(m => m.label)
                    .join(', ');
                return `${this.currentYear} - ${monthLabels}`;
            }
            return '';
        },
        
        
        filterParams() {
            // 创建特定于报告的过滤参数
            const filterParams = {};
            
            // 处理关键字搜索
            if (this.filters.keyword) {
                filterParams['filters[keyword]'] = '~' + this.filters.keyword;
            }
            
            // 处理数组类型的筛选器
            ['students', 'tutors', 'subjects', 'clients', 'months'].forEach(filterType => {
                if (this.filters[filterType] && this.filters[filterType].length > 0) {
                    this.filters[filterType].forEach((id, index) => {
                        filterParams[`filters[${filterType}][${index}]`] = id;
                    });
                }
            });
            
            // 处理单值类型的年份筛选器
            if (this.filters.year) {
                filterParams['filters[year]'] = this.filters.year;
            }
            
            filterParams.status = this.current_status;
            
            return filterParams;
        }
    },
    methods: {
        async duplicateDraftLesson(object, row, refreshPage = true) {

            try {

                 // 获取显示名称
                let displayId = 'Unknown';
                if (row) {
                    if (row.code) {
                        displayId = row.code;
                    } else if (row.id) {
                        displayId = `#${row.id}`;
                    } else if (row.name) {
                        displayId = row.name;
                    }
                }

                // 获取项目类型名称
                const itemType = 'Draft Lesson';

                console.log('Duplicate operation requested for:', itemType);

                const confirmResult = await this.$alert.confirm({
                    title: `Duplicate ${itemType} ${displayId}`,
                    text: `This will duplicate the ${itemType.toLowerCase()} and create a new draft for the following day. For example, a lesson dated 5/4/2025 will be copied to 6/4/2025. However, this function cannot duplicate items at the end of a month.`,
                });

                // 检查用户是否确认
                if (!confirmResult) {
                    console.log('User cancelled the operation');
                    return; // 如果用户取消,直接返回
                }
         
                // 执行删除操作
                const route = `/${this.$root.app_domain}/private/courses/${object.code}/lessons/draft/${row.id}/duplicate`;

                const response = await this.$api.post(
                    this.$passport_url(route)
                );
                
                if (refreshPage) {
                    this.$alert.success(response.message, null, true);
                } else {
                    // 处理响应
                    this.$root.resetSummary(response.data);
                    this.$root.$refs.listing.fetchData(true);
                }

            } catch (error) {
                console.error('Delete operation failed:', error);
                await this.$alert.error('Failed to duplicate item. Please try again.');
            }
        },
        async handleRegenerate(row) {
            try {
                // 显示确认对话框并等待结果

                const confirmResult = await this.$alert.confirm({
                    title: `Are you sure you want to regenerate lessons for ${row.code}?`,
                    text: 'This action will delete all existing lessons and generate new ones. This action cannot be undone. Please proceed with caution.',
                });
                
                // 检查用户是否确认
                if (!confirmResult) {
                    console.log('User cancelled the operation');
                    return; // 如果用户取消,直接返回
                }
        
                // 用户确认后,执行 API 调用
                const response = await this.$api.get(
                    this.$passport_url(`/admin/private/courses/${row.code}/regenerate`)
                );
                
                // 处理响应
                if (response?.data) {
                    this.resetSummary(response.data);
                    this.$refs.listing.fetchData(true);
                } else {
                    throw new Error('Unexpected response structure');
                }
            } catch (error) {
                console.error('Operation failed:', error);
                await this.$alert.error('System Failed to regenerate lessons.');
            }
        },

        async handleProceedPayment(row, refreshPage = false) {
            try {
                console.log('handleProceedPayment', row);
                // 显示确认对话框并等待结果
                const confirmResult = await this.$alert.confirm({
                    title: `Are you sure you want to add this payment to the invoice?`,
                    text: `This action will find the latest invoice and add the payment. If no latest invoice is found, a new one will be created and regenerated.`,
                });
                
                // 检查用户是否确认
                if (!confirmResult) {
                    console.log('User cancelled the operation');
                    return; // 如果用户取消,直接返回
                }
        
                // 用户确认后,执行 API 调用
                const response = await this.$api.get(
                    this.$passport_url(`/admin/private/payments/${row.id}/proceed`)
                );
                
                // 处理响应
                if (response?.data) {
                    if (refreshPage) {
                        this.$alert.success(response.message, null, true);
                    } else {
                        this.resetSummary(response.data);
                        this.$refs.listing.fetchData(true);
                    }
                } else {
                    throw new Error('Unexpected response structure');
                }
            } catch (error) {
                console.error('Operation failed:', error);
                await this.$alert.error('System Failed to proceed payment.');
            }
        },

        async handleTakeoutPayment(invoice, payment, refreshPage = false) {
            try {
                // 显示确认对话框并等待结果
                const confirmResult = await this.$alert.confirm({
                    title: `Confirm removal of this payment from current invoice ${invoice.code}?`,
                    text: 'This action will remove the payment record and regenerate the invoice. The payment will be scheduled in next month\'s invoice',
                });
                
                // 检查用户是否确认
                if (!confirmResult) {
                    console.log('User cancelled the operation');
                    return; // 如果用户取消,直接返回
                }
        
                // 用户确认后,执行 API 调用
                const response = await this.$api.get(
                    this.$passport_url(`/admin/private/payments/${payment.id}/takeout`)
                );
                
                // 处理响应
                if (response?.data) {
                    if (refreshPage) {
                        this.$alert.success(response.message, null, true);
                    } else {
                        this.resetSummary(response.data);
                        this.$refs.listing.fetchData(true);
                    }
                } else {
                    throw new Error('Unexpected response structure');
                }
            } catch (error) {
                console.error('Operation failed:', error);
                await this.$alert.error('System Failed to takeout payment.');
            }
        },

        async handleDuplicate(row) {
            try {
                // 显示确认对话框并等待结果
                const confirmResult = await this.$alert.confirm({
                    title: `Confirm duplication of this course to next months ${row.code}?`,
                    text: 'This action will create a new course for next month with the same date, time, and lesson/class. Are you sure you want to proceed?',
                });
        
                // 检查用户是否确认
                if (!confirmResult) {
                    console.log('User cancelled the operation');
                    return; // 如果用户取消,直接返回
                }
        
                // 用户确认后,执行 API 调用
                const response = await this.$api.get(
                    this.$passport_url(`/admin/private/courses/${row.code}/duplicate`)
                );
        
                // 处理响应
                if (response?.data) {
                    this.resetSummary(response.data);
                    this.$refs.listing.fetchData(true);
                } else {
                    throw new Error('Unexpected response structure');
                }
            } catch (error) {
                console.error('Operation failed:', error);
                //await this.$alert.error('System failed to create new course.');
            }
        },
        
        applyStudentsFilter() {
            this.applyFilter('students', this.currentStudent);
        },
        
        clearStudentsFilter() {
            this.clearFilter('students');
        },
        
        applyTutorsFilter() {
            this.applyFilter('tutors', this.currentTutor);
        },
        
        clearTutorsFilter() {
            this.clearFilter('tutors');
        },
        
        applySubjectsFilter() {
            this.applyFilter('subjects', this.currentSubject);
        },
        
        clearSubjectsFilter() {
            this.clearFilter('subjects');
        },
        
        applyClientsFilter() {
            this.applyFilter('clients', this.currentClient);
        },
        
        clearClientsFilter() {
            this.clearFilter('clients');
        },
        
        // 年月联合筛选相关方法
        applyYearMonthFilter() {
            this.$options.mixins[0].methods.applyYearMonthFilter.call(this, this.displayYear, this.selectedMonths);
        },

        clearYearMonthFilter() {
            // 调用基类中的方法
            this.$options.mixins[0].methods.clearYearMonthFilter.call(this);
        },
        
        // 日历选择器相关方法
        isMonthSelected(month) {
            return this.$options.mixins[0].methods.isMonthSelected.call(this, month);
        },
        
        toggleMonth(month, label) {
            this.$options.mixins[0].methods.toggleMonth.call(this, month, label);
        },
        
        prevYear() {
            this.$options.mixins[0].methods.prevYear.call(this);
        },
        
        nextYear() {
            this.$options.mixins[0].methods.nextYear.call(this);
        },
        
        resetFilters() {
            // 报告特定的重置过滤器，保留特定字段
            this.filters = _.pick(this.filters, ['status', 'years', 'countrys']);
            
            // 使用循环重置所有筛选器和映射
            ['students', 'tutors', 'subjects', 'clients', 'months'].forEach(filterType => {
                const capitalizedType = filterType.charAt(0).toUpperCase() + filterType.slice(1);
                this[filterType] = {}; // 重置映射
                this['current' + capitalizedType] = []; // 重置当前选择
            });
            
            // 重置年份筛选器
            this.currentYear = null;
            this.year = {};
            this.filters.year = null;
            this.selectedMonths = [];
            
            this.$nextTick(() => {
                this.$refs.listing.fetchData(true);
            });
        }
    },
}