@extends('layouts.admin')

@section('title', 'Update Products')
@section('content-header', 'Update Products')
@section('content-actions')
    <a href="{{route('products.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Products</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="Name" value="{{ old('name', $product->name) }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>


                <div class="form-group">
                    <label for="unit">Unit</label>
                    <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror"
                           id="unit"
                           placeholder="unit" value="{{ old('unit', $product->unit) }}">
                    @error('unit')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

                <div class="form-group">
                    <label for="measurement">Measurement</label>
                    <input type="number" name="measurement" class="form-control @error('measurement') is-invalid @enderror"
                           id="measurement"
                           placeholder="Measurement" value="{{ old('measurement', $product->measurement) }}">
                    @error('measurement')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           id="price"
                           placeholder="price" value="{{ old('price', $product->price) }}">
                    @error('price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

            
                <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
