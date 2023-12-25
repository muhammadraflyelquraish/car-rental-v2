<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;



class OrderExport implements FromView
{

    protected $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $order = Order::with(['car', 'user', 'payment']);
        $order->when($this->request->has('order_number'), function ($query) {
            $query->where('order_number', '=', $this->request->get('order_number'));
        });
        $order->when($this->request->has('customer'), function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('fullname', 'like', "%" . $this->request->get('customer') . "%");
            });
        });
        $order->when($this->request->has('car'), function ($query) {
            $query->whereHas('car', function ($q) {
                $q->where('name', 'like', "%" . $this->request->get('car') . "%")->orWhere('number_plate', 'like', "%" . $this->request->get('car') . "%");
            });
        });
        $order->when($this->request->has('start_date'), function ($query) {
            $query->whereDate('start_date', '>=', $this->request->get('start_date'));
        });
        $order->when($this->request->has('end_date'), function ($query) {
            $query->whereDate('end_date', '<=', $this->request->get('end_date'));
        });
        $order->when($this->request->has('order_status'), function ($query) {
            $query->where('order_status', $this->request->get('order_status'));
        });

        return view('exports.order', [
            'orders' => $order->get()
        ]);
    }
}
