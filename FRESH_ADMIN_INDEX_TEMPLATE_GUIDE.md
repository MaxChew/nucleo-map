# FRESH Admin Index 完整開發指南

## 🎯 **概述**

本文檔整合了 Admin Index 模板的教學指南和技術標準，提供完整的開發指引。基於 `resources/views/admin/users/index.blade.php` 和 `resources/views/admin/centers/index.blade.php` 的最佳實踐，確保所有索引頁面保持一致的設計和功能。

---

## 🏗️ **標準四區域布局架構**

每個索引頁面必須包含以下四個標準區域，按順序排列：

1. **頁面頭部** (Page Header)
2. **狀態面板** (Status Board) 
3. **搜索和篩選** (Search and Filters)
4. **數據表格** (Data Table)

---

## 📝 **完整模板結構**

### **基礎框架**
```blade
@extends('admin.layout.master', [
    'title' => '頁面標題',
])

@vuedata([
    'filters' => (object) [
        'keyword' => '',
        'status' => '',
        // 根據需求添加更多篩選條件
    ],
    'current_status' => $status ?? 'all',
    'current_type' => 'all',
    // 添加其他狀態變量
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <!-- 四個標準區域 -->
    </div>
@endsection
```

---

## 🔧 **區域 1: 頁面頭部 (Page Header)**

```blade
<!-- Page Header -->
<div class="px-6">
    <div class="flex items-center justify-between">
        <h1>頁面標題</h1>
        <a href="{{ route('admin.模組.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fad fa-plus mr-2"></i>
            添加操作
        </a>
    </div>
</div>
```

### **規範要求:**
- ✅ 使用 `px-6` 水平間距
- ✅ 左側顯示頁面標題 `<h1>`
- ✅ 右側顯示主要操作按鈕（通常是添加按鈕）
- ✅ 按鈕必須使用 `bg-primary-600` 顏色
- ✅ 圖標使用 Font Awesome Duotone (`fad`)

---

## 📊 **區域 2: 狀態面板 (Status Board)**

```blade
<!-- Status Board -->
<div class="px-6">
    @include('admin.partials.status-board', [
        'summary' => $summary ?? [],
        'url' => 'admin.模組.index',
        'statusMapKey' => '模組_STATUS_MAP',
        'statusListKey' => '模組_STATUS_LIST',
        'labels' => [
            'total' => 'Total Items',
        ],
    ])
</div>
```

### **規範要求:**
- ✅ 使用 `admin.partials.status-board` 組件
- ✅ 提供統計摘要數據
- ✅ 配置正確的路由和狀態映射

**用途：** 顯示統計摘要信息，讓用戶快速了解數據概況

---

## 🔍 **區域 3: 搜索和篩選 (Search and Filters)**

```blade
<!-- Search and Filters -->
<div class="px-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">搜索與篩選</h2>
            <p class="mt-1 text-sm text-gray-600">使用以下選項篩選數據列表。</p>
        </div>
        
        <div class="px-6 py-4 space-y-6">
            <!-- 搜索欄 -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fad fa-search text-gray-400"></i>
                </div>
                <input type="text" v-model="filters.keyword" @input="filterListing" 
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       placeholder="搜索關鍵詞...">
            </div>

            <!-- 篩選選項網格 -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <!-- 狀態篩選 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">狀態篩選</label>
                    <select v-model="current_status" @change="filterListing"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="all">全部狀態</option>
                        <option value="active">激活</option>
                        <option value="inactive">停用</option>
                    </select>
                </div>

                <!-- 添加更多篩選選項... -->
            </div>

            <!-- 清除篩選按鈕 -->
            <div class="flex justify-end">
                <button @click="clearFilters" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                    <i class="fad fa-refresh mr-2"></i>
                    清除篩選
                </button>
            </div>
        </div>
    </div>
</div>
```

### **規範要求:**
- ✅ 使用標準白色卡片容器
- ✅ 頭部使用灰色背景 `bg-gray-50`
- ✅ 搜索框帶圖標，使用 `focus:ring-primary-500`
- ✅ 篩選選項使用網格布局 `grid-cols-1 gap-6 sm:grid-cols-3`
- ✅ 清除按鈕右對齊

**用途：** 提供搜索和導航功能，讓用戶快速找到所需數據

---

## 📋 **區域 4: 數據表格 (Data Table)**

