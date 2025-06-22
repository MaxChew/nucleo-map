## Nucleo Map Project

这是一个基于 Laravel + Vue 3 的地图应用项目。

## ⚠️ 開發注意事項（For All Developers & AI Assistants）

請務必遵守以下規則：

1. 所有回覆必須使用 **中文**。
2. 回覆前，請先完整閱讀根目錄中的 `README.md`。
3. **🚨 AI工具必讀**: 在生成任何代码前，必须先阅读 `AI_DEVELOPMENT_RULES.md` 并严格遵循其中的所有规则。
4. **🎨 颜色标准**: 所有Blade文件必须遵循 `FRESH_COLOR_STANDARD.md` 中定义的颜色规范。
5. 優先參考以下文件（依照順序）：
   - **`AI_DEVELOPMENT_RULES.md`** - AI工具强制规则
   - **`FRESH_COLOR_STANDARD.md`** - 颜色使用标准
   - `FRESH_API_CONTROLER_INSTALL.md`
   - `FRESH_VUEDATA_INSTALL.md`
   - `FRESH_ADMIN_INDEX_TEMPLATE_GUIDE.md`
   - `FRESH_FONTAWESOME.md`
   - `NUCLEO_MAP_COLOR_STANDARDS.md`
6. 每次 Pull Request 被接受後，**請務必同步更新 `README.md` 文件**。
7. 若 Reviewer 發現以上規則未遵守，有權要求修改後重提。

> ⛔ **若你是 AI 工具（如 Cursor、Copilot 等），請務必先讀取 `AI_DEVELOPMENT_RULES.md` 和 `FRESH_COLOR_STANDARD.md`，然後依照規範來產生回覆內容。**
> 
> 🚨 **违反颜色标准或开发规则的代码将被拒绝！**

### 前端技术栈

- **Vue 3** - 前端框架
- **Pinia** - 状态管理
- **Vite** - 构建工具
- **Tailwind CSS** - 样式框架
- **Vue Router** - 路由管理

### 最新更新 (2025年1月)

- 🔄 **RoleController API升级** - 按照高级标准重构角色管理API控制器
  - **RestfulController基类**: 使用Maxxidev RestfulController提供标准化RESTful接口
  - **Resource转换**: 集成RoleResource用于API响应数据的标准化转换
  - **EloquentRepository**: 使用仓库模式管理数据查询逻辑
  - **服务层分离**: 创建RoleService处理业务逻辑，保持控制器精简
  - **高级过滤**: 支持关键词搜索、权限状态筛选等复杂查询功能
  - **数据统计**: 提供角色状态汇总统计信息
  - **系统角色保护**: 防止删除或修改系统关键角色（super-admin、admin）
  - **关联验证**: 检查角色是否有关联用户，防止误删有用户的角色
  - **事务处理**: 使用数据库事务确保数据操作的完整性
  - **权限计算**: 自动计算角色的权限数量和用户数量
  - **删除权限**: 智能判断角色是否可以删除

- 🔄 **UserController API升级** - 按照高级标准重构用户管理API控制器
  - **RestfulController基类**: 使用Maxxidev RestfulController提供标准化RESTful接口
  - **Resource转换**: 集成UserResource用于API响应数据的标准化转换
  - **EloquentRepository**: 使用仓库模式管理数据查询逻辑
  - **服务层分离**: 创建UserService处理业务逻辑，保持控制器精简
  - **高级过滤**: 支持关键词搜索、状态筛选等复杂查询功能
  - **数据统计**: 提供用户状态汇总统计信息
  - **密码管理**: 独立的密码更新功能，支持默认密码设置
  - **事务处理**: 使用数据库事务确保数据操作的完整性
  - **简化设计**: 根据当前User模型实际字段简化功能，移除不需要的地址和媒体相关功能

- 🔧 **活动日志Event列优化** - 完善Event列的显示逻辑，处理空值情况
  - **空值处理**: 当Event字段为空时显示"N/A"，提供更好的用户体验
  - **CSS类优化**: 更新CSS类条件判断，确保空值和未知事件类型都使用灰色标签
  - **视觉统一**: 保持所有日志条目的视觉一致性，避免空白显示
  - **前端重构**: 重新构建前端资源以应用Event列的显示改进

