@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
                @guest
                
                {{ redirect()->route('login')->send() }}

                @else

                <div class="col-12 my-3">
                @foreach(Auth::user()->stations as $key )
                {{ $key->name}}
                @endforeach
               
                {{-- {{ dd( Auth::user()->stations() ) }} --}}
                </div>
                <button type="button" class="btn mb-4 btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">+</button>
              
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">اضافة</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {{-- محتوي المودل --}}
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">اغلاق</button>
                        </div>
                    </div>
                    </div>
                </div>
                {{-- {{ dd(auth()->id()) }} --}}
              {{-- ============= form Task ==================== --}}
                <form action="{{ route('create.task') }}" method="POST">
                  @csrf
                    <div class="row mb-3 g-3">
                        <div class="col-3">
                          <input type="text" class="form-control" value="{{ old('ppm') }}" name="ppm" placeholder="ppm" aria-label="First name">
                        </div>
                        <div class="col-3">
                          <input type="number" class="form-control" value="{{ old('quantity') }}" name="quantity" placeholder="الكمية بالكيلو" aria-label="First name">
                        </div>
                        <div class="col-3">
                          <input type="number" class="form-control" value="{{ old('cont_hours') }}" name="cont_hours" placeholder="عدد الساعات" aria-label="First name">
                        </div>
                        <div class="col-3">
                          <input type="datetime-local" class="form-control" id="birthdaytime" value="{{ old('actual_time') }}" name="actual_time">
                        </div>
                        <div class="col-3">
                          <input type="text" class="form-control" value="{{ old('slices_ton') }}" name="slices_ton" placeholder="طن الشرائح" aria-label="Last name">
                        </div>
                        <div class="col-3">
                          <select class="form-select" name="shift" id="autoSizingSelect">
                            <option selected value="" disabled >الوردية</option>
                            <option value="am" {{ old('shift') == "am" ? 'selected' : '' }}>صباحية</option>
                            <option value="pm" {{ old('shift') == "pm" ? 'selected' : '' }}>ليلية</option>
                          </select>
                        </div>
                        <div class="col-3">
                          <input type="text" class="form-control" value="{{ old('name') }}" name="name" placeholder="الاسم" aria-label="Last name">
                        </div>
                        <div class="col-3">
                          <input type="text" class="form-control" value="{{ old('description') }}" name="description" placeholder="الوصف" aria-label="Last name">
                        </div>
                        <button type="submit" class="btn btn-primary w-25 mx-auto">حفظ</button>
                    </div>
                </form>
              {{-- ================================= --}}
                @endguest
        </div>
    </div>
</div>
@endsection
