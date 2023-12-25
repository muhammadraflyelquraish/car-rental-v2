<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
// use App\Models\DriverStatus;


use Yajra\DataTables\DataTables;
use App\Models\Driver;
use Google\Cloud\Storage\StorageClient;

class DriverController extends Controller
{
    function index(): View
    {
        return view('admin.driver.index');
    }

    function create(): View
    {
        return view('admin.driver.create');
    }

    function getData(): JsonResponse
    {
        $brands = Driver::query();
        return DataTables::of($brands)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<a href="' . route('driver.show', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                // $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('ktp_image', function ($row) {
                return '<a href="' . $row->ktp_image . '" target="_blank">Lihat KTP</a>';
            })
            ->editColumn('sim_image', function ($row) {
                return '<a href="' . $row->sim_image . '" target="_blank">Lihat SIM</a>';
            })
            ->rawColumns(['action', 'ktp_image', 'sim_image'])
            ->toJson();
    }

    function store(Request $request)
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

        Driver::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'ktp_image' => $ktpLink,
            'sim_image' => $simLink,
            'status' => 'Active',
        ]);

        return redirect()->route('driver.index')->with('success', 'Driver berhasil ditambahkan');
    }

    function show($id)
    {
        $driver = Driver::findOrFail($id);
        return view('admin.driver.edit', compact('driver'));
    }

    function update(Request $request, Driver $driver)
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

        $driver->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'ktp_image' => $ktpLink ? $ktpLink : $driver->ktp_image,
            'sim_image' => $simLink ? $simLink : $driver->sim_image,
            'status' => $request->status,
        ]);

        return redirect()->route('driver.index')->with('success', 'Data driver berhasil diubah');
    }

    function destroy($id): JsonResponse
    {
        try {
            Driver::findOrFail($id)->delete();
            return response()->json(['res' => 'success'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