- 🔧 **Moment.js全局属性修复** - 修复活动日志页面中$dayjs未定义的错误
  - **全局属性注册**: 在admin.js中添加`$dayjs`和`$moment`全局属性，映射到moment.js库
  - **依赖导入**: 正确导入moment.js库到admin.js中
  - **前端重构**: 重新构建前端资源，确保所有Vue模板中的日期格式化函数正常工作
  - **兼容性修复**: 解决活动日志页面视图模板中使用`$dayjs()`函数导致的"TypeError: $dayjs is not a function"错误
  - **错误解决**: 修复listing.vue第213行及相关的日期格式化问题

- 🔧 **活动日志页面修复** - 修复活动日志管理页面的Vue架构问题
  - **Mixin创建**: 为活动日志创建专门的mixin (`resources/js/mixins/admin/logs/list.js`)
  - **计算属性修复**: 添加缺失的`hasLogs`计算属性，基于统计数据判断是否有日志
  - **方法集成**: 将`deleteLog`和`bulkClearLogs`方法从内联脚本迁移到mixin系统
  - **路由修复**: 修复日志详情页面的路由调用从`{{ app('domain') }}.logs.show`改为`admin.logs.show`
  - **架构统一**: 移除内联Vue脚本，完全遵循项目的mixin架构模式
  - **全局属性**: 在admin.js中添加`$swal`全局属性支持SweetAlert2调用
  - **路由映射**: 在admin mixin索引中添加logs路由映射，确保正确加载logs mixin
  - **错误解决**: 解决了"hasLogs is not defined"和Vue挂载冲突等错误

- 🔧 **角色管理界面优化** - 修复Vue模板中的路由函数调用错误并简化状态面板
  - **路由函数修正**: 将`$route`改为`$ziggyRoute`，解决Vue模板中的路由调用问题
  - **路由重新生成**: 重新生成Ziggy路由文件，确保包含最新的角色管理路由
  - **状态面板简化**: 移除"With Permissions"和"Without Permissions"状态卡片，只保留总数统计
  - **前端资源重建**: 重新编译前端资源，确保所有修改生效
  - **API集成**: 角色管理API已正确实现deleteRole方法和can_delete字段判断

- 🔧 **Laravel Permission兼容性问题彻底解决** - 完全修复Spatie Permission包的所有兼容性错误
  - **用户模型获取优化**: 简化Role模型中用户模型类名获取逻辑，直接使用auth配置避免复杂guard检测
  - **方法签名修复**: 修复`App\Models\Role::users()`方法与父类Spatie\Permission\Models\Role的返回类型兼容性
  - **关系验证优化**: 在CaptureAuthors trait和Role模型中添加严格的类名验证和错误处理
  - **空值处理**: 当用户模型类不存在或为null时，安全返回null而不是抛出"Class name must be a valid object"错误
  - **错误预防**: 彻底防止HasRelationships中的类名验证错误和InvalidArgumentException
  - **自定义Role模型**: 创建完全兼容的`App\Models\Role`，正确继承Spatie Role模型并实现作者追踪
  - **数据库迁移**: 为roles表添加created_by、updated_by、deleted_by字段和外键约束
  - **配置更新**: 修改permission.php配置使用自定义Role模型
  - **调试代码清理**: 移除RoleController中的调试语句，确保生产环境稳定性
  - **功能验证**: 通过Tinker验证Role->users关系正常工作，返回正确的用户集合
  - **完全解决**: 所有Permission相关错误已彻底修复，系统运行稳定
- 🔧 **Vue应用架构冲突修复** - 修复角色管理页面中的Vue应用挂载冲突
  - **内联脚本移除**: 移除roles/index.blade.php中导致`Vue is not defined`错误的内联Vue脚本
  - **Mixin集成**: 将`deleteRole`方法集成到admin mixin系统中，避免多重Vue应用挂载
  - **全局属性**: 在admin.js中添加`$api`和`$passport_url`全局属性支持API调用
  - **架构统一**: 遵循项目的"传统Blade模板 + Vue增强"架构模式
  - **错误解决**: 解决了浏览器控制台中的"ReferenceError: Vue is not defined"错误
- 🔧 **SVG路径错误修复** - 修复状态面板中的SVG弧形标志错误
  - **路径字符串修复**: 修复状态面板中`712-2h2`应该为`11-2h2`的SVG路径错误
  - **弧形标志正确**: 确保SVG路径中的弧形标志参数符合'0'或'1'的格式要求
  - **错误解决**: 解决了浏览器控制台中的SVG路径解析错误
  - **界面稳定**: 状态面板图标现在能正确显示，不再出现路径错误
