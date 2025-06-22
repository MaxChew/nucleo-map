# @vuedata 指令使用指南

## 概述
`@vuedata` 是系统中自定义的 Blade 指令，用于将服务端 PHP 数据无缝传递到前端 Vue.js 应用中。

## 工作原理
1. **服务端定义**: 在 Blade 文件中使用 `@vuedata([...])`
2. **数据合并**: 后端将数据合并到全局 `app('vuedata')` 实例
3. **前端注入**: 通过 `js-variable.blade.php` 自动输出到 `window.app.vuedata`
4. **Vue 自动使用**: Vue 应用自动将数据注入到组件实例中

## 使用方法

### 1. 在 Blade 文件中定义数据
```php
@vuedata([
    'filters' => (object) [
        'status' => [],
    ],
    'current_status' => $status ?? 'all',
    'current_type' => 'all',
    'userModal' => false,
    'statusColors' => \App\Enums\Status::CLASSNAME,
])
```

### 2. 在 Vue 组件中直接使用
```vue
<template>
    <div v-if="userModal">显示模态框</div>
    <div>当前状态: {{ current_status }}</div>
    <select v-model="current_type">...</select>
</template>

<script>
export default {
    // 不需要在这里声明任何数据
    // current_status, userModal 等都可以直接使用
}
</script>
```

### 3. 在 Blade 模板中使用 Vue 语法
```html
<input v-model="current_status">
<div v-show="userModal" class="modal">...</div>
<div :class="statusColors[row.status]">...</div>
```

## 支持的数据类型
```php
@vuedata([
    'string_data' => 'hello world',
    'number_data' => 123,
    'boolean_data' => true,
    'array_data' => ['a', 'b', 'c'],
    'object_data' => (object) ['key' => 'value'],
    'model_data' => $user,
    'enum_data' => App\Enums\Status::getMap(),
    'conditional_data' => $status ?? 'default',
])
```

## 最佳实践
1. **数据命名**: 使用清晰易懂的变量名
2. **默认值**: 使用 `??` 操作符提供默认值
3. **对象转换**: 对于需要作为对象使用的数组，使用 `(object)` 转换
4. **数据量控制**: 只传递必要的数据，避免传递大量数据
5. **敏感数据**: 不要通过此方式传递敏感信息

## 前端配置
前端应用**无需任何配置**：
- ❌ 不需要 import 任何模块
- ❌ 不需要在组件中声明 props 或 data
- ❌ 不需要配置状态管理
- ❌ 不需要 API 调用获取初始数据
- ✅ 直接在组件中使用数据即可

## 常见使用场景
1. **列表页面过滤器**: 传递过滤状态和选项
2. **模态框控制**: 传递显示/隐藏状态
3. **枚举数据**: 传递下拉选项和状态映射
4. **用户权限**: 传递当前用户角色和权限
5. **表单初始值**: 传递编辑表单的初始数据

## 注意事项
- 数据会被序列化为 JSON，不能包含 PHP 资源类型
- 大量数据建议通过 API 异步加载
- 数据在页面加载时就已固定，动态数据需要通过 API 更新

## 技术实现细节

### 后端实现 (AppServiceProvider.php)
```php
// 注册 vuedata 实例
app()->instance('vuedata', []);

// 定义 Blade 指令
Blade::directive('vuedata', function ($expression) {
    return "<?php app()->instance('vuedata', array_merge(app('vuedata'), $expression)) ?>";
});
```

### 前端自动注入 (app.js)
```javascript
// 从全局变量获取数据
const vueData = window.app.vuedata || {};

const app = createApp({
    data() {
        return {
            user: window.app.user || {},
            ...vueData,  // 所有 @vuedata 的数据都自动展开到这里
        };
    },
});
```

### 数据传输桥梁 (js-variable.blade.php)
```javascript
window.app = {
    vuedata: <?php echo json_encode(app('vuedata')); ?>,
    // 其他全局数据...
}
```

## 使用示例

### 示例 1: 用户列表页面
```php
@vuedata([
    'filters' => (object) [
        'status' => [],
    ],
    'current_status' => $status ?? 'all',
    'current_type' => 'all',
    'userModal' => false,
])
```

在模板中使用：
```html
<div class="v-cloak--hidden flex flex-col gap-6">
    <h1>All Admins</h1>
    
    <!-- 直接使用 vuedata 中的数据 -->
    <div v-if="userModal" class="modal">
        用户模态框内容
    </div>
    
    <!-- 在组件属性中使用 -->
    <listing-div
        :params="{ filters: filterParams, status: current_status, type: current_type }"
        :url="'{{ passport_url('admin/private/users?page=1') }}'"
    />
</div>
```

