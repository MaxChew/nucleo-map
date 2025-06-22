# Maxxidev Laravel 开发包

这是一个为 Laravel 应用程序提供常用功能和工具的自定义开发包。该包包含了 API 调度器、数据仓库模式、RESTful 控制器、作者追踪、默认值管理等功能。

## 📋 前置要求

在开始使用 Maxxidev 开发包之前，请确保你的环境满足以下要求：

- **PHP** >= 8.1
- **Laravel** >= 10.0
- **Composer** 包管理器
- **基础 Laravel 知识**：
  - Service Provider（服务提供者）
  - Facade（门面）
  - Eloquent ORM
  - Artisan 命令

## 功能特性

### 🚀 核心功能
- **API 调度器** - 统一的 API 客户端调度系统
- **数据仓库模式** - 标准化的数据访问层
- **RESTful 控制器** - 快速构建 REST API
- **作者追踪** - 自动记录数据创建者和更新者
- **默认值管理** - 集中管理应用默认配置
- **辅助函数** - 30+ 个实用辅助函数，涵盖调试、日期、字符串、数字、URL、UI 等功能

### 📦 包含组件
- **ApiDispatcher** - HTTP 客户端调度器
- **Repositories** - 数据仓库和查询条件
- **Http/Controllers** - RESTful 控制器基类
- **Author** - 作者追踪系统
- **Support** - 支持工具和默认值管理
- **Notifications** - 自定义通知渠道
- **Rules** - 数据验证规则
- **MediaCustomPath** - 媒体文件路径管理

## 🛠 安装配置

### 1. 复制文件
将整个 `maxxidev` 文件夹复制到你的 Laravel 项目根目录下。

### 2. 配置 Composer 自动加载
在 `composer.json` 中添加：

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Maxxidev\\": "maxxidev/src/"
        },
        "files": [
            "maxxidev/helpers.php"
        ]
    }
}
```

### 3. 注册服务提供者
在 `bootstrap/providers.php` 中添加：

```php
<?php

return [
    // ... 其他服务提供者
    Maxxidev\Author\ServiceProvider::class,
    Maxxidev\PointlessServiceProvider::class,
];
```

### 4. 配置门面别名
在 `config/app.php` 中添加：

```php
'aliases' => Facade::defaultAliases()->merge([
    'Api' => Maxxidev\ApiDispatcher\Facades\Api::class,
    'Defaults' => Maxxidev\Support\Defaults::class,
    // ... 其他别名
])->toArray(),
```

### 5. 更新自动加载
运行命令：
```bash
composer dump-autoload
```

## 🗄️ 数据库迁移

### UserLoginActivity 表
该包包含用户登录活动追踪功能，需要运行相应的迁移文件：

```bash
# 运行迁移
php artisan migrate
```

**迁移文件位置**：
- `maxxidev/migrations/2024_12_27_000001_create_user_login_activities_table.php`
- `database/migrations/2024_12_27_000001_create_user_login_activities_table.php`

**表结构**：
```php
Schema::create('user_login_activities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('last_ip')->nullable()->comment('最后登录IP地址');
    $table->date('curdate')->nullable()->comment('当前日期');
    $table->decimal('lat', 10, 8)->nullable()->comment('纬度');
    $table->decimal('long', 11, 8)->nullable()->comment('经度');
    $table->string('user_agent')->nullable()->comment('用户代理');
    $table->string('device')->nullable()->comment('设备类型');
    $table->string('browser')->nullable()->comment('浏览器');
    $table->string('platform')->nullable()->comment('操作系统');
    $table->timestamp('last_activity')->nullable()->comment('最后活动时间');
    $table->timestamps();
});
```

### 模型使用示例
```php
use App\Models\UserLoginActivity;

// 根据IP和日期查询
$activity = UserLoginActivity::getByIpAndDate('127.0.0.1', '2024-12-27');

