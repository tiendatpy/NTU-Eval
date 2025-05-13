<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Role;
use App\Models\Unit;
use App\Models\MetaType;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Lấy danh sách roles và units từ cơ sở dữ liệu
        $roles = Role::all(); // Lấy tất cả các vai trò
        $units = Unit::all(); // Lấy tất cả các đơn vị
        $educationLevels = MetaType::where('category', 'education_level')->get();

        // Truyền dữ liệu roles và units đến view
        return view('auth.register', compact('roles', 'units', 'educationLevels'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'education_id' => ['required', 'exists:meta_types,id'],
            'role_id' => ['required', 'exists:roles,id'], 
            'unit_id' => ['required', 'exists:units,id'], 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'education_id' => $request->education_id,
            'role_id' => $request->role_id,
            'unit_id' => $request->unit_id,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
