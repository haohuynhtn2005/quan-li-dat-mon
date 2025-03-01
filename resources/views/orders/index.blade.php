@extends('layouts.dash')

@section('head')
  <title></title>
@endsection

@section('content')
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <a href="{{ route('orders.index') }}">
        Đơn hàng
      </a>
    </h1>
    <div>
      <a href="{{ route('orders.create') }}"
        class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-upload fa-sm text-white-50"></i>
        Tạo
      </a>
    </div>
  </div>

  @php
    $statuses = ['đang ăn', 'đã ăn', 'đã thanh toán'];
  @endphp
  <form method="GET" action="{{ route('orders.index') }}">
    <select name="status" onchange="this.form.submit()"
      class="form-control form-control-sm mb-1" style="width: fit-content;">
        <option value="">Tất cả</option>
        @foreach($statuses as $status)
            <option value="{{ rawurlencode($status) }}"
              @if (urldecode(request('status')) == $status)
                selected
              @endif
              >
                {{ Str::ucfirst($status) }}
            </option>
        @endforeach
    </select>
  </form>
  
  <div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%"
      cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Khách</th>
          <th>Bàn</th>
          <th>Giảm</th>
          <th>Tổng </th>
          <th>Còn lại</th>
          <th>Thanh toán</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
          <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->user ? $order->user->name : 'Khách vãng lai' }}</td>
            <td>{{ $order->table->name }}</td>
            <td>{{ number_format($order->discount, 0, '', ' ') }}%</td>
            <td>{{ number_format($order->total, 0, '.', ',') }}₫</td>
            <td>{{ number_format($order->total / 100 * (100 - $order->discount), 0, '.', ',') }}₫</td>
            <td>
              {{-- <form action="{{ route('orders.updatePaid', $order) }}" method="POST" class="d-inline">
                @method('PATCH')
                @csrf
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="paid" id="paid{{$order->id}}" value="1"
                          @if ($order->paid)
                            checked
                          @endif
                          onclick="return confirm(this)"
                          class="custom-control-input">
                        <label class="custom-control-label" for="paid{{$order->id}}">Thanh toán</label>
                    </div>
                    @error('paid')
                      <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>
              </form> --}}
              <form action="{{ route('orders.update', $order) }}" method="POST">
                @method('PATCH')
                @csrf
                <input type="hidden" name="user_id" value="{{ $order->user_id }}">
                <input type="hidden" name="table_id" value="{{ $order->table_id }}">
                <input type="hidden" name="discount_id" value="{{ $order->discount }}">
                <div class="mb-3">
                  <select name="status" id="status"
                    old="{{ $order->status }}"
                    {{-- value="{{ $order->status }}" --}}
                    onchange="return confirmSweet(this)"
                    class="form-control form-control-sm @error('status') is-invalid @enderror"
                    style="width: fit-content"
                    >
                    @foreach($statuses as $status)
                        <option value="{{ $status }}"
                          @if (isset($order) && old('status', $order->status) == $status)
                            selected
                          @endif
                          >
                            {{ Str::ucfirst($status) }}
                        </option>
                    @endforeach
                  </select>
                  @error('status')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
              </form>
            </td>
            <td>
              {{-- <a href="{{ route('orders.edit', $order) }}"
                class="btn btn-sm btn-warning">Sửa</a> --}}
              <a href="{{ route('orders.show', $order) }}"
                class="btn btn-sm btn-info">Đặt</a>
              <form
                action="{{ route('orders.destroy', $order) }}"
                method="POST"
                class="d-inline"
                >
                @method('DELETE')
                @csrf
                <button class="btn btn-sm btn-danger"
                  onclick="return confirmSweet(this)">Hủy</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
      {{-- {!! $orders->links() !!} --}}
      {{ $orders->appends(request()->query())->links() }}
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
     function confirmSweet(elem) {
      elem.setAttribute('new', elem.value)
      elem.value = elem.getAttribute('old')
      Swal.fire({
        title: "Xác nhận?",
        text: "Thực hiện hành động này",
        icon: "warning",
        reverseButtons: true,
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonColor: "#4e73df",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
      }).then((result) => {
        if (result.isConfirmed) {
          elem.value = elem.getAttribute('new');
          elem.form.submit();
        }
      });
      return false;
    }
    @foreach ($errors->all() as $error)
    console.warn(`{{ $error }}`)
    @endforeach
  </script>
@endsection