### 示例 2: 仪表板
```php
@vuedata([
    'current_status' => "all",
    'invoice_current_status' => "pending",
    'filters' => (object) [],
    'showPassword' => false,
])
```

### 示例 3: 状态组件
```php
@vuedata([
    'statusColors' => \App\Enums\Status::CLASSNAME,
])
```

在组件中使用：
```html
<div class="text-xs text-center rounded-lg w-4/5 p-2" 
     :class="['bg-' + (statusColors[row.status] || 'gray-500')]">
    <span class="!text-white">{{ row.status_label || row.status }}</span>
</div>
```

## 给初级开发者的快速指南

### ✅ 只需要做这两步：

1. **在 Blade 文件顶部定义数据**：
```php
@vuedata([
    'userModal' => false,
    'current_status' => 'all'
])
```

2. **在 Vue 组件中直接使用**：
```vue
<template>
    <div v-if="userModal">模态框内容</div>
    <span>{{ current_status }}</span>
</template>
```

### ❌ 完全不需要：
- ❌ import 任何东西
- ❌ 在 `data()` 中声明
- ❌ 写 props
- ❌ API 调用初始数据
- ❌ 配置状态管理

### 🎯 核心优势：
- **零配置**: 前端完全不需要任何设置
- **自动注入**: 数据自动可用于所有组件
- **类型安全**: 支持所有常见数据类型
- **简单易用**: 学习成本极低

就这么简单！现在你可以专注于业务逻辑，而不用担心数据传递的复杂性。

## $route 路由助手使用指南

### 概述
`$route` 是基于 Laravel Ziggy 包的路由助手函数，允许你在 Vue 组件中直接使用 Laravel 的命名路由。

### 注册原理
系统自动将 `$route` 函数注册为 Vue 全局属性：

```javascript
// app.js 中的注册
app.config.globalProperties.$route = route;
```

### 基本使用方法

#### 1. 在 Vue 模板中使用
```vue
<template>
    <!-- 简单路由 -->
    <a :href="$route('admin.dashboard.index')">Dashboard</a>
    
    <!-- 带参数的路由 -->
    <a :href="$route('admin.users.show', { user: userId })">查看用户</a>
    
    <!-- 带多个参数的路由 -->
    <a :href="$route('admin.students.courses.index', { object: studentId, page: 1 })">学生课程</a>
    
    <!-- 退出链接 -->
    <a :href="$route('auth.logout')">退出登录</a>
</template>
```

#### 2. 在 Vue 脚本中使用
```vue
<script>
export default {
    methods: {
        navigateToUser(userId) {
            const url = this.$route('admin.users.show', { user: userId });
            window.location.href = url;
        },
        
        redirectToDashboard() {
            window.location.href = this.$route('admin.dashboard.index');
        },
        
        generateApiUrl(objectId) {
            return this.$route('api.admin.courses.show', { object: objectId });
        }
    }
}
</script>
```

### 支持的路由类型

#### 1. Web 路由
```javascript
// 管理员路由
$route('admin.dashboard.index')          // /admin/dashboard
$route('admin.users.index')              // /admin/users
$route('admin.users.show', { user: 1 })  // /admin/users/1

// 导师路由
$route('tutor.dashboard.index')          // /tutor/dashboard
$route('tutor.courses.index')            // /tutor/courses

// 用户路由
$route('user.dashboard.index')           // /user/dashboard
$route('user.students.show', { object: 1 }) // /user/students/1

// 认证路由
$route('auth.login')                     // /auth/login
$route('auth.logout')                    // /auth/logout
```

#### 2. API 路由
```javascript
// API 路由也可以使用
$route('api.admin.users.index')          // /api/admin/private/users
$route('api.admin.users.show', { object: 1 }) // /api/admin/private/users/1
$route('api.tutor.courses.summary')      // /api/tutor/private/courses/summary
```

### 路由参数使用

#### 1. 单个参数
```vue
<template>
    <a :href="$route('admin.users.show', { user: currentUser.id })">
        查看用户资料
    </a>
</template>
```

#### 2. 多个参数
```vue
<template>
    <a :href="$route('admin.students.courses.index', { 
        object: student.id, 
        page: currentPage 
    })">
        查看学生课程
    </a>
</template>
```

#### 3. 可选参数
```vue
<template>
    <!-- 月份参数是可选的 -->
    <a :href="$route('admin.schedules.month', { month: selectedMonth || '' })">
        查看课程表
    </a>
</template>
```

### 实际应用示例

#### 示例 1: 侧边栏导航
```vue
<template>
    <nav class="sidebar">
        <ul>
            <li><a :href="$route('admin.dashboard.index')">仪表盘</a></li>
            <li><a :href="$route('admin.users.index')">用户管理</a></li>
            <li><a :href="$route('admin.courses.index')">课程管理</a></li>
            <li><a :href="$route('admin.students.index')">学生管理</a></li>
        </ul>
    </nav>
</template>
```

