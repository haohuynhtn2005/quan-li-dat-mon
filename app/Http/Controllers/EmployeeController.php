<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
  public function index()
  {
    $employees = User::whereNotIn('role', ['admin', 'user'])->paginate(10);
    return view('employees.index')->with('employees', $employees);
  }

  public function create()
  {
    $departments = Department::get();
    return view('employees.form')->with([
      'mode' => 'create',
      'departments' => $departments,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
      'phone' => ['required', 'regex:/^0\d{9,10}$/'],
      'password' => ['required', Rules\Password::defaults(), 'confirmed', ],
      'address' => 'nullable|string|max:65535',
      'role' => ['required', Rule::in(['user', 'staff', 'admin'])],
      // 'department_id' => 'required|exists:departments,id',
    ], [
      'name.required' => 'Tên nhân viên là bắt buộc.',
      'email.required' => 'Email là bắt buộc.',
      'email.email' => 'Email không đúng định dạng.',
      'email.unique' => 'Email đã tồn tại.',
      'phone.required' => 'Số điện thoại là bắt buộc.',
      'department_id.required' => 'Phòng ban là bắt buộc.',
      'department_id.exists' => 'Phòng ban không tồn tại.',
    ]);

    User::create($request->all());

    return redirect()->route('employees.index')->with('success', 'Nhân viên đã được thêm thành công.');
  }

  public function edit($id)
  {
    $departments = Department::get();

    $employee = User::whereNotIn('role', ['admin', 'user'])->findOrFail($id); // Lấy dữ liệu nhân viên theo ID
    return view('employees.form', [
      'mode' => 'update',
      'employee' => $employee,
      'departments' => $departments,
    ]);
  }

  // Xử lý cập nhật dữ liệu
  public function update(Request $request, $id)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:employees,email,' . $id,
      'phone' => 'required|string|max:15',
      'password' => ['nullable', Rules\Password::defaults(), 'confirmed', ],
      // 'department_id' => 'required|integer',
    ]);

    $employee = User::findOrFail($id);
    $employee->update($request->except(['password']));

    // Update password only if provided
    if ($request->filled('password')) {
        $employee->update([
            'password' => Hash::make($request->password),
        ]);
    }

    return redirect()->route('employees.index')->with('success', 'Cập nhật nhân viên thành công.');
  }

  public function renderDelete($id)
  {
    $departments = Department::get();

    $employee = Employee::findOrFail($id); // Lấy dữ liệu nhân viên theo ID
    return view('employee.form', [
      'mode' => 'delete',
      'employee' => $employee,
      'departments' => $departments,
    ]);
  }

  public function destroy($id)
  {
    $employee = User::findOrFail($id);
    try {
      $employee->delete();
    } catch (\Exception $e) {
      return redirect()->route('employees.index')->with(['err' => 'Không thể xóa!']);
    }
    return redirect()->route('employees.index')->with('success', 'Xóa thông tin thành công!');
  }
}
