@extends('layouts.dash')

@section('head')
  <title></title>
@endsection

@section('content')
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <a href="{{ route('orders.index') }}">
        Đặt món order #{{ $order->id }}
      </a>
    </h1>
  </div>

  <!-- Order Information -->
  <div class="card mb-3">
    <div class="card-body">
      <p><strong>User:</strong> {{ $order->user->name ?? 'Guest' }}</p>
      <p><strong>Table:</strong> {{ $order->table->name }}</p>
      <p><strong>Status:</strong> {{ $order->paid ? 'Paid' : 'Unpaid' }}</p>
      <p><strong>Discount:</strong> {{ $order->discount }}%</p>
    </div>
  </div>

  <div class="card mb-3 d-none">
    <div class="card-body">
      <!-- Form chỉnh sửa -->
      <form action="{{ route('orders.update', $order) }}" method="POST" class="d-inline">
        @method('PATCH')
        @csrf

        <div class="mb-3">
          <label for="table_id" class="form-label">
            Bàn
          </label>
          <select
            name="table_id"
            id="table_id"
            class="form-control @error('table_id') is-invalid @enderror"
            required>
            @foreach($tables as $table)
                <option value="{{ $table->id }}"
                  @if (isset($order) && old('table_id', $order->table_id) == $table->id)
                    selected
                  @endif>
                    {{ $table->name }}
                </option>
            @endforeach
          </select>
          @error('table_id')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="user_id" class="form-label">
            Khách
          </label>
          <select
            name="user_id"
            id="user_id"
            class="form-control @error('user_id') is-invalid @enderror"
            >
            <option value="">Khách vãng lai</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                  @if (isset($order) && old('user_id', $order->user_id) == $user->id)
                    selected
                  @endif>
                    {{ $user->name }}
                </option>
            @endforeach
          </select>
          @error('user_id')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="discount" class="form-label">
            Giảm giá
          </label>
          <input type="number" name="discount" id="discount"
            class="form-control @error('discount') is-invalid @enderror"
            @isset($order)
              value="{{ old('discount', $order->discount) }}"
            @endisset
            value="{{ old('discount', '0') }}">
          @error('discount')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="form-group">
          <div class="custom-control custom-checkbox">
              <input type="checkbox" name="paid" id="paid" value="1"
                @if ($order->paid)
                  checked
                @endif
                class="custom-control-input">
              <label class="custom-control-label" for="paid">Đã thanh toán</label>
          </div>
          @error('paid')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>
        <!-- Nút hành động -->
        <button type="submit" class="btn btn-primary">Ok</button>
      </form>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form action="{{ route('orders.addDetail', $order->id) }}" method="POST">
        @csrf

        <!-- Food Item Selection -->
        <div class="mb-3">
          <label for="food_item_id" class="form-label">
            Món
          </label>
          <select name="food_item_id" id="food_item_id"
            class="form-control @error('food_item_id') is-invalid @enderror"
            required>
            <option value="">Select a food item</option>
            @foreach ($foodItems as $item)
              <option value="{{ $item->id }}"
                @if (isset($order) && old('food_item_id', $order->food_item_id) == $item->id) selected @endif>
                {{ $item->name }} - {{ number_format($item->price) }}
              </option>
            @endforeach
          </select>
          @error('food_item_id')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Quantity Input -->
        <div class="mb-3">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" name="quantity" id="quantity"
            class="form-control" min="1" value="{{ old('quantity', 1) }}"
            required>
          @error('quantity')
            <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Add</button>
      </form>
    </div>
  </div>

  <!-- Order Items -->
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Order Items</h5>
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Food Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->orderDetails as $index => $detail)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $detail->foodItem->name }}</td>
              <td>{{ number_format($detail->price, 0) }} VND</td>
              <td>{{ $detail->quantity }}</td>
              <td>{{ number_format($detail->price * $detail->quantity, 0) }} VND
              </td>
              <td>
                <form action="{{ route('order-details.updateStatus', $detail) }}"
                  method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <select name="status" onchange="this.form.submit()"
                    class="form-control form-control-sm d-inline @error('status') is-invalid @enderror" style="width: fit-content;">
                    <option value="chuẩn bị"
                      {{ $detail->status == 'chuẩn bị' ? 'selected' : '' }}>Chuẩn
                      bị</option>
                    <option value="đã nấu"
                      {{ $detail->status == 'đã nấu' ? 'selected' : '' }}>Đã nấu
                    </option>
                    <option value="đã ra"
                      {{ $detail->status == 'đã ra' ? 'selected' : '' }}>Đã ra
                    </option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </form>
                <form action="{{ route('order-details.destroy', $detail) }}" method="POST"
                  class="d-inline">
                  @csrf
                  @method('DELETE')
                  
                  <button type="submit" class="btn btn-sm btn-danger" {{ $detail->status == 'đã ra' ? 'disabled' : '' }}>
                    Hủy
                  </button>
              </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Order Total -->
  <div class="mt-4">
    <h5>
      Tổng:
      {{ number_format($order->total, 0, '.', ',') }}₫
    </h5>
    <h5>
      Giảm:
      {{ number_format($order->discount, 0, '', ' ') }}%
    </h5>
    <h5>
      Cần thanh toán
      {{ number_format($order->total / 100 * (100 - $order->discount), 0, '.', ',') }}₫
    </h5>
  </div>

  <!-- Back Button -->
  <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Back to
    Orders</a>
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
  <script>
    @foreach ($errors->all() as $error)
      console.warn(`{{ $error }}`)
    @endforeach
  </script>
@endsection
