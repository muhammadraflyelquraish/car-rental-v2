<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

use Yajra\DataTables\DataTables;
use App\Models\Order;
use Illuminate\Http\Request;
// use App\Models\OrderStatus;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    function index(): View
    {
        return view('admin.order.index');
    }

    function create(): JsonResponse
    {
        $query = Order::with(['car', 'user', 'payment']);
        $query->when(request('order_number'), function ($query) {
            $query->where('order_number', '=', request('order_number'));
        });
        $query->when(request('customer'), function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('fullname', 'like', "%" . request('customer') . "%");
            });
        });
        $query->when(request('car'), function ($query) {
            $query->whereHas('car', function ($q) {
                $q->where('name', 'like', "%" . request('car') . "%")->orWhere('number_plate', 'like', "%" . request('car') . "%");
            });
        });
        $query->when(request('start_date'), function ($query) {
            $query->whereDate('start_date', '>=', request('start_date'));
        });
        $query->when(request('end_date'), function ($query) {
            $query->whereDate('end_date', '<=', request('end_date'));
        });
        $query->when(request('order_status'), function ($query) {
            $query->where('order_status', request('order_status'));
        });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<a class="btn btn-sm btn-success" href=' . route('order.show', $row->id) . '><i class="fa fa-eye"></i></a>';
                if ($row->order_status != 'Canceled' && $row->order_status != 'On Going' && $row->order_status != 'Finished') {
                    $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-times"></i></button>';
                }
                $button .= '</div>';
                return $button;
            })
            ->editColumn('user.fullname', function ($row) {
                return '
                    <span>' . $row->user->fullname . '</span> <br>
                    <small>' . $row->user->customer->phone_number . '</small>
                ';
            })
            ->editColumn('car.name', function ($row) {
                return '
                    <span>' . $row->car->name . '</span> <br>
                    <small>' . $row->car->number_plate . '</small>
                ';
            })
            ->editColumn('start_date', function ($row) {
                return date('d M Y', strtotime($row->start_date)) . ' ~ ' . date('d M Y', strtotime($row->end_date)) . ' | ' . date('H:i', strtotime($row->pickup_time));
            })
            ->editColumn('pickup_location', function ($row) {
                if ($row->pickup_location == "") {
                    return 'Tempat Rental';
                }
                return $row->pickup_location;
            })
            ->editColumn('dropoff_location', function ($row) {
                if ($row->dropoff_location == "") {
                    return 'Tempat Rental';
                }
                return $row->dropoff_location;
            })
            ->editColumn('order_status', function ($row) {
                if ($row->order_status == 'Waiting For Payment') {
                    return '<span class="label label-secondary">' . $row->order_status . '</span>';
                } else if ($row->order_status == 'Waiting For Pickup') {
                    return '<span class="btn btn-sm btn-warning">' . $row->order_status . '</span> <button class="btn btn-sm btn-success" id="ongoing" data-url="' . route('order.ongoing', $row->id) . '"><i class="fa fa-car"></i></button>';
                } else if ($row->order_status == 'On Going') {
                    return '<span class="btn btn-sm btn-success">' . $row->order_status . '</span> <button class="btn btn-sm btn-danger" id="finish" data-url="' . route('order.finish', $row->id) . '"><i class="fa fa-car"></i></button>';
                } else if ($row->order_status == 'Finished') {
                    return '<span class="label label-primary">' . $row->order_status . '</span>';
                } else {
                    return '<span class="label label-danger">' . $row->order_status . '</span>';
                }
            })
            ->rawColumns(['action', 'user.fullname', 'car.name', 'start_date', 'order_status'])
            ->toJson();
    }

    function show(Order $order)
    {
        $order = $order
            ->load('car')
            ->load('user')
            ->load('payment');

        return view('admin.order.detail', compact('order'));
    }

    function ongoingOrder(Order $order)
    {
        $order->update([
            'order_status' => 'On Going'
        ]);
    }

    function finishOrder(Order $order)
    {
        $order->update([
            'order_status' => 'Finished'
        ]);
    }

    function destroy(Order $order)
    {
        $order->update([
            'order_status' => 'Canceled'
        ]);
    }

    public function export(Request $request)
    {
        $time = date('dMY-His');
        return Excel::download(new OrderExport($request), 'order-' . $time . '.xlsx');
    }
}
