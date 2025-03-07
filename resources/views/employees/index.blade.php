@extends('layouts.dash')

@section('head')
  <title>Employee</title>
@endsection

@section('content')
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      Danh sách nhân viên
    </h1>
    <div>
      <a href="{{ route('employees.create') }}"
        class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50"></i>
        Tạo
      </a>
    </div>
  </div>
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%"
          cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên</th>
              <th>Email</th>
              <th>SDT</th>
              <th>Chức vụ</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($employees as $employee)
              <tr>
                <td>{{ $employee->id }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->role == 'staff' ? 'Bồi bàn' : 'Đầu bếp' }}</td>
                <td>
                  <a href="{{ route('employees.edit', $employee->id) }}"
                    class="btn btn-sm btn-warning">Sửa</a>
                  <form
                    action="{{ route('employees.destroy', $employee->id) }}"
                    method="POST"
                    class="d-inline"
                    >
                    @method('DELETE')
                    @csrf
                    <button class="btn btn-sm btn-danger">Xóa</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
          <!-- Pagination -->
        <div class="d-flex justify-content-center">
          {{ $employees->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  @if (session('success'))
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Thành công',
      text: '{{ session('success') }}',
      confirmButtonColor: '#4e73df',
    })
  </script>
  @endif
  @if (session('error'))
  <script>
    Swal.fire({
      icon: 'warning',
      title: 'Lỗi',
      text: '{{ session('error') }}',
      confirmButtonColor: '#4e73df',
    })
  </script>
  @endif
  <script>
    @foreach ($errors->all() as $error)
    console.warn(`{{ $error }}`)
    @endforeach
  </script>
@endsection