#### 示例 2: 数据表格操作
```vue
<template>
    <table>
        <tr v-for="user in users" :key="user.id">
            <td>{{ user.name }}</td>
            <td>
                <a :href="$route('admin.users.show', { user: user.id })" 
                   class="btn btn-info">查看</a>
                <a :href="$route('admin.users.edit', { user: user.id })" 
                   class="btn btn-warning">编辑</a>
            </td>
        </tr>
    </table>
</template>
```

#### 示例 3: 动态路由生成
```vue
<script>
export default {
    data() {
        return {
            breadcrumbs: [
                { title: 'Dashboard', url: this.$route('admin.dashboard.index') },
                { title: 'Users', url: this.$route('admin.users.index') },
                { title: 'User Detail', url: null }
            ]
        }
    },
    
    methods: {
        getUserEditUrl(userId) {
            return this.$route('admin.users.edit', { user: userId });
        },
        
        getApiEndpoint(endpoint, params = {}) {
            return this.$route(`api.admin.${endpoint}`, params);
        }
    }
}
</script>
```

### 路由命名规范

系统中的路由命名遵循以下规范：

#### Web 路由
- `{role}.{resource}.{action}` 格式
- 例如：`admin.users.index`、`tutor.courses.show`、`user.students.edit`

#### API 路由
- `api.{role}.{resource}.{action}` 格式
- 例如：`api.admin.users.index`、`api.tutor.courses.summary`

#### 常用路由命名
```javascript
// 基础 CRUD 操作
.index    // 列表页
.show     // 详情页  
.create   // 创建页
.edit     // 编辑页
.store    // 保存数据 (POST)
.update   // 更新数据 (PUT/PATCH)
.destroy  // 删除数据 (DELETE)

// 特殊操作
.summary  // 摘要数据
.active   // 激活/禁用
.approve  // 审批
.reject   // 拒绝
```

### 最佳实践

1. **使用命名路由**: 始终使用命名路由而不是硬编码 URL
```vue
<!-- ✅ 推荐 -->
<a :href="$route('admin.users.show', { user: userId })">查看用户</a>

<!-- ❌ 不推荐 -->
<a :href="`/admin/users/${userId}`">查看用户</a>
```

2. **参数验证**: 确保传递的参数存在且有效
```vue
<template>
    <a v-if="user.id" :href="$route('admin.users.show', { user: user.id })">
        查看用户
    </a>
</template>
```

3. **错误处理**: 路由不存在时的容错处理
```javascript
methods: {
    safeRoute(routeName, params = {}) {
        try {
            return this.$route(routeName, params);
        } catch (error) {
            console.warn(`Route ${routeName} not found:`, error);
            return '#';
        }
    }
}
```

4. **缓存路由**: 对于频繁使用的路由可以进行缓存
```javascript
computed: {
    dashboardUrl() {
        return this.$route('admin.dashboard.index');
    },
    
    userIndexUrl() {
        return this.$route('admin.users.index');
    }
}
```

### 技术实现细节

#### 1. Ziggy 配置
系统使用 `tightenco/ziggy` 包来生成前端路由：
```javascript
// resources/js/ziggy.js 自动生成
// 包含所有 Laravel 路由的 JavaScript 映射
```

#### 2. 路由生成
```bash
# 开发环境自动生成路由
npm run dev

# 生产环境手动生成
php artisan ziggy:generate resources/js/ziggy.js
```

#### 3. 基础 URL 配置
```javascript
// app.js 中的配置
Ziggy.baseUrl = process.env.APP_URL || 'https://simira.maxxi-dev.com';
```

### 注意事项

1. **路由存在性**: 确保使用的路由在 Laravel 中已定义
2. **参数匹配**: 参数名称必须与路由定义中的参数名称一致
3. **权限控制**: `$route` 只生成 URL，不处理权限验证
4. **生产环境**: 确保在部署时正确生成了 `ziggy.js` 文件

### 给初级开发者的快速指南

#### ✅ 只需要记住：

1. **基础用法**：
```vue
<a :href="$route('路由名称')">链接文字</a>
```

2. **带参数用法**：
```vue
<a :href="$route('路由名称', { 参数名: 参数值 })">链接文字</a>
```

3. **常用路由格式**：
- 管理员：`admin.资源.操作`
- 导师：`tutor.资源.操作`
- 用户：`user.资源.操作`
- API：`api.角色.资源.操作`

#### ❌ 完全不需要：
- ❌ 手写 URL 路径
- ❌ 担心路径变更
- ❌ 记住复杂的 URL 结构
- ❌ 处理参数拼接

现在你可以轻松地在 Vue 组件中使用 Laravel 路由了！