// 创建或更新记录
$activity = UserLoginActivity::createOrUpdate([
    'user_id' => 1,
    'last_ip' => '127.0.0.1',
    'curdate' => now()->format('Y-m-d'),
    'lat' => 40.7128,
    'long' => -74.0060,
    'user_agent' => request()->userAgent(),
]);
```

## 📝 使用指南

### API 调度器使用

```php
// 在控制器或服务类中使用
use Api;

// GET 请求
$response = Api::get('https://api.example.com/users');
$data = $response->json();

// POST 请求
$response = Api::post('https://api.example.com/users', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// 其他 HTTP 方法
Api::patch($url, $data);
Api::put($url, $data);
Api::destroy($url);
```

### RESTful 控制器使用

```php
<?php

namespace App\Http\Controllers\Api;

use Maxxidev\Http\Controllers\Restful\Controller as RestfulController;
use Maxxidev\Http\Controllers\Restful\RequestParsers\QueryStringToEloquent;
use Maxxidev\Repositories\EloquentRepository;

class UserController extends RestfulController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new EloquentRepository(new User);
        $this->requestParser = new QueryStringToEloquent;
    }

    // 自动获得 index, show, store, update, destroy 方法
}
```

### 数据仓库模式使用

```php
<?php

use Maxxidev\Repositories\EloquentRepository;

class UserService
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new EloquentRepository(new User);
    }

    public function getActiveUsers()
    {
        return $this->userRepository
            ->where('status', 'active')
            ->get();
    }
}
```

### 作者追踪使用

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maxxidev\Author\Traits\CaptureAuthors;

class Post extends Model
{
    use CaptureAuthors;

    // 模型会自动记录 created_by 和 updated_by
}
```

### 默认值管理使用

```php
<?php

use Defaults;

// 获取默认国家
$country = Defaults::country();

// 获取国家列表
$countries = Defaults::countryList();

// 获取学生列表
$students = Defaults::studentList();

// 获取客户列表
$clients = Defaults::clientList();
```

### 辅助函数使用

#### 📊 调试与日志函数
```php
// 调试输出
pr($data); // 格式化打印
vd($data); // var_dump 输出
prd($data); // 打印后停止执行
vdd($data); // 转储后停止执行

// 日志记录
log_content('custom.log', '日志内容', ['extra' => 'data']);

// 数据库查询监听
dblisten('query.log'); // 记录到文件
dblisten(); // 直接输出
```

#### 📅 日期时间函数
```php
// 日期格式化
$formatted = format_date('2024-01-01', 'Y-m-d');
$datetime = format_datetime('2024-01-01 12:30:00', 'j M Y H:i');

// Carbon 实例
$date = carbon('2024-01-01');

// 日期范围
$dates = carbon_range('2024-01-01', '2024-01-31');

// 时间下拉选项
$times = dropdown_hour_min(true, 9, 17, 30);
$hours = dropdown_hour(true);
$minutes = dropdown_min();
$years = dropdown_year(10); // 最近10年
```

#### 🔤 字符串处理函数
```php
// 大小写转换
$title = start_case('hello_world'); // "Hello World"
$upper = upper_case('hello_world');  // "HELLO WORLD"
$lower = lower_case('HELLO_WORLD');  // "hello world"
$ucfirst = uclower('HELLO WORLD');   // "Hello world"

// 字符串处理
$array = explode_filter(',', 'a,b,,c,'); // ['a', 'b', 'c']
$clean = cleanSpecialChars('Hello@#$World!'); // "HelloWorld"
```

#### 🔢 数字格式化函数
```php
// 数字格式化
$ordinal = number_ordinal(21); // "21st"
$knotation = format_number_in_k_notation(1500); // "1K+"
$original = unformat_number('1K+'); // 1500

// 货币处理
$currency = currency('USD', 1.2);
```