```blade
<!-- Data Table -->
<div class="px-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">數據列表</h2>
            <p class="mt-1 text-sm text-gray-600">所有數據的詳細信息。</p>
        </div>
        
        <div class="overflow-hidden">
            @component('components.listing-div', [
                'rowNumber' => true,
                'columns' => Columns::load([
                    'is_active' => ['label' => 'Status', 'sortable' => false, 'align' => 'center'],
                    'name' => ['label' => 'Name', 'sortable' => true],
                    'created_at' => ['label' => 'Created At', 'sortable' => true],
                ]),
                'url' => passport_url('admin/private/模組?page=1'),
                ':params' => '{ filters:filterParams, status:current_status }',
                'initialSorts' => ['updated_at:desc'],
                'ref' => 'listing',
            ])
            
            <!-- 狀態列槽位 -->
            @slot('column:is_active')
                <div class="flex justify-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="row.is_active ? 'bg-accent-100 text-accent-800' : 'bg-warning-100 text-warning-800'"
                        v-text="row.is_active ? 'Active' : 'Inactive'">
                    </span>
                </div>
            @endslot

            <!-- 名稱列槽位 -->
            @slot('column:name')
                <div class="flex flex-col">
                    <span class="font-semibold text-primary-600" v-text="row.name"></span>
                    <span class="text-xs text-gray-500" v-if="row.description" v-text="row.description"></span>
                </div>
            @endslot

            <!-- 操作按鈕 -->
            @slot('action')
                <div class="flex items-center gap-2">
                    <a :href="$ziggyRoute('admin.模組.show', [row.id])"
                        class="text-secondary-500 hover:text-secondary-600 transition-colors" 
                        aria-label="View" data-balloon-pos="up-right">
                        <i class="fad fa-eye text-base"></i>
                    </a>

                    <a :href="$ziggyRoute('admin.模組.edit', [row.id])"
                        class="text-primary-500 hover:text-primary-600 transition-colors" 
                        aria-label="Edit" data-balloon-pos="up-right">
                        <i class="fad fa-pen text-base"></i>
                    </a>

                    <button @click="deleteItem(row.id, row.name)"
                        class="text-danger-500 hover:text-danger-600 transition-colors cursor-pointer"
                        aria-label="Delete" data-balloon-pos="up-right">
                        <i class="fad fa-trash text-base"></i>
                    </button>
                </div>
            @endslot
            @endcomponent
        </div>
    </div>
</div>
```

### **規範要求:**
- ✅ 使用 `components.listing-div` 組件
- ✅ 啟用行號 `'rowNumber' => true`
- ✅ 狀態列使用語義化顏色 (accent-綠色表示激活，warning-粉色表示停用)
- ✅ 操作按鈕使用標準顏色組合：
  - 查看: `text-secondary-500`
  - 編輯: `text-primary-500` 
  - 刪除: `text-danger-500`

---

## 🎨 **顏色標準規範**

### **必須使用的語義化顏色:**

```css
/* 主要顏色 - 用於主要操作 */
primary-600, primary-700, primary-500

/* 成功/激活顏色 - 用於成功狀態 */
accent-100, accent-800, accent-600

/* 次要顏色 - 用於次要操作 */
secondary-500, secondary-600, secondary-700

/* 警告顏色 - 用於警告狀態 */
warning-100, warning-800, warning-600

/* 危險顏色 - 用於刪除操作 */
danger-500, danger-600, danger-700
```

### **禁止使用的顏色:**
❌ `blue-500`, `green-500`, `red-500`, `purple-500` 等默認Tailwind顏色

---

## ⚡ **Vue.js 腳本標準**

```blade
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (window.vueApp) {
                    // 篩選功能
                    window.vueApp.filterListing = function() {
                        this.$refs.listing.refresh();
                    };

                    // 清除篩選
                    window.vueApp.clearFilters = function() {
                        this.filters.keyword = '';
                        this.current_status = 'all';
                        // 重置其他篩選條件...
                        this.filterListing();
                    };

                    // 刪除功能
                    window.vueApp.deleteItem = function(itemId, itemName) {
                        Swal.fire({
                            title: '確認刪除',
                            text: `您確定要刪除 "${itemName}" 嗎？此操作無法撤銷！`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: '刪除',
                            cancelButtonText: '取消'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.delete(`/admin/模組/${itemId}`)
                                    .then(response => {
                                        Swal.fire('刪除成功！', '數據已被刪除。', 'success');
                                        this.$refs.listing.refresh();
                                    })
                                    .catch(error => {
                                        Swal.fire('刪除失败！', '刪除時發生錯誤。', 'error');
                                    });
                            }
                        });
                    };
                }
            }, 100);
        });
    </script>
@endpush
```

---

## 📚 **創建新模組步驟指南**

### **步驟 1: 複製基本模板**
1. 複製 `resources/views/admin/centers/index.blade.php`
2. 重命名為你的模組名稱，例如 `products/index.blade.php`

### **步驟 2: 修改基本信息**
```blade
@extends('admin.layout.master', [
    'title' => '產品管理', // 修改標題
])
```

### **步驟 3: 配置 Vue 數據**
根據你的模組需求修改：
```blade
@vuedata([
    'filters' => (object) [
        'keyword' => '',
        'category' => '', // 修改為你的篩選條件
        'brand' => '',
    ],
    'current_status' => $status ?? 'all',
    'current_type' => 'all',
    'productModal' => false, // 修改為你的模組名稱
])
```

### **步驟 4: 修改頁面標題**
```blade
<h1>所有產品</h1> <!-- 修改標題 -->
```

