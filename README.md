
# nucleo-map

一个专为核医学中心打造的互动式地图平台，结合 Google Maps API、多层筛选系统与 Laravel CMS 后台管理系统。

---

## 🗺️ 项目简介

**nucleo-map** 是一款整合医疗中心资讯的地图平台，前端使用 Vue 3 + Tailwind CSS，后端基于 Laravel 框架，支持用户互动式筛选与地图导航，广泛应用于核医学领域。

---

## ✨ 功能特色

- 📍 **互动式地图展示**
  - 显示各中心位置信息
  - 点击标记即可查看详细资料

- 🔍 **多维筛选系统（共11项）**
  - 服务、疾病类型、放射药物、研究、培训、合作、医生等

- 🧰 **后台内容管理系统**
  - 可由管理员操作新增 / 编辑 / 删除数据

- 📦 **初始数据导入支持**
  - 提供导入前 40 笔资料
  - 完整使用教学培训

---

## ⚙️ 技术栈

### 🧑‍💻 后端技术

- **Laravel 10.x**
- `spatie/laravel-activitylog` v4.8 - 活动记录
- `simplesoftwareio/simple-qrcode` v4.2 - 二维码生成
- `tightenco/ziggy` v1.8 - Laravel 路由桥接 Vue

### 💻 前端技术

- **Vue.js 3.2.37**
- **Vue Router 4.0**
- **Pinia 2.2** - 状态管理
- **Vite 5.0** - 构建工具
- **ApexCharts + vue3-apexcharts 1.6.2**
- **SweetAlert2 11.10**
- **Swiper 11.x**
- **Quill Editor 2.x**
- **GSAP 3.12**
- **VueUse + Vue Motion**
- **Three.js r165**
- **Axios, Lodash, Moment.js**

### 🗺️ 地图与位置服务

- **Google Maps API**
- `stevebauman/location` v8.1 - IP 地理定位
- 地址管理（GPS 坐标、Google Places 自动补全）
- 筛选 + 地址验证 + 坐标提取功能

### 🧱 数据库与缓存

- **MySQL 8 / MariaDB 10.x**
- **Redis 7.x**（使用 Predis）
- Laravel 原生 Migration / Seeder 系统

### 🎨 样式框架

- **Tailwind CSS 3.4.4**（主要 UI 样式）
- **Bootstrap 5.2.3**（兼容部分组件）
- **Sass / SCSS** + **PostCSS 8.x**

## 项目结构

nucleo-map/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── Admin/                # ✅ 管理员 API 控制器
│   │   ├── Middleware/                   # 中间件
│   │   ├── Requests/                     # 请求验证
│   │   └── Resources/                    # API 资源转换器
│   ├── Models/                           # 数据模型
│   │   ├── Center.php                    # 中心资料模型
│   │   ├── FilterOption.php              # 筛选选项模型（如疾病类型等）
│   │   └── ...
│   ├── Services/                         # 业务逻辑（可选）
│   └── Enums/                            # 枚举类（如筛选类型）
├── bootstrap/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── location.php                      # IP/地址配置
│   └── ...
├── database/
│   ├── migrations/
│   │   ├── create_centers_table.php
│   │   ├── create_filter_options_table.php
│   │   └── ...
│   ├── seeders/
│   └── factories/
├── lang/
├── public/
│   ├── build/                            # Vite 编译输出
│   └── assets/                           # 图标、图片等
├── resources/
│   ├── js/
│   │   ├── components/                   # Vue 组件
│   │   │   ├── CenterForm.vue
│   │   │   ├── CenterMap.vue
│   │   │   ├── FilterManager.vue
│   │   │   └── ...
│   │   ├── stores/                       # Pinia 状态管理
│   │   ├── plugins/                      # 插件（如 ziggy）
│   │   ├── libs/                         # 工具函数
│   │   ├── admin.js                      # Admin 主入口
│   │   ├── bootstrap.js
│   │   └── ziggy.js
│   ├── css/
│   └── views/
│       └── admin.blade.php               # Admin 单页入口
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
│   ├── app/
│   ├── logs/
│   └── framework/
├── tests/
│   ├── Feature/
│   └── Unit/
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
├── .env.example
├── .gitignore
├── composer.json
├── package.json
├── README.md
└── artisan

---

## 🚀 安装步骤

```bash
git clone https://github.com/MaxChew/nucleo-map.git
cd nucleo-map
composer install
cp .env.example .env
php artisan key:generate
```

配置 `.env` 后：

```bash
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

---

## 📌 Google Maps API 限额说明

| 月访问次数        | 收费（由 Google 决定） |
|-------------------|--------------------------|
| 0 – 20,000        | ✅ 免费（Max 承担）     |
| 20,001 – 30,000   | 💵 USD 150               |
| 30,001 – 40,000   | 💵 USD 220               |
| 40,001 – 50,000   | 💵 USD 290               |

> ⚠️ 若超出免费额度，额外费用由客户承担。

---

## 🔒 系统授权与维护

- **开发方**：Maxxi Dev Enterprise  
- **客户数据维护权**：MSNMMI  
- **维护费用**：RM 1,200 / 年（固定五年）

---

## 📞 联系我们

📧 **maxxi.dev@gmail.com**  
🔗 项目代号：`nucleo-map`

---

© 2025 Maxxi Dev Enterprise. All rights reserved.
