// page specific mixin imports
import dashboardList from "./dashboard/list";
import usersList from "./users/list";
import logsList from "./logs/list";
import centersList from "./centers/list";

import _ from "lodash";
import { matchUrlPattern } from "./../../libs/helpers";

const ROUTE_PATTERNS = {
    LIST: '',
    CREATE: '/create',
    SHOW: '/*',
    EDIT: '/*/*',
    DETAIL: '/*/*/*'
  };
  
  // 创建一个简单的路由生成函数
  const createAdminRoutes = (path, component, patterns = ['', '/create', '/*', '/*/*', '/*/*/*']) => {
    return patterns.reduce((routes, pattern) => {
      const fullRoute = `/admin/${path}${pattern}`;
      routes[fullRoute] = component;
      return routes;
    }, {});
  };
  
  // 更简洁的路由映射
  export const pagemaps = {
    // 基础路由
    ...createAdminRoutes('dashboard', dashboardList, ['']),
    ...createAdminRoutes('statuslist', dashboardList, ['']),
    
    // 完整 CRUD 路由
    ...createAdminRoutes('users', usersList),
    ...createAdminRoutes('centers', centersList),
    ...createAdminRoutes('roles', {
      // 角色管理特定方法
      methods: {
        deleteRole(roleId, roleName) {
          window.Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the role "${roleName}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              // 使用全局API调用删除角色
              this.$api.delete(this.$passport_url(`/admin/private/roles/${roleId}`))
                .then(response => {
                  window.Swal.fire(
                    'Deleted!',
                    'Role has been deleted successfully.',
                    'success'
                  );
                  this.$refs.listing.refresh();
                  // 更新摘要信息如果提供
                  if (response.meta && response.meta.summary) {
                    this.resetSummary(response.meta.summary);
                  }
                })
                .catch(error => {
                  let errorMessage = 'There was an error deleting the role.';
                  if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = error.response.data.error;
                  }
                  window.Swal.fire(
                    'Error!',
                    errorMessage,
                    'error'
                  );
                });
            }
          });
        },
        resetSummary(summary) {
          // 更新摘要面板的实现
          console.log('Updating summary:', summary);
        }
      }
    }),
    ...createAdminRoutes('logs', logsList),
    
    '/admin/setting': usersList,
  };
  
  export const pages = _(pagemaps)
      .filter((mixin, path) => matchUrlPattern(path, document.location.pathname))
      .values()
      .value();

