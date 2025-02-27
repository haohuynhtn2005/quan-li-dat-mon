@extends('layouts.dash')

@section('head')
  <title></title>
@endsection

@section('content')
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <a href="{{ route('orders.index') }}">
        @if ($mode == 'create')
          Thêm
        @elseif ($mode == 'update')
          Sửa
        @else
          Xóa
        @endif
        đơn hàng
      </a>
    </h1>
  </div>
  <div class="card">
    <div class="card-body">

      @php
        $form;
        switch ($mode) {
          case 'update':
            $form = [
              'action' => route('orders.update', $order),
              'method' => 'PATCH',
            ];
            break;
          default:
            $form = [
              'action' => route('orders.store'),
              'method' => 'POST',
            ];
            break;
        }
      @endphp
      <!-- Form chỉnh sửa -->
      <form action="{{ $form['action'] }}" method="POST">
      @method($form['method'])
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
          required>
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


      @if ($mode == 'update')
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
      @endif

      <!-- Nút hành động -->
      <button type="submit" class="btn btn-primary">Ok</button>
      <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        Quay lại
      </a>
      </form>
    </div>
  </div>
@endsection

@section('script')
<script>
  @foreach ($errors->all() as $error)
    console.warn(`{{ $error }}`)
  @endforeach
</script>
@endsection