#### 🌐 URL 和网络函数
```php
// URL 生成
$url = url_params('https://api.com', ['key' => 'value']);
$consumer = consumer_url('/api/users');
$external = external_url('https://api.com', '/users');

// IP 地址
$ip = get_ip(); // 获取客户端 IP

// 地图坐标提取
$coords = extractLongLatFromGoogleMapsUrl($googleMapUrl);
```

#### 🎨 样式和 UI 函数
```php
// 颜色转换
$rgba = hex_to_rgba('#FF0000', 0.5); // "rgba(255, 0, 0, 0.5)"

// HTML 属性
$attrs = htmlattributes(['class' => 'btn', 'id' => 'submit']);

// 面包屑导航
$breadcrumb = admin_breadcrumb('用户管理', '/admin/users', 'index');
```

#### 🔧 数据处理函数
```php
// 数据转换
$array = object_to_array($object);

// Trait 检查
$usesTrait = class_uses_trait(User::class, 'SoftDeletes');

// 会话管理
set_intended('/dashboard');
```

#### 📈 统计和状态函数
```php
// 状态统计
$progress = getProgressByStatus('orders', 'completed');
$counts = getStatusCounts($query, ['active', 'inactive']);
$activeCounts = getUserActiveCounts($userQuery);
$years = getYearsList('App\Models\Order');
```

## 🔧 配置文件

创建 `config/defaults.php` 配置文件：

```php
<?php

return [
    'app_name' => env('APP_NAME', 'Your App'),
    'country' => env('DEFAULT_COUNTRY', 'MY'),
    'state' => env('DEFAULT_STATE', 'Kuala Lumpur'),
    'timezone' => env('ADMIN_TIMEZONE', 'Asia/Kuala_Lumpur'),
    'currency' => env('DEFAULT_CURRENCY', 'MYR'),
    'currency_display' => env('DEFAULT_CURRENCY_DISPLAY', 'RM'),
    
    'api' => [
        'base_uri' => env('API_BASE_URI'),
        'client_id' => env('API_CLIENT_ID'),
        'client_secret' => env('API_CLIENT_SECRET'),
    ],
];
```

## 📊 数据库迁移

如果使用作者追踪功能，需要在数据库迁移中添加：

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthorColumnsToTable extends Migration
{
    public function up()
    {
        Schema::table('your_table', function (Blueprint $table) {
            $table->author(); // 添加 created_by 和 updated_by 字段
        });
    }
}
```

## 🎯 高级用法

### 自定义查询条件

```php
<?php

use Maxxidev\Repositories\Criteria\GenericEloquentFilter;
use Maxxidev\Repositories\Criteria\GenericEloquentSort;

$repository = new EloquentRepository(new User);

// 添加筛选条件
$repository->pushCriteria(new GenericEloquentFilter([
    'status' => 'active',
    'role' => 'admin'
]));

// 添加排序条件
$repository->pushCriteria(new GenericEloquentSort([
    'created_at' => 'desc'
]));

$users = $repository->get();
```

### API 客户端配置

```php
<?php

use Maxxidev\ApiDispatcher\GuzzleApi\Client;

$client = new Client([
    'base_uri' => 'https://api.example.com',
    'timeout' => 30,
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ]
]);

