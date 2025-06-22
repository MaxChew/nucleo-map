import BaseList from '../base-list';
import _ from 'lodash';

export default {
    mixins: [BaseList],

    data() {
        return {
            // 扩展基础过滤器
            filters: {
                keyword: '',
                status: '',
                months: [],
                year: null,
                // 用户特定的过滤器
                countrys: [],
                gender: ''
            },
            // Vue data 相关变量
            current_status: 'all',
            current_type: 'all',
            
            // 国家相关数据
            countrys: {},
            currentCountrys: [],
            // 映射对象
            months: {},
            year: {},
            // 当前选择的项目
            currentMonths: [],
            currentYear: null,
            displayYear: new Date().getFullYear(),
            selectedMonths: [],
            
            // 分页相关数据
            page: 1,
            lastPage: 1
        }
    },
    computed: {
        countrysLabel() {
            if (!_.isEmpty(this.filters["countrys"])) {
                const selected = this.filters["countrys"].slice(0);
                return selected.length > 1
                    ? selected.length + " Countries"
                    : selected
                          .map((countrys) => this.countrys[countrys])
                          .join(", ");
            }
            return '';
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
            const filterParams = _.omitBy(this.filters, value => !value);

            // 处理关键词搜索
            if (filterParams.keyword) {
                filterParams.keyword = '~' + filterParams.keyword;
            }
            
            // 处理状态过滤
            if (!_.isEmpty(this.filters.status)) {
                filterParams.status = this.filters.status;
            }
            
            // 处理性别筛选
            if (this.filters.gender) {
                filterParams.gender = this.filters.gender;
            }
            
            // 处理数组类型的筛选器
            ['months', 'countrys'].forEach(filterType => {
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
            
            return filterParams;
        }
    },
    methods: {
        handleStatusChange(status) {
            this.$root.current_status = status;
            this.$nextTick(() => {
                this.$refs.listing.fetchData(true);
            });
        },
        
        // 年月联合筛选相关方法
        applyYearMonthFilter() {
            this.$options.mixins[0].methods.applyYearMonthFilter.call(this, this.displayYear, this.selectedMonths);
        },

        clearYearMonthFilter() {
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
        
        resetSummary(summary) {
            console.log('summary', summary);
            this.$root.selectedRow = []
            if (summary != null) {
                this.$root.summary = summary
            }
        },
        resetFilters() {
            // 重置所有筛选器到初始状态
            this.filters = {
                keyword: '',
                status: '',
                months: [],
                year: null,
                countrys: [],
                gender: ''
            };
            
            // 重置状态和类型
            this.current_status = 'all';
            this.current_type = 'all';
            
            // 重置月份筛选器
            this.months = {};
            this.currentMonths = [];
            this.selectedMonths = [];
            
            // 重置年份筛选器
            this.currentYear = null;
            this.year = {};
            
            // 重置国家筛选器
            this.countrys = {};
            this.currentCountrys = [];
            
            this.$refs.listing.fetchData(true);
        },
        filterListing(e, vm) {
            if (vm) {
                vm.loading = false;
            }
            
            if (e) {
                e.preventDefault();
            }
            
            this.$nextTick(() => {
                try {
                    if (this.$refs.listing) {
                        this.$refs.listing.fetchData(true);
                    } else {
                        console.error('$refs.listing not found in users filterListing');
                    }
                } catch (error) {
                    console.error('Error in users filterListing:', error);
                }
            });
        },
        // 国家筛选相关方法
        applyCountrysFilter() {
            this.applyFilter('countrys', this.currentCountrys);
        },
        
        clearCountrysFilter() {
            this.clearFilter('countrys');
        },
        
        // 删除用户方法
        deleteUser(userId, userName) {
            window.Swal.fire({
                title: '确认删除？',
                text: `您即将删除用户 "${userName}"，此操作无法撤销！`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '确认删除',
                cancelButtonText: '取消'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$api.delete(this.$passport_url(`/admin/private/users/${userId}`))
                        .then(response => {
                            window.Swal.fire(
                                '删除成功！',
                                '用户已成功删除。',
                                'success'
                            );
                            this.$refs.listing.fetchData(true);
                            // 更新摘要信息
                            if (response.meta && response.meta.summary) {
                                this.resetSummary(response.meta.summary);
                            }
                        })
                        .catch(error => {
                            let errorMessage = '删除用户时发生错误。';
                            if (error.response && error.response.data && error.response.data.error) {
                                errorMessage = error.response.data.error;
                            }
                            window.Swal.fire(
                                '错误！',
                                errorMessage,
                                'error'
                            );
                        });
                }
            });
        }
    },
}