<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
// use App\Models\DriverStatus;
use App\Models\Order;
// use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use DateTime;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    function home()
    {
        $latestCars = Car::with('brand', 'images')
            ->latest()
            ->take(4)
            ->get();

        $customer = "";
        $activeOrder = [];
        if (auth()->user()) {
            $activeOrder = Order::with('car', 'user', 'payment')
                ->where('user_id', auth()->user()->id)
                ->whereIn('order_status', [
                    'Waiting For Payment',
                    'Waiting For Pickup',
                    'On Going'
                ])
                ->latest()
                ->first();

            $customer = Customer::where('user_id', auth()->user()->id)->first();
        }

        $rentedCars = Order::whereIn('order_status', [
            'Waiting For Payment',
            'Waiting For Pickup',
            'On Going'
        ])
            ->get();

        return view('landing.home', compact('latestCars', 'activeOrder', 'rentedCars', 'customer'));
    }

    function aboutUs()
    {
        return view('landing.about-us');
    }

    function listCars()
    {
        $cars = Car::orderBy('created_at', 'desc')
            ->with('brand', 'images', 'accessories')
            ->paginate(12);

        $rentedCars = Order::with('car')
            ->whereIn('order_status', [
                'Waiting For Payment',
                'Waiting For Pickup',
                'On Going'
            ])
            ->get();

        return view('landing.list-cars', compact('cars', 'rentedCars'));
    }

    function carDetail($carId)
    {
        $car = Car::find($carId)
            ->load('brand')
            ->load('images')
            ->load('accessories');

        $drivers = Driver::where('status', 'Active')->get();

        $payment_methods = PaymentMethod::get();

        return view('landing.car-details', compact('car', 'payment_methods', 'drivers'));
    }

    function contactUs()
    {
        return view('landing.contact-us');
    }

    function getImage(Request $request)
    {
        $imageData = file_get_contents($request->query('url'));
        return response($imageData, 200)->header('Content-Type', 'image/jpeg');
    }
}