Api::config($client);
```

## 🔍 故障排除

### 常见问题

1. **找不到 Maxxidev 类**
   - 确保运行了 `composer dump-autoload`
   - 检查 `composer.json` 中的 autoload 配置

2. **门面不工作**
   - 确保在 `config/app.php` 中正确配置了别名
   - 检查服务提供者是否正确注册

3. **辅助函数不可用**
   - 确保 `maxxidev/helpers.php` 在 `composer.json` 的 files 数组中

## 📚 更多示例

查看项目中的以下文件以获取更多使用示例：

- `app/Http/Controllers/Api/` - RESTful 控制器示例
- `app/Models/` - 作者追踪模型示例
- `app/Providers/AppServiceProvider.php` - 服务提供者配置示例

## 🤝 贡献

这是一个内部开发包，如需修改或扩展功能，请按照以下规范：

1. 保持命名空间一致性
2. 遵循 PSR-4 自动加载标准
3. 添加适当的文档注释
4. 确保向后兼容性

## 📄 许可证

内部使用，版权所有。

---

**💭 开发提示**: 
- 遇到问题时，首先检查日志文件
- 使用 `php artisan tinker` 来测试功能
- 善用 `pr()`, `vd()` 等调试函数来理解数据流

**注意**: 这个包是为特定项目定制的，复制到新项目时请根据实际需求调整配置。

## 🎓 核心概念解释

在使用 Maxxidev 开发包之前，了解以下核心概念会帮助你更好地理解和使用这个包：

### Repository 模式（数据仓库模式）
Repository 模式是一种设计模式，用于将数据访问逻辑与业务逻辑分离。它提供了一个统一的接口来访问数据，使代码更易测试和维护。

**好处：**
- 代码更易测试（可以轻松 mock）
- 业务逻辑与数据访问分离
- 可以轻松切换数据源

### Facade 门面
Laravel 的 Facade 提供了一个简单的接口来访问容器中的服务。在 Maxxidev 中，我们使用 `Api` 和 `Defaults` 门面来简化 API 调用和默认值获取。

### RESTful 控制器
RESTful 控制器遵循 REST 架构风格，提供标准的 CRUD 操作：
- `index()` - 获取资源列表
- `show($id)` - 获取单个资源
- `store()` - 创建新资源
- `update($id)` - 更新资源
- `destroy($id)` - 删除资源

## ⚠️ 常见错误及解决方案

### 错误：Class 'Api' not found
**症状**: 使用 `Api::get()` 时出现类未找到错误
```
Class 'Api' not found
```

**原因**: Facade 别名未正确配置

**解决方案**:
1. 检查 `config/app.php` 中的 `aliases` 配置
2. 确保添加了：
```php
'Api' => Maxxidev\ApiDispatcher\Facades\Api::class,
```
3. 清除配置缓存：`php artisan config:clear`

### 错误：Call to undefined function pr()
**症状**: 使用辅助函数时出现未定义函数错误
```
Call to undefined function pr()
```

**原因**: 辅助函数文件未正确加载

**解决方案**:
1. 检查 `composer.json` 中的 files 配置
2. 运行 `composer dump-autoload`
3. 确保 `maxxidev/helpers.php` 文件存在

### 错误：Class 'Maxxidev\...' not found
**症状**: 无法找到 Maxxidev 命名空间下的类

**原因**: PSR-4 自动加载配置错误

**解决方案**:
1. 检查 `composer.json` 中的 autoload 配置
2. 确保路径正确：`"Maxxidev\\": "maxxidev/src/"`
3. 运行 `composer dump-autoload`

### 错误：Service Provider 注册失败
**症状**: Laravel 启动时报错，服务提供者相关

**原因**: 服务提供者路径错误或未正确注册

**解决方案**:
1. 检查 `bootstrap/providers.php` 配置
2. 确保类路径正确
3. 运行 `php artisan config:clear`

## 💡 最佳实践

### 1. 控制器设计原则
```php
<?php
// ✅ 好的做法：保持控制器轻量
class UserController extends RestfulController
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }
    
    // 业务逻辑委托给服务层
    public function getActiveUsers()
    {
        return $this->userService->getActiveUsers();
    }
}

