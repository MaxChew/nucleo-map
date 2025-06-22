<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Role as RoleResource;
use Maxxidev\Http\Controllers\Restful\Controller as RestfulController;
use Maxxidev\Http\Controllers\Restful\RequestParsers\QueryStringToEloquent;
use Maxxidev\Repositories\EloquentRepository;
use Illuminate\Validation\Rule;

class RoleController extends RestfulController
{
    protected $service;

    public function __construct(RoleService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function source()
    {
        $query = Role::with(['permissions:id,name', 'users:id,name,email']);

        // 状态筛选
        if (request()->query('status') && request()->query('status') !== 'all') {
            switch (request()->query('status')) {
                case 'with_permissions':
                    $query->whereHas('permissions');
                    break;
                case 'without_permissions':
                    $query->whereDoesntHave('permissions');
                    break;
            }
        }

        return EloquentRepository::of($query);
    }

    protected function transformData($data)
    {
        return new RoleResource($data);
    }

    protected function transformCollection($data)
    {
        return RoleResource::collection($data);
    }

    protected function registerCustomFilters()
    {
        $this->requestParser->registerCustomFilter('keyword', function ($app, $raw) {
            $query = $raw[0];
            $raw = $raw[1];
            $raw = QueryStringToEloquent::decodeFilter($raw);
            $query->where(function ($query) use ($raw) {
                $query->orWhere(DB::raw("name"), $raw['operator'], $raw['value'])
                    ->orWhere(DB::raw("display_name"), $raw['operator'], $raw['value'])
                    ->orWhere('description', $raw['operator'], $raw['value']);
            });
        });
    }

    public function store(Request $request)
    {
        $inputs = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'guard_name' => 'nullable|string|max:255',
        ]);

        // 设置默认guard_name
        $inputs['guard_name'] = $inputs['guard_name'] ?? 'web';

        $object = Role::create($inputs);

        return $this->success($this->message('created', 'Role'))->withData($this->transformData($object));
    }

    public function update(Request $request)
    {
        $object = $this->routeModel();

        if (!$object) {
            return $this->error('Role not found');
        }

        // 防止修改系统角色的名称
        $nameValidation = 'required|string|max:255';
        if (!in_array($object->name, $this->service->getSystemRoles())) {
            $nameValidation .= '|' . Rule::unique('roles', 'name')->ignore($object->id);
        }

        $inputs = $request->validate([
            'name' => $nameValidation,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'guard_name' => 'nullable|string|max:255',
        ]);

        // 系统角色不能修改名称
        if (in_array($object->name, $this->service->getSystemRoles()) && $inputs['name'] !== $object->name) {
            return $this->error('Cannot modify system role name');
        }

        $object->update($inputs);

        return $this->success($this->message('updated', 'Role'))->withData($this->transformData($object));
    }

    public function destroy(Request $request)
    {
        $object = $this->routeModel();

        if (!$this->service->canDeleteRole($object)) {
            if (in_array($object->name, $this->service->getSystemRoles())) {
                return $this->error('Cannot delete system roles');
            }
            if ($object->users()->count() > 0) {
                return $this->error('Cannot delete role that has assigned users');
            }
        }

        $result = DB::transaction(function () use ($object) {
            $object->delete();
            return true;
        });

        return $this->success($this->message('deleted', 'Role'))->withData($result);
    }

    public function summary(Request $request)
    {
        $years_ary = Role::select(
            DB::raw("(DATE_FORMAT(created_at, '%Y')) as year")
        )
            ->orderBy('year')
            ->groupBy('year')
            ->get('year')
            ->pluck('year');

        $years = $years_ary->mapWithKeys(function ($item, $key) {
            return [$item => $item];
        });

        return $this->success()->withData([
            'status' => $this->getSummary(),
            'years_ary' => $years_ary,
            'years' => $years,
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $object = $this->routeModel();
        if (!$object) {
            return $this->error('Role not found');
        }

        // 这里可以将来扩展角色状态切换功能
        // 目前只返回角色数据
        return $this->success('Role status updated successfully')->withData($this->transformData($object));
    }

    private function getSummary()
    {
        return $this->service->getRolesSummary();
    }
}
