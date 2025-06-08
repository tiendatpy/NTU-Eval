<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $unitsQuery = Unit::query();
        
        if ($search) {
            $unitsQuery->where('name', 'like', "%{$search}%");
        }
        
        $units = $unitsQuery->orderBy('name')->paginate(10)->withQueryString();
        
        return view('admin.units.index', compact('units', 'search'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Unit::create($request->all());

        return redirect()->route('admin.units.index')
            ->with('success', 'Đơn vị đã được tạo thành công.');
    }

    public function show(Unit $unit)
    {
        return view('admin.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
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