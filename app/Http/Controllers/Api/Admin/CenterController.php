<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\Center as CenterResource;
use App\Models\Center;
use App\Services\CenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maxxidev\Http\Controllers\Restful\Controller as RestfulController;
use Maxxidev\Http\Controllers\Restful\RequestParsers\QueryStringToEloquent;
use Maxxidev\Repositories\EloquentRepository;

class CenterController extends RestfulController
{
    protected $service;

    public function __construct(CenterService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * 数据源定义.
     */
    protected function source()
    {
        $query = Center::with(['createdBy', 'updatedBy']);

        // 状态筛选（使用is_active字段，不是ppis_active）
        if (request('status') && request('status') !== 'all') {
            $query->where('is_active', request('status') === 'active');
        }

        // state 和 service 筛选现在通过自定义过滤器处理
        // 移除重复的筛选逻辑，避免冲突

        return EloquentRepository::of($query);
    }

    /**
     * 数据转换.
     */
    protected function transformData($data)
    {
        return new CenterResource($data);
    }

    protected function transformCollection($data)
    {
        return CenterResource::collection($data);
    }

    /**
     * 自定义过滤器.
     */
    protected function registerCustomFilters()
    {
        $this->requestParser->registerCustomFilter('keyword', function ($app, $raw) {
            $query = $raw[0];
            $raw = QueryStringToEloquent::decodeFilter($raw[1]);
            $query->where(function ($q) use ($raw) {
                $q->where('name', $raw['operator'], $raw['value'])
                  ->orWhere('code_name', $raw['operator'], $raw['value'])
                  ->orWhere('code_no', $raw['operator'], $raw['value'])
                  ->orWhere('state', $raw['operator'], $raw['value']);
            });
        });

        // 注册 service 自定义过滤器，处理 JSON 字段查询
        $this->requestParser->registerCustomFilter('service', function ($app, $raw) {
            $query = $raw[0];
            $raw = QueryStringToEloquent::decodeFilter($raw[1]);

            // 如果不是 'all'，则进行 JSON 包含查询
            if ($raw['value'] !== 'all') {
                $query->whereJsonContains('services', $raw['value']);
            }
        });

        // 注册 state 自定义过滤器，避免与其他筛选冲突
        $this->requestParser->registerCustomFilter('state', function ($app, $raw) {
            $query = $raw[0];
            $raw = QueryStringToEloquent::decodeFilter($raw[1]);

            // 如果不是 'all'，则进行州筛选
            if ($raw['value'] !== 'all') {
                $query->where('state', $raw['operator'], $raw['value']);
            }
        });
    }

    /**
     * 统计方法.
     */
    public function summary(Request $request)
    {
        return $this->success()->withData([
            'status' => $this->service->getSummary(),
            'filter_options' => $this->service->getFilterOptions(),
            'years' => Center::selectRaw('YEAR(created_at) as year')
                          ->distinct()
                          ->orderBy('year', 'desc')
                          ->pluck('year'),
        ]);
    }

    /**
     * 创建医疗中心.
     */
    public function store(Request $request)
    {
        $inputs = $request->validate([
            'state' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'code_name' => 'required|string|max:10|unique:centers,code_name',
            'code_no' => 'required|string|max:10|unique:centers,code_no',
            'contact' => 'nullable|string',
            'webpage' => 'nullable|url',
            'services' => 'nullable|array',
            'services.*' => 'string|in:SPECT,PET,RAI,PRRT,PSMA,SIRT,MIBG,BONE-P,RSO,AC',
            'is_active' => 'boolean',
        ]);

        $center = DB::transaction(function () use ($inputs) {
            return Center::create($inputs);
        });

        return $this->success()->withData($this->transformData($center));
    }

    /**
     * 更新医疗中心.
     */
    public function update(Request $request)
    {
        $center = $this->routeModel();

        $inputs = $request->validate([
            'state' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'code_name' => [
                'required',
                'string',
                'max:10',
                Rule::unique('centers')->ignore($center->id),
            ],
            'code_no' => [
                'required',
                'string',
                'max:10',
                Rule::unique('centers')->ignore($center->id),
            ],
            'contact' => 'nullable|string',
            'webpage' => 'nullable|url',
            'services' => 'nullable|array',
            'services.*' => 'string|in:SPECT,PET,RAI,PRRT,PSMA,SIRT,MIBG,BONE-P,RSO,AC',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($center, $inputs) {
            $center->update($inputs);
        });

        return $this->success()->withData($this->transformData($center->fresh()));
    }

    /**
     * 删除医疗中心.
     */
    public function destroy(Request $request)
    {
        $center = $this->routeModel();

        // 安全检查
        if (!$this->service->canDelete($center)) {
            return $this->error('无法删除此医疗中心，可能存在相关联的数据');
        }

        DB::transaction(function () use ($center) {
            $center->delete();
        });

        return $this->success();
    }

    /**
     * 批量导入数据.
     */
    public function import(Request $request)
    {
        $request->validate([
            'centers' => 'required|array',
            'centers.*.state' => 'required|string',
            'centers.*.name' => 'required|string',
            'centers.*.code_name' => 'required|string',
            'centers.*.code_no' => 'required|string',
            'centers.*.contact' => 'nullable|string',
            'centers.*.webpage' => 'nullable|string',
            'centers.*.services' => 'nullable|array',
        ]);

        try {
            $this->service->importFromJson($request->input('centers'));

            return $this->success()->withMessage('医疗中心数据导入成功');
        } catch (\Exception $e) {
            return $this->error('导入失败: '.$e->getMessage());
        }
    }

    /**
     * 获取地图数据 - 包含篩選功能.
     */
    public function mapData(Request $request)
    {
        $mapData = $this->service->getMapData();
        
        // 如果有篩選參數，則對結果進行篩選
        $centers = $mapData['centers'];
        
        // 狀態篩選
        if ($request->filled('state') && $request->state !== 'all') {
            $centers = array_filter($centers, function($center) use ($request) {
                return $center['state'] === $request->state;
            });
        }
        
        // 服務篩選
        if ($request->filled('service') && $request->service !== 'all') {
            $centers = array_filter($centers, function($center) use ($request) {
                return in_array($request->service, $center['services']);
            });
        }
        
        // 關鍵字搜尋
        if ($request->filled('keyword')) {
            $keyword = strtolower($request->keyword);
            $centers = array_filter($centers, function($center) use ($keyword) {
                return strpos(strtolower($center['name']), $keyword) !== false ||
                       strpos(strtolower($center['code_name']), $keyword) !== false ||
                       strpos(strtolower($center['code_no']), $keyword) !== false ||
                       strpos(strtolower($center['state']), $keyword) !== false;
            });
        }
        
        // 重新索引數組
        $centers = array_values($centers);
        
        return $this->success()->withData([
            'centers' => $centers,
            'states' => $mapData['states'],
            'services' => $mapData['services'],
            'total_count' => count($centers),
            'original_count' => $mapData['total_count'],
            'filters' => [
                'state' => $request->state,
                'service' => $request->service,
                'keyword' => $request->keyword,
            ]
        ]);
    }
    
    /**
     * 获取地图筛选选项.
     */
    public function getFilters(Request $request)
    {
        $filterOptions = $this->service->getFilterOptions();
        $stateCoordinates = $this->service->getStateCoordinates();
        
        return $this->success()->withData([
            'states' => $filterOptions['states'],
            'services' => $filterOptions['services'],
            'coordinates' => $stateCoordinates,
        ]);
    }
}
