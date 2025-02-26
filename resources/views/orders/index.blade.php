@extends('layouts.dash')

@section('head')
  <title></title>
@endsection

@section('content')
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <a href="{{ route('food-items.index') }}">
        Đơn hàng
      </a>
    </h1>
    <div>
      <a href="{{ route('food-items.create') }}"
        class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-upload fa-sm text-white-50"></i>
        Tạo
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%"
      cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Khách</th>
          <th>Bàn</th>
          <th>Giảm giá</th>
          <th>Tổng tiền</th>
          <th>Thanh toán</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
          <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->user->name }}</td>
            <td>{{ $order->table->name }}</td>
            <td>{{ number_format($order->discount, 0, '', ' ') }}%</td>
            <td>{{ number_format($order->total, 0, '', ' ') }}</td>
            <td>{{ $order->paid ? 'Rồi' : 'Chưa' }}</td>
            <td>
              <a href="{{ route('orders.edit', $order) }}"
                class="btn btn-sm btn-warning">Sửa</a>
              <form
                action="{{ route('orders.destroy', $order) }}"
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
  </div>
@endsection

@section('script')
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
@endsection