- 🔧 **Ziggy路由系统完整修复** - 修复Vue组件中的路由访问错误
  - **路由函数修复**: 修复admin.js中$ziggyRoute函数的Ziggy配置传递问题
  - **动态路由名称修复**: 将`{{ app('domain') }}.users.show`替换为静态的`admin.users.show`路由名称
  - **Vue组件兼容**: 确保Vue模板中的路由引用能正确解析，避免undefined错误
  - **前端稳定性**: 解决了Toggle.vue和Listing.vue中的路由访问问题
- 🔧 **前端错误修复与稳定性优化** - 修复Vue应用和SVG相关的前端错误
  - **Vue混入错误处理**: 在admin.js中添加try-catch块，防止mixin数据获取时的解构错误
  - **状态面板SVG修复**: 修复状态面板中重复的SVG路径导致的arc flag错误
  - **资源重建**: 重新构建前端资源，确保所有修复生效
  - **错误容错**: 添加错误处理机制，提高应用的稳定性和用户体验
- 🔧 **用户管理路由修复** - 修复Ziggy路由问题，完善用户管理功能
  - **视图文件创建**: 创建缺失的 `admin.users.show`、`admin.users.edit`、`admin.users.create` 视图文件
  - **路由生成**: 重新生成Ziggy路由缓存，修复前端路由错误
  - **界面完善**: 添加用户详情页面、编辑表单和创建表单
  - **操作按钮**: 在用户列表页面添加"添加用户"按钮
  - **功能完整**: 现在支持完整的用户CRUD操作（创建、查看、编辑、删除）
- 🔐 **Sanctum API 认证修复** - 解决管理员API接口401认证错误问题
  - **中间件启用**: 在API中间件组中启用 `EnsureFrontendRequestsAreStateful` 中间件
  - **域名配置**: 将 `nucleo-map.test` 添加到Sanctum的受信任域名列表
  - **CSRF保护**: 为前端Axios请求添加自动CSRF token获取和验证
  - **请求拦截**: 实现API请求拦截器，确保在调用API前获取CSRF cookie
  - **路由添加**: 添加 `/sanctum/csrf-cookie` 路由支持CSRF令牌获取
  - **认证修复**: 修复用户列表、角色管理等管理界面的API认证问题
- 🎨 **状态面板样式调整** - 移除所有white文本颜色类，简化悬停效果
  - **文本颜色移除**: 移除所有`text-white`和`group-hover:text-white`类
  - **SVG图标优化**: 移除SVG图标的白色文本类，使用默认stroke颜色
  - **悬停效果简化**: 保留背景渐变效果，但移除文本颜色变化
  - **代码清理**: 简化CSS类结构，保持动画过渡效果
  - **视觉统一**: 所有状态卡片使用一致的样式规则
- 🎨 **Favicon 修复** - 修复网站图标缺失问题，创建完整的favicon文件集
  - **多尺寸支持**: 提供 16x16、32x32、64x64 像素和ICO格式的favicon
  - **品牌标识**: 使用蓝色"N"字母作为Nucleo品牌标识
  - **错误修复**: 解决浏览器404错误，提升用户体验
  - **文件完整**: `favicon.ico`、`favicon-16x16.png`、`favicon-32x32.png`、`favicon-64x64.png`
- 🚀 **Mixin 架构优化** - 重构用户列表管理系统，提升代码复用性和维护性
  - **BaseList 重构**: 将通用列表功能抽象到基础 mixin，包含过滤器、年月筛选、状态处理等核心功能
  - **用户列表优化**: 用户列表 mixin 现在继承并扩展基础功能，支持国家筛选等专有功能
  - **方法统一**: 标准化筛选器应用和清除方法，提供更一致的API接口
  - **代码简化**: 移除重复代码，通过继承实现功能共享，提升开发效率
- 🔧 **Vue 应用架构重构** - 修复了多个关键的前端问题并简化了架构
  - **路由冲突修复**: 解决了 Vue Router 的 `$route` 与 Ziggy 路由的冲突，改为使用 `$ziggyRoute`
  - **应用挂载冲突修复**: 修复了内联 Vue 脚本与主应用的挂载点冲突问题
  - **架构简化**: 将复杂的 SPA 架构简化为传统 Blade 模板 + Vue 增强模式
  - **组件依赖清理**: 移除了缺失的 Vue 组件依赖，确保构建成功
  - **全局变量统一**: 统一了 `window.vueApp` 全局变量供页面脚本使用
  - **多挂载点支持**: 各应用支持自动检测挂载点 (`#admin-app`, `#user-app`, `#tutor-app`, `#auth-app` 或默认 `#app`)
