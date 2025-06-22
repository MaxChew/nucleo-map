import _ from 'lodash';

export default {
    data() {
        return {
            // 基础数据，可被扩展
            filters: {
                keyword: '',
                status: '',
                months: [],
                year: null
            },
            // 年月筛选相关数据
            months: {},
            year: {},
            currentMonths: [],
            currentYear: null,
            displayYear: new Date().getFullYear(),
            selectedMonths: []
        }
    },
    
    mounted() {
        // 添加全局点击事件监听器，如果在helpers.js中已定义，这里可移除
        if (typeof onDocumentClick !== 'undefined') {
            document.addEventListener('click', onDocumentClick);
        }
    },
    
    beforeUnmount() {
        // 移除全局点击事件监听器，如果在helpers.js中已定义，这里可移除
        if (typeof onDocumentClick !== 'undefined') {
            document.removeEventListener('click', onDocumentClick);
        }
    },
    
    computed: {
        // 基础过滤参数计算属性
        filterParams() {
            const filterParams = _.omitBy(this.filters, value => !value);

            if (filterParams.keyword) {
                filterParams.keyword = '~' + filterParams.keyword;
            }
            
            if (!_.isEmpty(this.filters.status)) {
                filterParams.status = this.filters.status;
            }
            
            return filterParams;
        },
        
        // 获取筛选器标签的通用方法
        getFilterLabel() {
            return function(filterType) {
                // 检查过滤类型是否存在映射对象
                if (!this[filterType] || _.isEmpty(this[filterType])) {
                    return '';
                }
                
                // 从过滤器映射获取标签
                const labels = Object.values(this[filterType]);
                if (labels.length === 0) {
                    return '';
                }
                
                // 返回标签字符串
                return labels.join(', ');
            };
        }
    },
    
    methods: {
        // 状态变更处理
        handleStatusChange(status) {
            this.$root.current_status = status;
            
            this.$nextTick(() => {
                try {
                    if (this.$refs.listing) {
                        this.$refs.listing.fetchData(true);
                    } else {
                        console.error('$refs.listing not found');
                    }
                } catch (error) {
                    console.error('Error in handleStatusChange fetchData:', error);
                }
            });
        },
        
        // 重置摘要信息
        resetSummary(summary) {
            this.$root.selectedRow = [];
            if (summary != null) {
                this.$root.summary = summary;
            }
        },
        
        // 重置过滤器
        resetFilters() {
            // 可在扩展中自定义保留哪些过滤器
            this.filters = _.pick(this.filters, ['status']);
            
            try {
                if (this.$refs.listing) {
                    this.$refs.listing.fetchData(true);
                } else {
                    console.error('$refs.listing not found in resetFilters');
                }
            } catch (error) {
                console.error('Error in resetFilters fetchData:', error);
            }
        },
        
        // 列表过滤处理
        filterListing(e, vm) {
            if (vm) {
                vm.loading = false;
            }
            
            if (e) {
                e.preventDefault();
            }
            
            try {
                if (this.$refs.listing) {
                    this.$refs.listing.fetchData(true);
                } else {
                    console.error('$refs.listing not found in filterListing');
                    console.log('Available $refs:', Object.keys(this.$refs));
                }
            } catch (error) {
                console.error('Error in filterListing fetchData:', error);
            }
        },
        
        // 通用筛选器应用方法
        applyFilter(filterType, currentItems) {
            // 构建映射对象
            const itemsMap = {};
            
            // 提取选中项的值和标签
            if (currentItems && currentItems.length) {
                currentItems.forEach(item => {
                    if (item && item.value && item.label) {
                        itemsMap[item.value] = item.label;
                    } else {
                        console.log('Item error - no value/label:', item);
                    }
                });
            }
            
            // 更新映射对象
            this[filterType] = itemsMap;
            
            // 提取值数组
            const values = currentItems.map(item => item.value).filter(Boolean);

            if (this.$set) {
                // Vue 2
                this.$set(this.filters, filterType, values);
            } else {
                // Vue 3
                this.filters[filterType] = values;
            }
            
            this.$nextTick(() => {
                try {
                    if (this.$refs.listing) {
                        this.$refs.listing.fetchData(true);
                    } else {
                        console.error('$refs.listing not found in applyFilter');
                    }
                } catch (error) {
                    console.error('Error in applyFilter fetchData:', error);
                }
            });
        },
        
        // 通用筛选器清除方法
        clearFilter(filterType) {
            // 清空当前选择的项目
            const capitalizedType = filterType.charAt(0).toUpperCase() + filterType.slice(1);
            const currentProperty = 'current' + capitalizedType;
            
            this[currentProperty] = [];
            this[filterType] = {}; // 清空映射
            
            if (this.$set) {
                // Vue 2
                this.$set(this.filters, filterType, []);
            } else {
                // Vue 3
                this.filters[filterType] = [];
            }
            
            this.$nextTick(() => {
                try {
                    if (this.$refs.listing) {
                        this.$refs.listing.fetchData(true);
                    } else {
                        console.error('$refs.listing not found in clearFilter');
                    }
                } catch (error) {
                    console.error('Error in clearFilter fetchData:', error);
                }
            });
        },
        
        // 年月联合筛选相关方法（通用基础版本）
        applyYearMonthFilter(displayYear, selectedMonths) {
            // 应用年份筛选
            if (displayYear) {
                this.currentYear = displayYear;
                this.year = { [displayYear]: displayYear };
                this.filters.year = displayYear;
            }
            
            // 应用月份筛选
            if (selectedMonths && selectedMonths.length) {
                const monthsMap = {};
                const monthValues = [];
                
                selectedMonths.forEach(item => {
                    if (item && item.value && item.label) {
                        monthsMap[item.value] = item.label;
                        monthValues.push(item.value);
                    }
                });
                
                this.months = monthsMap;
                this.filters.months = monthValues;
                this.currentMonths = selectedMonths;
            } else {
                this.months = {};
                this.filters.months = [];
                this.currentMonths = [];
            }
            
            this.$nextTick(() => {
                try {
                    if (this.$refs.listing) {
                        this.$refs.listing.fetchData(true);
                    } else {
                        console.error('No listing reference found');
                    }
                } catch (error) {
                    console.error('Error calling fetchData:', error);
                }
            });
        },

        clearYearMonthFilter() {
            // 清除年份筛选
            this.currentYear = null;
            this.year = {};
            this.filters.year = null;
            
            // 清除月份筛选
            this.currentMonths = [];
            this.months = {};
            this.filters.months = [];
            if (this.selectedMonths) {
                this.selectedMonths = [];
            }
            
            this.$nextTick(() => {
                try {
                    if (this.$refs.listing) {
                        this.$refs.listing.fetchData(true);
                    } else {
                        console.error('No listing reference found');
                    }
                } catch (error) {
                    console.error('Error calling fetchData:', error);
                }
            });
        },
        
        // 日历选择器相关通用方法
        isMonthSelected(month) {
            return this.selectedMonths && 
                  this.selectedMonths.findIndex(item => item.value === month) > -1;
        },
        
        toggleMonth(month, label) {
            this.selectedMonths = this.selectedMonths || [];
            const index = this.selectedMonths.findIndex(item => item.value === month);
            if (index > -1) {
                this.selectedMonths.splice(index, 1);
            } else {
                this.selectedMonths.push({
                    value: month,
                    label: label,
                    year: this.displayYear
                });
            }
        },
        
        prevYear() {
            this.displayYear = parseInt(this.displayYear) - 1;
        },
        
        nextYear() {
            this.displayYear = parseInt(this.displayYear) + 1;
        }
    }
} 