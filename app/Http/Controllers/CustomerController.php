<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

use Yajra\DataTables\DataTables;
use App\Models\Customer;
use Google\Cloud\Storage\StorageClient;

class CustomerController extends Controller
{
    function index(): View
    {
        return view('admin.customer.index');
    }

    function create(): JsonResponse
    {
        $customers = Customer::with('user');
        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<a href="' . route('customer.show', $row->id) . '" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('status_approval', function ($row) {
                if ($row->status_approval == 'On Procces') {
                    return '<span class="label label-secondary">' . $row->status_approval . '</span>';
                } else if ($row->status_approval == 'Approved') {
                    return '<span class="label label-primary">' . $row->status_approval . '</span>';
                } else if ($row->status_approval == 'Rejected') {
                    return '<span class="label label-danger">' . $row->status_approval . '</span>';
                }
            })
            ->rawColumns(['action', 'status_approval'])
            ->toJson();
    }

    function show(Customer $customer)
    {
        $customer = $customer->load('user');
        return view('admin.customer.detail', compact('customer'));
    }

    function update(Request $request, Customer $customer)
    {
        $ktpLink = "";
        $simLink = "";

        $googleConfigFile = file_get_contents(base_path('/car-rental-408623-1173b036a196.json'));
        $storage = new StorageClient([
            'keyFile' => json_decode($googleConfigFile, true)
        ]);
        $storageBucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
        $bucket = $storage->bucket($storageBucketName);

        if ($request->file('ktp_image')) {
            //    <-- save ktp -->
            $ktpFile = $request->file('ktp_image');
            $ktpFolder = 'ktp';
            $ktpStoragePath = $ktpFolder . '/' . time() . "." . $ktpFile->getClientOriginalExtension();
            $bucket->upload(file_get_contents($ktpFile), [
                'name' => $ktpStoragePath
            ]);
            $ktpLink = 'https://storage.googleapis.com/' . $storageBucketName . '/' . $ktpStoragePath;
        }

        if ($request->file('sim_image')) {
            $simFile = $request->file('sim_image');
            $simFolder = 'sim';
            $simStoragePath = $simFolder . '/' . time() . "." . $simFile->getClientOriginalExtension();
            $bucket->upload(file_get_contents($simFile), [
                'name' => $simStoragePath
            ]);
            $simLink = 'https://storage.googleapis.com/' . $storageBucketName . '/' . $simStoragePath;
        }

        $customer->update([
            'fullname' => $request->fullname,
            'address' => $request->address,
            'status_approval' => $request->status_approval,
            'ktp_image' => $ktpLink ? $ktpLink : $customer->ktp_image,
            'sim_image' => $simLink ? $simLink : $customer->sim_image,
            'note' => $request->status_approval == 'Approved' ? '' : $request->note,
        ]);

        $customer->user->update([
            'fullname' => $request->fullname
        ]);

        return redirect()->route('customer.index')->with('success', 'Data customer berhasil diubah');
    }
}
