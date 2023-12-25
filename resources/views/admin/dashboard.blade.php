@extends('admin.layouts.master')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Order</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($totalOrder, 0, ',', ' '); }}</h1>
                    <small>Total Order</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Mobil</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($totalCar, 0, ',', ' '); }}</h1>
                    <small>Total Mobil</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Driver</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($totalDriver, 0, ',', ' '); }}</h1>
                    <small>Total Driver</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><small>(Order)&nbsp;Sedang&nbsp;Berjalan</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($activeOrder, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><small>(Order)&nbsp;Menunggu&nbsp;Pembayaran</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($waitingOrder, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><small>(Mobil)&nbsp;Disewaan</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($activeCar, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><small>(Mobil)&nbsp;Tersedia</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($availableCar, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><small>(Driver)&nbsp;Aktif</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($activeDriver, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><small>(Driver)&nbsp;Nonactive</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($nonActiveDriver, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><small>(Order)&nbsp;Menunggu&nbsp;Pengambilan</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($pickupOrder, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><small>(Order)&nbsp;Dibatalkan</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($cancelOrder, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Customer</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($totalCustomer, 0, ',', ' '); }}</h1>
                    <small>Total Customer</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>User</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($totalUser, 0, ',', ' '); }}</h1>
                    <small>Total User</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><small>(Customer)&nbsp;Approved</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($approvedCustomer, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><small>(Customer)&nbsp;Menunggu&nbsp;Persetujuan</small></h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($waitingCustomer, 0, ',', ' '); }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection