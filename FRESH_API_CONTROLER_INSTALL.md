# API 控制器創建標準指南 (優化版)

## 🚀 快速檢查清單

創建 API 控制器前，確保你有：

- [ ] **Service 類** (`app/Services/{Model}Service.php`)
- [ ] **Resource 類** (`app/Http/Resources/{Model}.php`) 
- [ ] **Controller 類** (`app/Http/Controllers/Api/Admin/{Model}Controller.php`)
- [ ] 繼承 `RestfulController` ✅
- [ ] 實現核心方法：`source()`, `transform*()`, `registerCustomFilters()` ✅
- [ ] 添加 `summary()` 統計方法 ✅
- [ ] CRUD 操作有適當驗證 ✅
- [ ] 刪除操作有安全檢查 ✅

## 🏗️ 基礎架構

```php
// 1. Service - 業務邏輯層
app/Services/{Model}Service.php

// 2. Resource - API 響應轉換層  
app/Http/Resources/{Model}.php

// 3. Controller - 控制器層
app/Http/Controllers/Api/Admin/{Model}Controller.php
```

## 🎯 Controller 核心模板

### 基本結構

```php
use Maxxidev\Http\Controllers\Restful\Controller as RestfulController;

class {Model}Controller extends RestfulController
{
    protected $service;

    public function __construct({Model}Service $service)
    {
        parent::__construct();
        $this->service = $service;
    }
}
```

### 必需的核心方法

```php
// 1. 數據源定義 ⭐
protected function source()
{
    $query = {Model}::with(['relations']);
    
    // 狀態篩選
    if (request('status') && request('status') !== 'all') {
        $query->where('is_active', request('status') === 'active');
    }
    
    return EloquentRepository::of($query);
}

// 2. 數據轉換 ⭐
protected function transformData($data)
{
    return new {Model}Resource($data);
}

protected function transformCollection($data)
{
    return {Model}Resource::collection($data);
}

// 3. 自定義過濾器 ⭐
protected function registerCustomFilters()
{
    $this->requestParser->registerCustomFilter('keyword', function ($app, $raw) {
        $query = $raw[0];
        $raw = QueryStringToEloquent::decodeFilter($raw[1]);
        $query->where(function ($q) use ($raw) {
            $q->where('name', $raw['operator'], $raw['value'])
              ->orWhere('email', $raw['operator'], $raw['value']);
        });
    });
}

// 4. 統計方法 ⭐
public function summary(Request $request)
{
    return $this->success()->withData([
        'status' => $this->service->getSummary(),
        'years' => {Model}::selectRaw('YEAR(created_at) as year')
                          ->distinct()->pluck('year')
    ]);
}
```

## 🔧 CRUD 操作要點

### 創建 (Store)
```php
public function store(Request $request)
{
    $inputs = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|unique:table,email',
    ]);
    
    $object = {Model}::create($inputs);
    return $this->success()->withData($this->transformData($object));
}
```

### 更新 (Update)  
```php
public function update(Request $request)
{
    $object = $this->routeModel();
    $inputs = $request->validate([
        'email' => Rule::unique('table')->ignore($object->id),
    ]);
    
    $object->update($inputs);
    return $this->success()->withData($this->transformData($object));
}
```

### 刪除 (Destroy)
```php
public function destroy(Request $request)
{
    $object = $this->routeModel();
    
    // 🛡️ 安全檢查
    if (!$this->service->canDelete($object)) {
        return $this->error('Cannot delete this item');
    }
    
    DB::transaction(fn() => $object->delete());
    return $this->success();
}
```

## 📋 Service 類模板

```php
class {Model}Service
{
    public function getSummary()
    {
        return [
            'total' => {Model}::count(),
            'active' => {Model}::where('is_active', true)->count(),
            'inactive' => {Model}::where('is_active', false)->count(),
        ];
    }

    public function canDelete({Model} $object)
    {
        // 檢查關聯數據
        return $object->relatedItems()->count() === 0;
    }
}
```

## 🎨 Resource 類模板

```php
class {Model} extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            
            // 關聯數據
            'relations' => $this->whenLoaded('relations'),
            
            // 時間戳
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

## ⚠️ 重要規則

### ✅ 必須做
- 繼承 `RestfulController` 
- 業務邏輯放 Service
- 使用 Resource 轉換數據
- 實現統計功能
- 刪除前檢查關聯
- 使用數據庫事務

### ❌ 不要做  
- 在 Controller 寫業務邏輯
- 直接返回模型數據
- 忘記輸入驗證
- 忘記錯誤處理

## 🚨 常見錯誤及解決

### 1. "Class not found" 錯誤
```php
// ❌ 錯誤：缺少 use 語句
// ✅ 正確：添加所有必需的 imports
use App\Models\{Model};
use App\Services\{Model}Service;
use App\Http\Resources\{Model} as {Model}Resource;
```

### 2. 查詢性能問題
```php
// ❌ 避免 N+1 查詢
// ✅ 使用 with() 預載入關聯
$query = {Model}::with(['relations']);
```

### 3. 過濾器不生效
```php
// ❌ 忘記注冊過濾器
// ✅ 在 registerCustomFilters() 中注冊
protected function registerCustomFilters()
{
    $this->requestParser->registerCustomFilter('keyword', ...);
}
```

## 🎯 快速開始模板

想快速創建一個標準的 API 控制器？複製以下模板並替換 `{Model}` 為你的模型名：

```bash
# 1. 創建文件
touch app/Services/{Model}Service.php
touch app/Http/Resources/{Model}.php
touch app/Http/Controllers/Api/Admin/{Model}Controller.php

# 2. 使用上面的模板填充內容
# 3. 添加路由
# 4. 測試 API 端點
```

---
**💡 提示**: 遵循這個指南可以確保 API 控制器的一致性和可維護性。有問題隨時參考這個文檔！