### **步驟 5: 配置列定義**
修改 `Columns::load()` 部分：
```blade
'columns' => Columns::load([
    'is_active' => ['label' => '是否啟用', 'sortable' => false, 'align' => 'center'],
    'name' => ['label' => '產品名稱', 'sortable' => true],
    'category' => ['label' => '分類', 'sortable' => false],
    'price' => ['label' => '價格', 'sortable' => true],
    'stock' => ['label' => '庫存', 'sortable' => true],
    'created_at' => ['label' => '創建時間', 'sortable' => true],
]),
```

### **步驟 6: 修改 API URL**
```blade
'url' => passport_url('admin/private/products?page=1'), // 修改為你的 API 端點
```

### **步驟 7: 配置自定義列槽位**

#### A. 狀態切換槽位
```blade
@slot('column:is_active')
    <div class="flex justify-center">
        <label class="switch">
            <input type="checkbox" value="1" v-model="row.is_active"
                @change="$event.target.disabled = true, 
            $api.post($passport_url('/admin/private/products/' + row.id + '/active' ), 
            { active: row.is_active }, null, $event)
            .then(function (response) {
                Object.assign(row, response.data.data),
                $event.target.disabled = false;
                resetSummary(response.meta.summary);
            })">
        </label>
    </div>
@endslot
```

#### B. 自定義顯示槽位
```blade
@slot('column:price')
    <span class="font-semibold text-primary-600">${{ row.price }}</span>
@endslot
```

### **步驟 8: 配置操作按鈕**
```blade
@slot('action')
    <div class="flex items-center gap-2">
        <a :href="$ziggyRoute('admin.products.edit', [row.id])" 
           class="text-primary-500 hover:text-primary-600 transition-colors" 
           aria-label="Edit" data-balloon-pos="up-right"
           v-if="row.deleted_at == null">
            <i class="fad fa-pen text-base"></i>
        </a>
        
        <button @click="deleteItem(row.id, row.name)"
            class="text-danger-500 hover:text-danger-600 transition-colors cursor-pointer"
            aria-label="Delete" data-balloon-pos="up-right">
            <i class="fad fa-trash text-base"></i>
        </button>
    </div>
@endslot
```

---

## 🛠️ **必要的後端支持**

### **1. 控制器方法**
確保你的控制器有以下方法：
- `index()` - 返回頁面視圖和數據
- API 端點用於列表數據
- 狀態切換端點（如需要）

### **2. 路由配置**
在 `routes/web/admin.php` 和 `routes/api/admin.php` 中添加相應路由

### **3. 資源類**
創建對應的 Resource 類用於 API 數據格式化

---

## 🔧 **常見自定義需求**

### **1. 添加批量操作**
```blade
// 在列表組件中添加
'batchActions' => true,
```

### **2. 自定義篩選器**
```blade
// 在 Vue 數據中添加更多篩選條件
'filters' => (object) [
    'category' => [],
    'brand' => [],
    'price_range' => [],
],
```

### **3. 自定義排序**
```blade
'initialSorts' => ['name:asc', 'created_at:desc'],
```

---

## 💡 **最佳實踐**

1. **保持一致性**：使用相同的命名約定和結構
2. **數據驗證**：確保後端有適當的數據驗證
3. **權限控制**：添加適當的權限檢查
4. **國際化**：使用語言文件而不是硬編碼文字
5. **響應式設計**：確保在不同設備上正常顯示

---

## ⚠️ **開發注意事項**

### **必須遵守:**
1. ✅ 嚴格按照四區域布局結構
2. ✅ 使用語義化顏色系統
3. ✅ 保持一致的間距 (`px-6`)
4. ✅ 使用標準的Vue腳本模式
5. ✅ 所有文字使用中文

### **禁止行為:**
1. ❌ 改變四區域的順序
2. ❌ 使用非語義化顏色
3. ❌ 混合不同的布局模式
4. ❌ 省略任何一個標準區域
5. ❌ 使用內聯Vue腳本

---

## ✅ **創建檢查清單**

創建新模組時，請確保：
- [ ] 模板標題已修改
- [ ] Vue 數據配置正確
- [ ] 四區域布局完整
- [ ] 顏色使用符合標準
- [ ] 列定義符合數據結構
- [ ] API URL 正確指向
- [ ] 自定義槽位實現
- [ ] 操作按鈕配置
- [ ] Vue腳本功能正常
- [ ] 後端 API 支持
- [ ] 路由配置完成
- [ ] 權限檢查到位

---

## 🔗 **相關文檔**

建議按以下順序閱讀相關文檔：
1. `README_AI_DEVELOPMENT_RULES.md` - AI開發強制規則
2. `FRESH_COLOR_STANDARD.md` - 顏色使用標準
3. `FRESH_VUEDATA_INSTALL.md` - Vue集成指南
4. `FRESH_FONTAWESOME.md` - 圖標使用指南

---

**📝 文檔版本:** v2.0 (合併版)  
**📅 最後更新:** 2025年1月  
**👨‍💻 基於:** `users/index.blade.php` + `centers/index.blade.php`

遵循此完整指南，你可以快速為任何模組創建一致且功能完整的管理頁面