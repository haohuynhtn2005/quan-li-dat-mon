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
    <select name="foodTypeId" onchange="this.form.submit()"
      class="form-control form-control-sm d-inline mb-1" style="width: fit-content;">
      <option value="">Tất cả món</option>
        @foreach($foodTypes as $foodType)
            <option value="{{ $foodType->id}}"
              @if (urldecode(request('foodTypeId')) == $foodType->id)
                selected
              @endif
              >
                {{ $foodType->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Ok</button>
  </form>

  <canvas id="weeklyChart"></canvas>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('weeklyChart').getContext('2d');
      ctx.canvas.height = 120; // Set height before creating the chart
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: @json($labels),
          datasets: [
            {
              label: 'Thống kê doanh thu (VND)',
              data: @json($revenueData),
              backgroundColor: 'rgba(54, 162, 235, 0.6)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              yAxisID: 'y-left' // Attach to left Y-axis
            },
            {
              label: 'Number of Orders',
              backgroundColor: 'rgba(255, 99, 132, 0.6)', // Red
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 1,
              data: @json($orderCountData),
              yAxisID: 'y-right' // Attach to right Y-axis
            }
          ],
        },
        options: {
          responsive: true,
          scales: {
            yAxes: [
              ticks: {
                  beginAtZero: true,
                  callback: function(value) {
                    return value.toLocaleString('vi-VN') +
                      ' ₫'; // Format as VND
                  }
                },
              },
              {
                id: 'y-left',
                position: 'left',
                ticks: { beginAtZero: true },
                scaleLabel: {
                    display: true,
                    labelString: 'Revenue ($)'
                },
              },
              {
                id: 'y-right',
                position: 'right',
                ticks: { beginAtZero: true },
                scaleLabel: {
                    display: true,
                    labelString: 'Number of Orders'
                }
              }
            ],
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
  @include('components.scripts.alert-script')
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
  </script>
@endsection