- 🛠️ **路由注册优化** - 修复 Vue 应用中的 `$ziggyRoute` 全局属性，使用正确的 Ziggy 路由函数
  - 更新 `app.js`, `admin.js`, `user.js`, `tutor.js`, `auth.js` 文件
  - 正确导入 `route` 函数从 `ziggy-js` 包
  - 修复全局属性注册，避免与 Vue Router 冲突
  - 确保在 Vue 组件中可以正确使用 `this.$ziggyRoute('路由名称', 参数)`
- 🎨 **界面优化** - 管理员页面列表现在与头部padding保持一致，使用px-6布局
- 🗑️ **完全移除页脚** - 按用户要求完全移除页脚，提供更简洁的界面体验
- 📱 **布局统一** - 所有管理页面现在使用一致的padding和间距
- 🔧 **对齐修复** - 重构头部结构，确保面包屑导航与页面内容完美对齐
- 📐 **响应式优化** - 手机菜单按钮不影响桌面端的布局对齐

### 核心功能

- 🗺️ 地图功能 (CenterMap 组件)
- 🎛️ 筛选管理 (FilterManager 组件)
- 📱 响应式 UI 组件库
- 🎨 主题切换
- 📊 图表展示 (ApexCharts)
- 🔔 通知系统

### 已集成的依赖

- **SweetAlert2** - 弹窗提示
- **Lodash** - 工具函数库
- **Axios** - HTTP 请求
- **@vueuse/motion** - 动画效果
- **Moment.js** - 时间处理
- **jenssegers/agent** - 设备检测 (桌面端、平板、移动端)
- **spatie/laravel-activitylog** - 系统活动日志记录

### 开发说明

- Vue 应用入口: `resources/js/app.js`
- 组件目录: `resources/js/components/`
- 状态管理: `resources/js/stores/`
- 自定义指令: `resources/js/directives/`

### @vuedata 指令系统

项目实现了自定义的 `@vuedata` Blade 指令，用于将服务端数据无缝传递到前端 Vue 应用中。

#### 特点
- 🚀 **零配置前端** - 数据自动注入到 Vue 组件
- 🔄 **数据同步** - 服务端数据直接可用于前端
- 📦 **类型安全** - 支持对象、数组、枚举等多种数据类型
- 🎯 **简单易用** - 只需要在 Blade 中定义，Vue 中直接使用

#### 使用方法

1. **在 Blade 文件中定义数据**：
```php
@vuedata([
    'current_status' => $status ?? 'all',
    'userModal' => false,
    'filters' => (object) ['status' => []],
    'statusColors' => \App\Enums\Status::CLASSNAME,
])
```

2. **在 Vue 模板中直接使用**：
```html
<div v-if="userModal">模态框内容</div>
<span>{{ current_status }}</span>
<select v-model="current_status">...</select>
```

#### 技术实现
- **后端**: `AppServiceProvider` 中注册 `@vuedata` 指令
- **前端**: `app.js` 自动将数据注入到 Vue 根组件
- **传输**: 通过 `js-variable.blade.php` 输出到 `window.app.vuedata`

#### 已应用页面
- 👥 用户管理页面 (`admin/users/index.blade.php`)
- 🔑 角色管理页面 (`admin/roles/index.blade.php`) 
- 📋 活动日志页面 (`admin/logs/index.blade.php`)
- 🏷️ 状态组件 (`components/listing-status.blade.php`)

### Maxxidev Laravel 开发包

项目已集成 Maxxidev 开发包，提供以下功能：

- 🚀 **API 调度器** - 统一的 HTTP 客户端调度系统
- 📦 **数据仓库模式** - 标准化的数据访问层
- 🎯 **RESTful 控制器** - 快速构建 REST API
- 👤 **作者追踪** - 自动记录数据创建者和更新者
- ⚙️ **默认值管理** - 集中管理应用默认配置
- 🛠️ **30+ 辅助函数** - 涵盖调试、日期、字符串、数字、URL、UI 等

#### 可用的门面

```php
use Api;          // HTTP 客户端调用
use Defaults;     // 默认值获取
```

#### 可用的辅助函数

