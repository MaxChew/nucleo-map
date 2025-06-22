import ErrorBag from './../libs/ErrorBag';
import _ from 'lodash';

const breakpoints = {
    'xl': 1200,
    'lg': 992,
    'md': 768,
    'sm': 576,
    'xs': 0,
};

function determineScreensize() {
    const width = document.body.clientWidth;
    return _.findKey(breakpoints, px => width >= px);
}

export default {
    data() {
        return {
            errors: {},
            verrors: new ErrorBag(),
            screenSize: determineScreensize(),
        };
    },
    computed: {
        isMobileScreen() {
            return this.screenSize === 'xs' || this.screenSize === 'sm';
        },
        isTabletScreen() {
            return this.screenSize === 'md' || this.screenSize === 'sm';
        },
        isDesktopScreen() {
            return this.screenSize === 'lg' || this.screenSize === 'xl';
        },
    },
    methods: {
        closeLessonDrawer() {
            this.$root.isLessonDrawerOpen = false;
            this.$root.object = null;
            this.$root.showRejectPopUp = false;
            this.$root.rescheduleAgain = false;
        },
        screenGT(size) {
            const target = Object.keys(breakpoints).indexOf(size);
            const current = Object.keys(breakpoints).indexOf(this.screenSize);
            return current < target;
        },
        screenLT(size) {
            const target = Object.keys(breakpoints).indexOf(size);
            const current = Object.keys(breakpoints).indexOf(this.screenSize);
            return current > target;
        },
        screenGTE(size) {
            const target = Object.keys(breakpoints).indexOf(size);
            const current = Object.keys(breakpoints).indexOf(this.screenSize);
            return current <= target;
        },
        screenLTE(size) {
            const target = Object.keys(breakpoints).indexOf(size);
            const current = Object.keys(breakpoints).indexOf(this.screenSize);
            return current >= target;
        },
        handleResize() {
            this.screenSize = determineScreensize();
        },
        difference(object, base) {
            function changes(object, base) {
                return _.transform(object, function (result, value, key) {
                    if (!_.isEqual(value, base[key])) {
                        result[key] = (_.isObject(value) && _.isObject(base[key])) ? changes(value, base[key]) : value;
                    }
                });
            }
            return changes(object, base);
        },
        resetForm(event) {
            const form = event.target.form;
            const elements = form.querySelectorAll('input, input[type=checkbox], input[type=radio]');
            if (elements.length) {
                elements.forEach(function (element) {
                    element.value = '';
                    element.checked = false;
                });
            }
        },
        showMoreTags(e) {
            const hiddenTags = document.querySelectorAll('#taggings > [data-hidden]');
            hiddenTags.forEach(tag => {
                if (tag.getAttribute('data-hidden') === 'true') {
                    tag.classList.remove('hidden');
                    tag.setAttribute('data-hidden', false);
                    e.target.innerHTML = '...Show less';
                } else {
                    tag.classList.add('hidden');
                    tag.setAttribute('data-hidden', true);
                    e.target.innerHTML = '...Show more';
                }
            });
        },
        sortByCriteria(e) {
            const sortBy = e.target.value;
            const urlParams = new URLSearchParams(window.location.search);

            urlParams.set('sortBy', sortBy);

            window.location.search = urlParams;
        },
        async handleDelete(row, name, moduleName, routeUrl, refreshPage = false) {
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
                const itemType = name || 'Item';

                console.log('Delete operation requested for:', itemType);

                const confirmResult = await this.$alert.confirm({
                    title: `Delete ${itemType} ${displayId}`,
                    text: `This will permanently remove the ${itemType.toLowerCase()}. This action cannot be undone.`,
                });

                // 检查用户是否确认
                if (!confirmResult) {
                    console.log('User cancelled the operation');
                    return; // 如果用户取消,直接返回
                }
         
                // 执行删除操作
                const route = routeUrl + '/' + row.id || `/${this.$root.app_domain}/private/${moduleName}/${row.code}`;

                const response = await this.$api.destroy(
                    this.$passport_url(route)
                );
                
                if (refreshPage) {
                    this.$alert.success(response.message, null, refreshPage);
                } else {
                    // 处理响应
                    this.$root.resetSummary(response.data);
                    this.$root.$refs.listing.fetchData(true);
                }

            } catch (error) {
                console.error('Delete operation failed:', error);
                await this.$alert.error('Failed to remove item.');
            }
         },
         
         async handleRestore(id, name) {
            try {
                // 显示确认对话框
                const confirmResult = await this.$alert.confirm(
                    `Are you sure you want restore this ${this.name}?`
                );
         
                // 检查用户是否确认
                if (!confirmResult.isConfirmed) {
                    return;
                }
         
                // 执行恢复操作
                const response = await this.$api.get(
                    this.$passport_url(`/admin/private/${this.name}s/${id}/restore`)
                );
         
                // 处理响应
                if (response?.data) {
                    await this.$alert.success('Restore Successfully.');
                    this.$root.resetSummary(response.data);
                    this.$root.$refs.listing.fetchData(true);
                } else {
                    throw new Error('Unexpected response structure');
                }
            } catch (error) {
                console.error('Restore operation failed:', error); 
                await this.$alert.error('Failed to restore item.');
            }
         }
    },
    created() {
        const { validation, ...other } = this.errors;
        if (validation) this.verrors.record(validation);
        this.errors = _.mapValues(other, (errors, key) => new ErrorBag(errors));
    },
    mounted() {
        this.handleResize();
        window.addEventListener('resize', this.handleResize);
    },
};