export default {
    data() {
        return {
            lastWidth: null,
            headerHeight: null,
            footerHeight: null,
            toggleNavMenu: false,
            toggleSideMenu: false,
            toggleSideFilter: false,
            lastScrollY: 0,
            affix: false,
            asideWidth: null,
            showSearchModal: false,
            showSearchBar: true,
            topOfPage: false,
        };
    },
    computed: {
        toggleCanvasData() {
            const { toggleSideMenu, toggleSideFilter } = this;
            return {
                toggleSideMenu,
                toggleSideFilter
            };
        },
    },
    watch: {
        toggleNavMenu: {
            immediate: true,
            handler(showing) {
                this.toggleBodyOverflow(showing);
            },
        },
        showSearchModal: {
            immediate: true,
            handler(showing) {
                this.toggleBodyOverflow(showing);
            },
        },
        toggleCanvasData: {
            immediate: true,
            handler(data) {
                const showing = Object.values(data).includes(true);
                this.toggleBodyOverflow(showing);
            },
            deep: true
        }
    },
    methods: {
        // 将这些方法移到 methods 内部
        determineHeaderWidth() {
            try {
                return document.querySelector('header')?.clientWidth || 0;
            } catch (error) {
                console.error('Error determining header width:', error);
                return 0;
            }
        },

        determineHeaderHeight() {
            return document.querySelector('header')?.clientHeight || 0;
        },

        determineFooterHeight() {
            return document.querySelector('footer')?.clientHeight || 0;
        },

        handleOffsetResize() {
            try {
                let currentWidth = this.determineHeaderWidth();
                if (currentWidth !== this.lastWidth) {
                    this.headerHeight = this.determineHeaderHeight();
                    this.footerHeight = this.determineFooterHeight();
                    this.asideWidth = null;
                    this.lastWidth = currentWidth;
                }
            } catch (error) {
                console.error('Error in handleOffsetResize:', error);
            }
        },

        handleScroll() {
            try {
                const isScrolled = window.pageYOffset > 0;
                if (this.topOfPage === !isScrolled) {
                    this.topOfPage = !isScrolled;
                }
            } catch (error) {
                console.error('Error in handleScroll:', error);
            }
        },

        toggleBodyOverflow(state) {
            try {
                if (typeof state !== 'boolean' && state !== undefined) {
                    console.warn('Invalid state type provided to toggleBodyOverflow');
                    return;
                }

                const bodyClassList = document.body.classList;
                if (state === undefined) {
                    state = !bodyClassList.contains('overflow-hidden');
                }

                if (state) {
                    this.lastScrollY = window.scrollY;
                    bodyClassList.add('overflow-hidden');
                } else {
                    bodyClassList.remove('overflow-hidden');
                    window.scroll(0, this.lastScrollY);
                }
            } catch (error) {
                console.error('Error in toggleBodyOverflow:', error);
            }
        },

        setAsideAffix() {
            try {
                if (!this.$refs.aside) return;

                if (!this.asideWidth) {
                    this.asideWidth = this.$refs.aside.clientWidth;
                }

                let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                let asideMargin = parseFloat(window.getComputedStyle(this.$refs.aside)['margin-top']);
                let asideHeight = this.$refs.aside.clientHeight;
                let asideOffsetTop = this.$refs.aside.parentElement.getBoundingClientRect().top +
                    scrollTop - (this.$refs.fixedHeaderMargin?.clientHeight || 0);
                let asideOffsetBottom = asideOffsetTop + this.$refs.aside.parentElement.clientHeight;
                let asideBottom = scrollTop + asideHeight + (this.$refs.fixedHeaderMargin ? 0 : asideMargin);

                if (scrollTop > asideOffsetTop) {
                    if (asideBottom >= asideOffsetBottom) {
                        if (this.affix !== false) {
                            this.affix = 'bottom';
                        }
                        return;
                    }
                    this.affix = 'top';
                    return;
                }
                this.affix = false;
            } catch (error) {
                console.error('Error in setAsideAffix:', error);
            }
        },

        autoCollapseSearchBar() {
            if (this.toggleNavMenu) return;
            this.showSearchBar = (document.body.scrollTop || document.documentElement.scrollTop) <= 30;
        },
        toggleCanvas(element) {
            try {
                if (!element) {
                    console.warn('No element ID provided to toggleCanvas');
                    return;
                }

                this.toggleBackdrop();
                const canvas = document.getElementById(element);
                if (!canvas) {
                    console.warn(`Canvas element with ID "${element}" not found`);
                    return;
                }

                canvas.classList.toggle('is-active');
            } catch (error) {
                console.error('Error in toggleCanvas:', error);
            }
        },
        toggleBackdrop() {
            const bodyClassList = document.body.classList;
            if (bodyClassList.contains('offcanvas-active')) {
                bodyClassList.remove('offcanvas-active');
                let backdrop = document.querySelector('div.backdrop');
                document.body.removeChild(backdrop);
            } else {
                let backdrop = document.createElement('div');
                backdrop.classList.add('backdrop');
                document.body.appendChild(backdrop);

                bodyClassList.add('offcanvas-active');
            }
        },
        async copyUrl(link) {
            if (!link) {
                return;
            }

            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(link);
                } else {
                    const textArea = document.createElement("textarea");
                    textArea.value = link;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                }
                this.$alert.success('Copied item link successfully!');
            } catch (err) {
                console.error('Failed to copy: ', err);
                this.$alert.error('Unable to copy item link!');
            }
        },
    },
    created() {
        window.addEventListener('resize', this.handleOffsetResize);
    },
    mounted() {
        if (this.$refs.aside) {
            window.addEventListener('scroll', this.setAsideAffix);
            this.setAsideAffix();
        }
        if (this.$refs.searchBar) {
            window.addEventListener('scroll', this.autoCollapseSearchBar);
            this.autoCollapseSearchBar();
        }

        if (this.$refs.navBar) {
            window.addEventListener('scroll', this.handleScroll);
            this.handleScroll();
        }
    },
    beforeDestroy() {
        // 清理 resize 事件监听
        window.removeEventListener('resize', this.handleOffsetResize);

        // 清理 scroll 事件监听
        if (this.$refs.aside) {
            window.removeEventListener('scroll', this.setAsideAffix);
        }
        if (this.$refs.searchBar) {
            window.removeEventListener('scroll', this.autoCollapseSearchBar);
        }
        if (this.$refs.navBar) {
            window.removeEventListener('scroll', this.handleScroll);
        }
    }
};
