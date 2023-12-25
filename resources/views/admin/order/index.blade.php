@extends('admin.layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Pemesanan</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row m-b-sm m-t-sm">
                        <div class="col-md-2">
                            <label for="">Nomor Order</label>
                            <input type="text" name="order_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label for="">Customer</label>
                            <input type="text" name="customer" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label for="">Mobil</label>
                            <input type="text" name="car" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label for="">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label for="">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label for="">Status</label>
                            <select name="order_status" id="order_status" class="form-control form-control-sm">
                                <option value="" selected>Pilih Status</option>
                                <option value="Waiting For Payment">Waiting For Payment</option>
                                <option value="Waiting For Pickup">Waiting For Pickup</option>
                                <option value="On Going">On Going</option>
                                <option value="Finished">Finished</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                        </div>
                        <div class="col-md-1" style="margin-top: 28px;">
                            <button class="btn btn-sm btn-primary" id="btnFilter"><i class="fa fa-filter mr-1"></i>Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox-title">
                <h5><a class="btn btn-primary btn-sm" href="{{ route('order.export') }}" id="export" target="_blank"><i class="fa fa-plus-square mr-1"></i> Export Pemesanan</a></h5>
            </div>
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover driverTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="1px">No</th>
                                    <th>Nomor Order</th>
                                    <th>Customer</th>
                                    <th>Mobil</th>
                                    <th>Masa Rental</th>
                                    <th>Lokasi Pengambilan</th>
                                    <th>Lokasi Pengembalian</th>
                                    <th>Status</th>
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        //BASE 
        let ladda = $('.ladda-button-demo').ladda();

        function LaddaStart() {
            ladda.ladda('start');
        }

        function LaddaAndDrawTable() {
            ladda.ladda('stop');
            serverSideTable.draw()
        }

        function sweetalert(title, msg, type, timer = 60000, confirmButton = true) {
            swal({
                title: title,
                text: msg,
                type: type,
                timer: timer,
                showConfirmButton: confirmButton
            });
        }

        //TEMPLATES 
        let serverSideTable = $('.driverTable').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, 'asc']
            ],
            ajax: {
                url: "{{ route('order.create') }}",
                type: "GET",
                data: function(d) {
                    d.order_number = $('input[name="order_number"]').val()
                    d.customer = $('input[name="customer"]').val()
                    d.car = $('input[name="car"]').val()
                    d.start_date = $('input[name="start_date"]').val()
                    d.end_date = $('input[name="end_date"]').val()
                    d.order_status = $('select[name="order_status"]').val()
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false,
            }, {
                data: 'order_number',
                name: 'order_number'
            }, {
                data: 'user.fullname',
                name: 'user.fullname'
            }, {
                data: 'car.name',
                name: 'car.name'
            }, {
                data: 'start_date',
                name: 'start_date'
            }, {
                data: 'pickup_location',
                name: 'pickup_location'
            }, {
                data: 'dropoff_location',
                name: 'dropoff_location'
            }, {
                data: 'order_status',
                name: 'order_status'
            }, {
                data: 'action',
                name: 'action',
                searchable: false,
                orderable: false
            }],
            search: {
                "regex": true
            }
        });

        $('#btnFilter').click(function(e) {
            e.preventDefault()

            let exportUrl = "{{ route('order.export') }}"
            let currentUrl = new URL(exportUrl);
            let searchParams = new URLSearchParams(currentUrl);
            let order_number = $('input[name="order_number"]').val()
            let customer = $('input[name="customer"]').val()
            let car = $('input[name="car"]').val()
            let start_date = $('input[name="start_date"]').val()
            let end_date = $('input[name="end_date"]').val()
            let order_status = $('select[name="order_status"]').val()

            if (order_number) {
                searchParams.set("order_number", order_number);
            }
            if (customer) {
                searchParams.set("customer", customer);
            }
            if (car) {
                searchParams.set("car", car);
            }
            if (start_date) {
                searchParams.set("start_date", start_date);
            }
            if (end_date) {
                searchParams.set("end_date", end_date);
            }
            if (order_status) {
                searchParams.set("order_status", order_status);
            }

            let newUrl = currentUrl + '?' + searchParams.toString();

            $('#export').attr('href', newUrl)

            serverSideTable.draw();
        })

        $(document).on('click', '#delete', function(e) {
            let id = $(this).data('integrity')
            let name = $(this).closest('tr').find('td:eq(1)').text()
            swal({
                title: "Batalkan?",
                text: `Order "${name}" akan dibatalkan!`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, hapus!",
                closeOnConfirm: false
            }, function() {
                swal.close()
                $.ajax({
                    url: "{{ route('order.store') }}/" + id,
                    type: "DELETE",
                    dataType: 'json',
                    success: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', `Data berhasil dibatalkan.`, null, 500, false)
                    },
                    error: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Tidak terhapus!', 'Terjadi kesalahan saat mengubah data.', 'error')
                    }
                })
            });
        })

        $(document).on('click', '#ongoing', function(e) {
            let url = $(this).data('url')
            let name = $(this).closest('tr').find('td:eq(1)').text()
            swal({
                title: "On going?",
                text: `Order "${name}" akan diubah menjadi ongoing!`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1c84c6",
                confirmButtonText: "Ya, Ubah!",
                closeOnConfirm: false
            }, function() {
                swal.close()
                $.ajax({
                    url: url,
                    type: "PUT",
                    dataType: 'json',
                    success: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', `Status order berhasil diubah.`, null, 500, false)
                    },
                    error: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Tidak terhapus!', 'Terjadi kesalahan saat mengubah status.', 'error')
                    }
                })
            });
        })

        $(document).on('click', '#finish', function(e) {
            let url = $(this).data('url')
            let name = $(this).closest('tr').find('td:eq(1)').text()
            swal({
                title: "Selesai Order?",
                text: `Order "${name}" akan diubah menjadi selesai!`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1c84c6",
                confirmButtonText: "Ya, Selesai!",
                closeOnConfirm: false
            }, function() {
                swal.close()
                $.ajax({
                    url: url,
                    type: "PUT",
                    dataType: 'json',
                    success: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', `Status order berhasil diubah.`, null, 500, false)
                    },
                    error: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Tidak terhapus!', 'Terjadi kesalahan saat mengubah status.', 'error')
                    }
                })
            });
        })

    });
</script>
@endpush