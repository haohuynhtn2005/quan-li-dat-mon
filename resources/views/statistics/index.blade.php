@extends('layouts.dash')

@section('head')
  <title></title>
@endsection

@section('content')
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <a href="{{ route('statistics.index') }}">
        Thống kê
      </a>
    </h1>
    <div>
      {{-- <a href="" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-upload fa-sm text-white-50"></i>
        Tạo
      </a> --}}
    </div>
  </div>

  @php
    $types = ['tuần', 'tháng', 'năm'];
  @endphp
  <form method="GET" class="mb-4">
    {{-- <label>Select Date:</label> --}}
    <input type="date" name="date"
      value="{{ request('date') ?? now()->toDateString() }}"
      class="form-control form-control-sm d-inline" style="width: fit-content"
      >
    <select name="type" onchange="this.form.submit()"
      class="form-control form-control-sm d-inline mb-1" style="width: fit-content;">
        @foreach($types as $type)
            <option value="{{ urlencode($type) }}"
              @if (urldecode(request('type')) == $type)
                selected
              @endif
              >
                {{ Str::ucfirst($type) }}
            </option>
        @endforeach
    </select>
    <select name="foodItemId" onchange="this.form.submit()"
      class="form-control form-control-sm d-inline mb-1" style="width: fit-content;">
      <option value="">Tất cả món</option>
        @foreach($foodItems as $foodItem)
            <option value="{{ $foodItem->id}}"
              @if (urldecode(request('foodItemId')) == $foodItem->id)
                selected
              @endif
              >
                {{ $foodItem->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Ok</button>
  </form>

  <h4>Order tại quán</h4>
  <div class=" container-sm">
    <canvas id="weeklyChart"></canvas>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('weeklyChart').getContext('2d');
      ctx.canvas.height = 180; // Set height before creating the chart
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: {!! json_encode($labels) !!},
          datasets: [{
            label: 'Thống kê doanh thu (VND)',
            data: {!! json_encode($data) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function(value) {
                  return value.toLocaleString('vi-VN') +
                    ' ₫'; // Format as VND
                }
              }
            }],
          },
          tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                return tooltipItem.yLabel.toLocaleString('vi-VN') +
                  " ₫";
              }
            }
          }
        }
      });
    });
  </script>
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
        bankId: "970416", // Mã ngân hàng ACB
        accountNo: "38752307", // Số tài khoản của bạn
        accountName: "TRAN MINH QUOC THAI", // Tên tài khoản của bạn
        amount: price,
        content: `Thanh toan don hang ${orderId}` // Nội dung chuyển khoản
      };
      // Tạo chuỗi dữ liệu theo định dạng QR Pay VietQR
      const qrData =
        `https://api.vietqr.io/image/${bankInfo.bankId}-${bankInfo.accountNo}-compact.jpg?amount=${bankInfo.amount}&addInfo=${bankInfo.content}&accountName=${bankInfo.accountName}`;
      document.getElementById('qrCode').src = qrData;
      $('#paymentModal').modal('show');
    }
  </script>
@endsection
