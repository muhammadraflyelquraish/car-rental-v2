<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        // Order
        $totalOrder = Order::count();
        $activeOrder = Order::where('order_status', 'On Going')->count();
        $waitingOrder = Order::where('order_status', 'Waiting For Payment')->count();
        $pickupOrder = Order::where('order_status', 'Waiting For Pickup')->count();
        $cancelOrder = Order::where('order_status', 'Canceled')->count();

        // Car
        $totalCar = Car::count();
        $activeCar = Order::whereIn('order_status', ['On Going', 'Waiting For Payment', 'Waiting For Pickup'])->count();
        $availableCar = $totalCar - $activeCar;

        // Driver
        $totalDriver = Driver::count();
        $activeDriver = Driver::where('status', 'Active')->count();
        $nonActiveDriver = Driver::where('status', 'Nonactive')->count();

        // Customer
        $totalCustomer = Customer::count();
        $approvedCustomer = Customer::where('status_approval', 'Approved')->count();
        $waitingCustomer = Customer::where('status_approval', 'On Procces')->count();

        // User
        $totalUser = User::count();

        return view('admin.dashboard', compact(
            'totalOrder',
            'activeOrder',
            'waitingOrder',
            'pickupOrder',
            'cancelOrder',
            'totalCar',
            'activeCar',
            'availableCar', 
            'totalDriver',
            'activeDriver',
            'nonActiveDriver',
            'totalCustomer',
            'approvedCustomer',
            'waitingCustomer',
            'totalUser',
        ));
    }
}
