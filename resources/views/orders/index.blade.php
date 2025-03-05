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
            <option value="{{ urlencode($status) }}"
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
              <a href="{{ route('orders.show', [$order, ...request()->query()]) }}"
                class="btn btn-sm btn-info">Đặt</a>
              @if ($order->status == 'đang ăn' || $order->status == 'đã ăn')
                <button class="btn btn-sm btn-success" onclick="showPaymentModal({{ $order->id }},{{ $order->total / 100 * (100 - $order->discount) }})">Thanh toán</button>
              @endif
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
      {{ $orders->appends(request()->query())->links() }}
    </div>
  </div>

  <!-- Payment Modal -->
  <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Thanh toán</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <p>Quét mã QR để thanh toán:</p>
          <img id="qrCode" src="" alt="QR Code" class="img-fluid">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
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

    function showPaymentModal(orderId, price) {
      const bankInfo = {
            bankId: "970416",         // Mã ngân hàng ACB
            accountNo: "38752307",   // Số tài khoản của bạn
            accountName: "TRAN MINH QUOC THAI", // Tên tài khoản của bạn
            amount: price,
            content: `Thanh toan don hang ${orderId}` // Nội dung chuyển khoản
        };
        // Tạo chuỗi dữ liệu theo định dạng QR Pay VietQR
        const qrData = `https://api.vietqr.io/image/${bankInfo.bankId}-${bankInfo.accountNo}-compact.jpg?amount=${bankInfo.amount}&addInfo=${bankInfo.content}&accountName=${bankInfo.accountName}`;
      document.getElementById('qrCode').src = qrData;
      $('#paymentModal').modal('show');
    }
  </script>
@endsection