```php
pr($data);        // 调试输出
vd($data);        // 转储输出
log_content();    // 日志记录
format_date();    // 日期格式化
start_case();     // 字符串格式化
// 等 30+ 个实用函数
```

### 认证系统

项目已集成完整的认证系统，支持：

- 🔐 **用户登录/退出** - 安全的身份验证
- 🔑 **忘记密码** - 邮件重置密码功能
- 👥 **角色权限管理** - 基于 Spatie Permission
- 🛡️ **访问控制** - 中间件保护路由

#### 角色域识别系统 (`{{ app('domain') }}`)

项目实现了基于路由的角色域识别系统，通过 Laravel 服务容器自动识别当前用户角色：

**核心机制**
- 通过 `RegisterGlobal` 中间件注册当前用户角色到服务容器
- 支持在 Blade 模板中使用 `{{ app('domain') }}` 获取当前角色
- 前端可通过 `window.app.domain` 访问角色信息

**支持的角色域**
- `user` - 普通用户/客户 (路由前缀: `/user/*`)
- `admin` - 管理员 (路由前缀: `/admin/*`)  
- `tutor` - 导师 (路由前缀: `/tutor/*`)

**使用场景**
- 动态路由生成：`route(app('domain') . '.dashboard')`
- 视图模板选择：`@if(app('domain') === 'admin')`
- API路径生成：`/api/{{ app('domain') }}/private/users`
- 前端条件渲染：`window.app.domain`

**实现文件**
- 中间件：`app/Http/Middleware/RegisterGlobal.php`
- 路由配置：`routes/web.php` (按角色分组)
- 默认值：`app/Providers/AppServiceProvider.php`
- 前端变量：`resources/views/layout/js-variable.blade.php`

#### 管理员账户

- **超级管理员**: `superadmin@example.com` / `password123`
- **普通管理员**: `admin@example.com` / `password123`

#### 角色权限管理

- **超级管理员**: 拥有所有系统权限
- **普通管理员**: 拥有内容管理、报告查看等权限

#### 角色管理功能

- 🎭 **角色列表** - 查看所有系统角色
- ➕ **创建角色** - 添加新的用户角色
- ✏️ **编辑角色** - 修改角色信息和权限
- 🗑️ **删除角色** - 删除不需要的角色（保护系统角色）
- 🔍 **搜索角色** - 按角色名称或描述搜索
- 📊 **权限统计** - 显示角色权限和用户分配情况
- 🛡️ **权限分配** - 为角色分配或移除权限

#### 可用权限

- `view-dashboard` - 查看仪表板
- `manage-users` - 管理用户
- `manage-roles` - 管理角色
- `manage-permissions` - 管理权限
- `manage-system` - 系统管理
- `view-reports` - 查看报告
- `manage-content` - 内容管理
- `access-logs` - 访问日志

#### 路由保护

- `/` 和 `/index.php` 自动重定向到登录页
- 未登录用户无法访问受保护的路由
- 登录后默认跳转到仪表板

### 活动日志系统

项目集成了 Spatie Activity Log 包，提供完整的系统活动追踪功能：

#### 活动日志功能

- 📊 **日志列表** - 查看所有系统活动记录
- 🔍 **智能搜索** - 按描述、用户、日志名称搜索
- 🕐 **时间过滤** - 按今天、本周、本月过滤
- 🏷️ **分类标签** - 按日志类型和事件类型分类显示
- 👤 **用户追踪** - 显示操作用户信息和头像
- 📝 **详细属性** - 展开查看操作的详细属性
- 🗑️ **日志清理** - 删除单个日志或批量清理旧日志
- 📈 **统计面板** - 显示总数、今日、本周、本月统计

#### 自动记录的活动

- **用户操作** - 创建、更新、删除用户
- **登录活动** - 用户登录、退出记录
- **系统维护** - 系统级操作记录
- **权限变更** - 角色和权限分配记录

#### 配置的模型

- **User 模型** - 自动记录用户字段变更
- **Role 模型** - 角色管理操作记录
- **其他模型** - 可扩展到任何需要追踪的模型

#### 访问路径

- **活动日志列表**：`/admin/logs`
- **日志详情**：`/admin/logs/{id}`
- **清理旧日志**：`/admin/logs/clear-old`
- **清理所有日志**：`/admin/logs/clear-all`

### 数据库迁移

项目包含以下数据库迁移文件：

- **用户相关迁移**

  - `create_users_table.php`