// ❌ 避免：在控制器中写复杂业务逻辑
class UserController extends Controller 
{
    public function getActiveUsers()
    {
        // 大量业务逻辑代码...
        // 这样会让控制器变得臃肿
    }
}
```

### 2. Repository 使用最佳实践
```php
<?php
// ✅ 推荐：在服务类中使用 Repository
class UserService
{
    protected $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new EloquentRepository(new User);
    }
    
    public function getActiveUsers($filters = [])
    {
        $query = $this->userRepository->where('status', 'active');
        
        // 应用过滤条件
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        return $query->get();
    }
}
```

### 3. API 调用异常处理
```php
<?php
// ✅ 推荐：使用 try-catch 处理 API 调用
public function fetchUserData($userId)
{
    try {
        $response = Api::get("https://api.example.com/users/{$userId}");
        
        if ($response->successful()) {
            return $response->json();
        }
        
        // 处理 HTTP 错误
        log_content('api_errors.log', 'API 调用失败', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        return null;
        
    } catch (\Exception $e) {
        // 处理网络错误等异常
        log_content('api_errors.log', 'API 异常', [
            'message' => $e->getMessage(),
            'user_id' => $userId
        ]);
        
        return null;
    }
}
```

### 4. 辅助函数使用建议
```php
<?php
// ✅ 调试时使用辅助函数
if (app()->environment('local')) {
    pr($debugData); // 只在本地环境输出调试信息
}

// ✅ 生产环境使用日志
log_content('user_actions.log', '用户登录', [
    'user_id' => auth()->id(),
    'ip' => get_ip(),
    'timestamp' => now()
]);

// ✅ 格式化显示数据
$formattedDate = format_datetime($user->created_at, 'j M Y H:i');
$displayName = start_case($user->name);
```

## 🔧 完整项目示例

以下是一个完整的用户管理功能实现示例：

### 1. 模型设置
```php
<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maxxidev\Author\Traits\CaptureAuthors;

class User extends Model
{
    use CaptureAuthors;
    
    protected $fillable = ['name', 'email', 'status'];
}
```

### 2. 控制器实现
```php
<?php
// app/Http/Controllers/Api/UserController.php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Maxxidev\Http\Controllers\Restful\Controller as RestfulController;
use Maxxidev\Repositories\EloquentRepository;

class UserController extends RestfulController
{
    public function __construct()
    {
        $this->repository = new EloquentRepository(new User);
        parent::__construct();
    }
    
    // 自动获得所有 RESTful 方法
}
```

### 3. 服务层（可选但推荐）
```php
<?php
// app/Services/UserService.php
namespace App\Services;

use App\Models\User;
use Maxxidev\Repositories\EloquentRepository;

class UserService
{
    protected $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new EloquentRepository(new User);
    }
    
    public function getActiveUsers()
    {
        return $this->userRepository
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

## ❓ 常见问题 (FAQ)

### Q: 如何在现有项目中集成 Maxxidev？
**A**: 按照安装配置章节的步骤操作，特别注意备份现有配置文件，避免覆盖重要设置。

### Q: 可以只使用部分功能吗？
**A**: 可以的。你可以只注册需要的服务提供者，或者只使用特定的辅助函数。

### Q: 如何扩展现有功能？
**A**: 继承相关类并重写方法，或者创建新的 Repository、Controller 等。保持命名空间一致性。

### Q: 性能考虑？
**A**: Repository 模式可能增加轻微开销，但带来的代码组织和测试便利性通常值得这个代价。对于高并发场景，考虑添加缓存层。

### Q: 如何测试使用了 Maxxidev 的代码？
**A**: 
```php
// 在测试中 mock Repository
$mockRepository = \Mockery::mock(EloquentRepository::class);
$mockRepository->shouldReceive('where')->andReturnSelf();
$mockRepository->shouldReceive('get')->andReturn(collect([]));

$this->app->instance(EloquentRepository::class, $mockRepository);
```

### Q: API 调用失败如何处理？
**A**: 使用 try-catch 包装 API 调用，记录错误日志，并提供合适的用户反馈。参考最佳实践中的示例。

## 🎯 下一步学习建议

1. **熟悉 Laravel 基础**：如果你是 Laravel 新手，建议先学习官方文档
2. **理解设计模式**：深入了解 Repository 模式、Facade 模式等
3. **实践项目**：用 Maxxidev 构建一个小项目来熟悉各个功能
4. **阅读源码**：查看 `maxxidev/src/` 目录下的源码来深入理解实现
