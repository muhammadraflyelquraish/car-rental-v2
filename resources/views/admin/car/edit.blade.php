@extends('admin.layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Ubah Mobil</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('car.index') }}"><u>Mobil</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Ubah</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Data Mobil</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('car.update', $car->id) }}" method="POST" id="formCar" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class=" row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nama Mobil</label>
                                    <input type="text" name="name" class="form-control" value="{{ $car->name }}" required placeholder="Contoh: Avanza">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Plat Nomor</label>
                                    <input type="text" name="number_plate" class="form-control" value="{{ $car->number_plate }}" required placeholder="Contoh: Z 1234 AB">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Brand</label>
                            <select name="brand_id" id="brand_id" class="form-control" required>
                                <option value="" selected disabled>Pilih Brand</option>
                                @foreach($brands as $id => $brand)
                                @if($id == $car->brand_id)
                                <option value="{{ $id }}" selected>{{ $brand }}</option>
                                @else
                                <option value="{{ $id }}">{{ $brand }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tahun Keluar</label>
                                    <input type="number" class="form-control" name="launch_year" min="1900" max="2099" step="1" value="{{ $car->launch_year }}" required placeholder="Contoh: 1990">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jarak Tempuh (KM)</label>
                                    <input type="number" class="form-control" name="mileage" value="{{ $car->mileage }}" required placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Transmisi</label>
                                    <select name="transmission" class="form-control" required>
                                        @if($car->transmission == 'Manual')
                                        <option value="Manual" selected>Manual</option>
                                        <option value="Automatic">Otomatis</option>
                                        @else
                                        <option value="Manual">Manual</option>
                                        <option value="Automatic" selected>Otomatis</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bahan Bakar</label>
                                    <select name="fuel_type" class="form-control" required>
                                        @if($car->fuel_type == 'Petrol')
                                        <option value="Petrol" selected>Bensin</option>
                                        <option value="Electric">Listrik</option>
                                        @else
                                        <option value="Petrol">Bensin</option>
                                        <option value="Electric" selected>Listrik</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tempat Duduk</label>
                                    <input type="number" class="form-control" name="number_of_seat" value="{{ $car->number_of_seat }}" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga per Hari (Rp)</label>
                                    <input type="number" class="form-control" name="price_per_day" value="{{ $car->price_per_day }}" placeholder="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" id="description" cols="30" rows="2" class="form-control">{{ $car->description }}</textarea>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <h5>Aksesoris</h5>

                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Fitur</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($car->accessories as $i => $accessories)
                                <tr>
                                    <td>{{ $accessories->name }}</td>
                                    <td class="text-center">
                                        <input type="hidden" class="form-control" name="accessories_name[]" value="{{ $accessories->name }}">
                                        @if($accessories->is_featured)
                                        <input type="hidden" name="accessories_value[]" value="{{ $accessories->is_featured }}" checked><input type="checkbox" checked onclick="this.previousSibling.value=1-this.previousSibling.value">
                                        @else
                                        <input type="hidden" name="accessories_value[]" value="{{ $accessories->is_featured }}"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="hr-line-dashed"></div>

                        <h5>Gambar Mobil</h5>
                        <button type="button" class="btn btn-primary btn-xs mb-3" id="addImage"><i class="fa fa-plus"></i> gambar</button>

                        <div class="imageList">
                            @foreach($car->images as $image)
                            <div class="form-group">
                                <label>Gambar {{ $loop->iteration }}</label>
                                <input type="file" name="images[]" class="form-control">

                                <a href="{{ asset('cars') }}/{{$image->url}}" target="_blank" data-gallery="">
                                    <img src="{{ asset('cars') }}/{{$image->url}}" width="150" height="150">
                                </a>
                            </div>
                            @endforeach
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary float-right" type="submit"><i class="fa fa-save"></i> Ubah</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $(document).on('click', '#addImage', function(e) {
            let elementImage = $('#formCar').find('.imageList')
            let iteration = elementImage.find('div').length

            elementImage.append(
                `<div class="form-group">
                    <label>Gambar ${iteration+1}</label>
                    <input type="file" name="images[]" class="form-control">
                </div>`
            )
        })
    });
</script>
@endpush