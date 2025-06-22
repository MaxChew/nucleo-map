import baseList from "../base-list";

export default {
    mixins: [baseList],
    
    data() {
        return {
            ...baseList.data(),
            
            // Logs 特有的数据
            current_status: 'all',
            current_log_name: 'all',
            current_event: 'all',
            
            // 分页相关数据
            page: 1,
            lastPage: 1
        };
    },

    computed: {
        // 检查是否有活动日志
        hasLogs() {
            return this.logs_stats && this.logs_stats.total > 0;
        },
        
        // 扩展过滤参数
        filterParams() {
            const baseParams = baseList.computed.filterParams.call(this);
            return {
                ...baseParams,
                log_name: this.current_log_name,
                event: this.current_event
            };
        }
    },

    methods: {
        ...baseList.methods,
        
        // 删除活动日志
        deleteLog(logId, description) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete this activity log: "${description.substring(0, 50)}...". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$api.delete(this.$passport_url(`/logs/${logId}`))
                        .then(response => {
                            this.$swal.fire(
                                'Deleted!',
                                'Activity log has been deleted successfully.',
                                'success'
                            );
                            // 刷新列表组件
                            if (this.$refs && this.$refs.listing) {
                                this.$refs.listing.fetchData(true);
                            } else {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            let errorMessage = 'There was an error deleting the activity log.';
                            if (error.response && error.response.data && error.response.data.error) {
                                errorMessage = error.response.data.error;
                            }
                            this.$swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        });
                }
            });
        },
        
        // 批量清除日志
        bulkClearLogs() {
            this.$swal.fire({
                title: 'Clear All Logs?',
                text: 'This will permanently delete all activity logs. This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, clear all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$api.delete(this.$passport_url('/logs/clear-all'))
                        .then(response => {
                            this.$swal.fire(
                                'Cleared!',
                                'All activity logs have been cleared successfully.',
                                'success'
                            );
                            location.reload();
                        })
                        .catch(error => {
                            this.$swal.fire(
                                'Error!',
                                'There was an error clearing the logs.',
                                'error'
                            );
                        });
                }
            });
        },
        
        // 清除筛选器
        clearFilters() {
            this.current_status = 'all';
            this.current_log_name = 'all';
            this.current_event = 'all';
            
            // 调用基类清除方法
            this.resetFilters();
        }
    },
    
    watch: {
        // 监听筛选器变化
        current_status() {
            this.filterListing();
        },
        
        current_log_name() {
            this.filterListing();
        },
        
        current_event() {
            this.filterListing();
        }
    }
}; 