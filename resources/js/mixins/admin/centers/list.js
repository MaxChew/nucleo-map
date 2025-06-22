import baseList from '../base-list';

export default {
    name: 'centers-list',
    
    mixins: [baseList],
    
    data() {
        return {
            // 继承base-list的所有数据
            ...baseList.data(),
            
            // 医疗中心特有的数据
            current_status: 'all',
            current_state: 'all',
            current_service: 'all',
            centerModal: false,
            
            // 分页相关数据
            page: 1,
            lastPage: 1
        }
    },
    
    computed: {
        // 继承并扩展过滤参数
        filterParams() {
            const baseParams = baseList.computed.filterParams.call(this);
            const extendedParams = {
                ...baseParams,
                state: this.current_state,
                service: this.current_service,
            };
            
            return extendedParams;
        },
        
        // 检查是否有医疗中心数据
        hasCenters() {
            return this.status && this.status.total > 0;
        },
    },
    
    methods: {
        // 继承base-list的所有方法
        ...baseList.methods,
        
        // 医疗中心特有方法
        deleteCenter(centerId, centerName) {
            this.$swal({
                title: '确认删除',
                text: `确定要删除医疗中心 "${centerName}" 吗？`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '删除',
                cancelButtonText: '取消'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$api.delete(`admin/private/centers/${centerId}`)
                        .then(response => {
                            this.$swal({
                                title: '删除成功',
                                text: '医疗中心已成功删除',
                                icon: 'success',
                                timer: 2000
                            });
                            this.refreshData();
                        })
                        .catch(error => {
                            this.$swal({
                                title: '删除失败',
                                text: error.response?.data?.message || '删除过程中发生错误',
                                icon: 'error'
                            });
                        });
                }
            });
        },
        
        // 切换医疗中心状态
        toggleCenterStatus(center) {
            this.$api.post(`admin/private/centers/${center.id}/status`)
                .then(response => {
                    this.$swal({
                        title: '状态更新成功',
                        text: `医疗中心状态已${center.is_active ? '停用' : '激活'}`,
                        icon: 'success',
                        timer: 2000
                    });
                    this.refreshData();
                })
                .catch(error => {
                    this.$swal({
                        title: '状态更新失败',
                        text: error.response?.data?.message || '状态更新过程中发生错误',
                        icon: 'error'
                    });
                });
        },
        
        // 清除州筛选
        clearStateFilter() {
            this.current_state = 'all';
            this.filterListing();
        },
        
        // 清除服务筛选
        clearServiceFilter() {
            this.current_service = 'all';
            this.filterListing();
        },
        
        // 导出医疗中心数据
        exportCenters() {
            this.$api.get('admin/private/centers', {
                params: {
                    ...this.filterParams,
                    export: true,
                    format: 'csv'
                }
            })
            .then(response => {
                // 创建下载链接
                const blob = new Blob([response.data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `centers_${new Date().toISOString().slice(0, 10)}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                this.$swal({
                    title: '导出失败',
                    text: '导出过程中发生错误',
                    icon: 'error'
                });
            });
        },
        
        // 添加刷新数据方法
        refreshData() {
            try {
                if (this.$refs.listing) {
                    this.$refs.listing.fetchData(true);
                } else {
                    console.error('$refs.listing not found in refreshData');
                }
            } catch (error) {
                console.error('Error in refreshData:', error);
            }
        },
        
        // 清除筛选器 (template中调用的方法)
        clearFilters() {
            // 清除所有中心特有的筛选
            this.current_state = 'all';
            this.current_service = 'all';
            
            // 调用基类清除方法
            this.resetFilters();
        },
    },
    
    watch: {
        // 监听州筛选变化
        current_state(newVal, oldVal) {
            this.filterListing();
        },
        
        // 监听服务筛选变化  
        current_service(newVal, oldVal) {
            this.filterListing();
        }
    }
}; 