<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
// use App\Models\StatusApproval;
use App\Models\User;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{

    public function create(): View
    {
        return view('auth.register');
    }

    function updateDocument(): View
    {
        return view('auth.update-document');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'max:20', 'unique:' . Customer::class],
            'address' => ['required', 'string', 'max:255'],
            'ktp_image' => ['required', 'image'],
            'sim_image' => ['required', 'image'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'fullname' => $request->fullname,
                'username' => Str::random(15),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Customer'
            ]);

            $googleConfigFile = file_get_contents(base_path('/car-rental-408623-1173b036a196.json'));
            $storage = new StorageClient([
                'keyFile' => json_decode($googleConfigFile, true)
            ]);
            $storageBucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            $bucket = $storage->bucket($storageBucketName);


            //    <-- save ktp -->
            $ktpFile = $request->file('ktp_image');
            $ktpFolder = 'ktp';
            $ktpStoragePath = $ktpFolder . '/' . time() . "." . $ktpFile->getClientOriginalExtension();
            $bucket->upload(file_get_contents($ktpFile), [
                'name' => $ktpStoragePath
            ]);
            $ktpLink = 'https://storage.googleapis.com/' . $storageBucketName . '/' . $ktpStoragePath;

            //    <-- save sim -->
            $simFile = $request->file('sim_image');
            $simFolder = 'sim';
            $simStoragePath = $simFolder . '/' . time() . "." . $simFile->getClientOriginalExtension();
            $bucket->upload(file_get_contents($simFile), [
                'name' => $simStoragePath
            ]);
            $simLink = 'https://storage.googleapis.com/' . $storageBucketName . '/' . $simStoragePath;

            Customer::create([
                'user_id' => $user->id,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'ktp_image' => $ktpLink,
                'sim_image' => $simLink,
                'status_approval' => 'On Procces',
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil, mohon tunggu approval dokumen kamu');
    }

    public function update(Request $request, $userId): RedirectResponse
    {
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $customer = Customer::where('user_id', $userId)->first();
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
            'address' => $request->address,
            'status_approval' => 'On Process',
            'ktp_image' => $ktpLink ? $ktpLink : $customer->ktp_image,
            'sim_image' => $simLink ? $simLink : $customer->sim_image,
        ]);

        $customer->user->update([
            'fullname' => $request->fullname
        ]);

        return redirect()->route('home')->with('success', 'Update berhasil, mohon tunggu approval dokumen kamu');
    }
}
