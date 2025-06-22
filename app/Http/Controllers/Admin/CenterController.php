<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Services\CenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CenterController extends Controller
{
    protected $centerService;

    public function __construct(CenterService $centerService)
    {
        $this->centerService = $centerService;
    }

    /**
     * Display a listing of the centers.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $state = $request->get('state', 'all');
        $service = $request->get('service', 'all');
        
        // 获取状态面板数据
        $summary = $this->centerService->getSummary();
        
        return view('admin.centers.index', compact('status', 'state', 'service', 'summary'));
    }

    /**
     * Show the form for creating a new center.
     */
    public function create()
    {
        $filterOptions = $this->centerService->getFilterOptions();
        return view('admin.centers.create', compact('filterOptions'));
    }

    /**
     * Display the specified center.
     */
    public function show(Center $center)
    {
        return view('admin.centers.show', compact('center'));
    }

    /**
     * Store a newly created center in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'code_name' => 'required|string|max:10|unique:centers,code_name',
            'code_no' => 'required|string|max:10|unique:centers,code_no',
            'contact' => 'nullable|string',
            'webpage' => 'nullable|url',
            'services' => 'nullable|array',
            'services.*' => 'string|in:SPECT,PET,RAI,PRRT,PSMA,SIRT,MIBG,BONE-P,RSO,AC',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $center = Center::create([
                'name' => $request->name,
                'state' => $request->state,
                'code_name' => $request->code_name,
                'code_no' => $request->code_no,
                'contact' => $request->contact,
                'webpage' => $request->webpage,
                'services' => $request->services,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();
            
            return redirect()->route('admin.centers.show', $center)
                ->with('success', '医疗中心创建成功！');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', '创建医疗中心时发生错误：' . $e->getMessage());
        }
    }

    /**
     * Update the specified center in storage.
     */
    public function update(Request $request, Center $center)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'code_name' => [
                'required',
                'string',
                'max:10',
                Rule::unique('centers', 'code_name')->ignore($center->id)
            ],
            'code_no' => [
                'required',
                'string',
                'max:10',
                Rule::unique('centers', 'code_no')->ignore($center->id)
            ],
            'contact' => 'nullable|string',
            'webpage' => 'nullable|url',
            'services' => 'nullable|array',
            'services.*' => 'string|in:SPECT,PET,RAI,PRRT,PSMA,SIRT,MIBG,BONE-P,RSO,AC',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $center->update([
                'name' => $request->name,
                'state' => $request->state,
                'code_name' => $request->code_name,
                'code_no' => $request->code_no,
                'contact' => $request->contact,
                'webpage' => $request->webpage,
                'services' => $request->services,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();
            
            return redirect()->route('admin.centers.show', $center)
                ->with('success', '医疗中心更新成功！');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', '更新医疗中心时发生错误：' . $e->getMessage());
        }
    }

    /**
     * Remove the specified center from storage.
     */
    public function destroy(Center $center)
    {
        try {
            $center->delete();
            
            return redirect()->route('admin.centers.index')
                ->with('success', '医疗中心删除成功！');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '删除医疗中心时发生错误：' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified center.
     */
    public function edit(Center $center)
    {
        $filterOptions = $this->centerService->getFilterOptions();
        return view('admin.centers.edit', compact('center', 'filterOptions'));
    }
} 