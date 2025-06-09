<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\MetaType;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('type');
        
        $unitsQuery = Unit::with('type');
        
        if ($search) {
            $unitsQuery->where('name', 'like', "%{$search}%");
        }
        
        if ($typeFilter) {
            $unitsQuery->where('type_id', $typeFilter);
        }
        
        $units = $unitsQuery->orderBy('name')->paginate(10)->withQueryString();
        $types = MetaType::where('category', 'unit_type')->get();
        
        return view('admin.units.index', compact('units', 'search', 'types', 'typeFilter'));
    }

    public function create()
    {
        $types = MetaType::where('category', 'unit_type')->get();
        return view('admin.units.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'type_id' => 'required|exists:meta_types,id',
        ]);

        Unit::create($request->all());

        return redirect()->route('admin.units.index')
            ->with('success', 'Đơn vị đã được tạo thành công.');
    }

    public function show(Unit $unit)
    {
        $unit->load('type');
        
        // Đếm số lượng người dùng trong đơn vị
        $totalUsers = $unit->users()->count();
        
        // Lấy danh sách người dùng trong đơn vị
        $users = $unit->users()->with('role')->paginate(5);
        
        return view('admin.units.show', compact('unit', 'totalUsers', 'users'));
    }

    public function edit(Unit $unit)
    {
        $types = MetaType::where('category', 'unit_type')->get();
        return view('admin.units.edit', compact('unit', 'types'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'type_id' => 'required|exists:meta_types,id',
        ]);

        $unit->update($request->all());

        return redirect()->route('admin.units.index')
            ->with('success', 'Thông tin đơn vị đã được cập nhật thành công.');
    }

    public function destroy(Unit $unit)
    {
        $hasUsers = $unit->users()->exists();
        
        if ($hasUsers) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Không thể xóa đơn vị này vì còn có người dùng thuộc đơn vị.');
        }
        
        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Đơn vị đã được xóa thành công.');
    }
}