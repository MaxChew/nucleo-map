<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\User as UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maxxidev\Http\Controllers\Restful\Controller as RestfulController;
use Maxxidev\Http\Controllers\Restful\RequestParsers\QueryStringToEloquent;
use Maxxidev\Repositories\EloquentRepository;
use Maxxidev\Rules\ValidPassword;

class UserController extends RestfulController
{
    protected $service;

    public function __construct(UserService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function source()
    {
        $query = User::with('roles');

        // 状态筛选
        if (request()->query('status') == null || strtolower(request()->query('status')) == 'all') {
            // 显示所有用户
        } else {
            if (strtolower(request()->query('status')) == 'active') {
                $query->where('is_active', true);
            } elseif (strtolower(request()->query('status')) == 'inactive') {
                $query->where('is_active', false);
            }
        }

        return EloquentRepository::of($query);
    }

    protected function transformData($data)
    {
        return new UserResource($data);
    }

    protected function transformCollection($data)
    {
        return UserResource::collection($data);
    }

    protected function registerCustomFilters()
    {
        $this->requestParser->registerCustomFilter('keyword', function ($app, $raw) {
            $query = $raw[0];
            $raw = $raw[1];
            $raw = QueryStringToEloquent::decodeFilter($raw);
            $query->where(function ($query) use ($raw) {
                $query->orWhere(DB::raw('name'), $raw['operator'], $raw['value'])
                    ->orWhere(DB::raw('mobile'), $raw['operator'], $raw['value'])
                    ->orWhere('email', $raw['operator'], $raw['value']);
            });
        });
    }

    public function store(Request $request)
    {
        $inputs = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'required|unique:users,email|max:250',
            'password' => ['nullable', 'string', 'confirmed', new ValidPassword()],
            'gender' => 'nullable|in:male,female,other',
        ]);

        if (isset($inputs['password']) && $inputs['password']) {
            $inputs['password'] = bcrypt($inputs['password']);
        } else {
            $inputs['password'] = bcrypt(config('defaults.default_password', 'password'));
        }

        $object = User::create(array_merge(
            $inputs, [
                'is_active' => true,
            ]));

        return $this->success($this->message('created', 'User'))->withData($this->transformData($object));
    }

    public function update(Request $request)
    {
        $object = $this->routeModel();

        if (!$object) {
            return $this->error('User not found');
        }

        $inputs = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($object->id),
            ],
            'gender' => 'nullable|in:male,female,other',
        ]);

        $object->update($inputs);

        return $this->success($this->message('updated', 'User'))->withData($this->transformData($object));
    }

    public function updatePassword(Request $request)
    {
        $object = $this->routeModel();

        if (!$object) {
            return $this->error('User not found');
        }

        try {
            if ($request->input('password') && $request->input('password_confirmation')) {
                $request->validate([
                    'password' => ['required', 'string', 'confirmed', new ValidPassword()],
                ]);

                $object->update(['password' => bcrypt($request->password)]);
            } else {
                $object->update(['password' => bcrypt(config('defaults.default_password', 'password'))]);
            }

            return $this->success('Password updated successfully')->withData($this->transformData($object));
        } catch (\Exception $e) {
            return $this->error('Update password failed: '.$e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $object = $this->routeModel();

        $result = DB::transaction(function () use ($object) {
            $object->delete();

            return true;
        });

        return $this->success($this->message('deleted', 'User'))->withData($result);
    }

    public function summary(Request $request)
    {
        $years_ary = User::select(
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

    public function active(Request $request)
    {
        $object = $this->routeModel();
        if (!$object) {
            return $this->error('User not found');
        }

        $object->update([
            'is_active' => $request->input('active', !$object->is_active),
        ]);

        return $this->success(null, $this->transformData($object))->withMeta(['summary' => $this->getSummary()]);
    }

    private function getSummary()
    {
        return [
            'all' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];
    }
}
