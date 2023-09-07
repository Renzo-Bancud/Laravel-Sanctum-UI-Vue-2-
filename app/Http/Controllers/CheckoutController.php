<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User_contact;

class CheckoutController extends Controller
{
    protected $dateTime;

    
    public function __construct()
    {
        $now = Carbon::now('Asia/Manila');
        $this->dateTime = $now->toDateTimeString();
    }

    public function delivery_information(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'mobile' => 'required|regex:/^09\d{9}$/',
            'houseDetails' => 'required',
            'landmark' => 'required',
            'selectedProvince' => 'required',
            'selectedMunicipality' => 'required',
            'selectedBarangay' => 'required',
        ], [
            'selectedProvince.required' => 'The province field is required.',
            'selectedMunicipality.required' => 'The municipality field is required.',
            'selectedBarangay.required' => 'The barangay field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        User_contact::create([
            'user_id' => Auth::user()->id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'mobile_number' => $request->mobile,
            'house_no' => $request->houseDetails,
            'province' => $request->selectedProvince,
            'city' => $request->selectedMunicipality,
            'barangay' => $request->selectedBarangay,
            'landmark' => $request->landmark,
            'to_deliver' => $request->selectedLocation,
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Delivery address information',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);


        return response()->json(['message' => 'Delivery information added'], 200);

    }



    public function billing_address($uid)
    {
        $user_contact = User_contact::where('user_id', $uid)->where('isCurrentLocation', 1)->first();
        return response()->json($user_contact, 200);
    }

    public function edit_delivery_information(Request $request, $contact_id)
    {

        $validator = Validator::make($request->all(), [
            'houseDetails' => 'required',
            'landmark' => 'required',
            'selectedProvince' => 'required',
            'selectedMunicipality' => 'required',
            'selectedBarangay' => 'required',
            'selectedLocation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        User_contact::find($contact_id)->update([
            'house_no' => $request->houseDetails,
            'landmark' => $request->landmark,
            'province' => $request->selectedProvince,
            'city' => $request->selectedMunicipality,
            'barangay' => $request->selectedBarangay,
            'to_deliver' => $request->selectedLocation,
            'updated_at' => $this->dateTime,
        ]);

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Edit',
            'activity_description' => 'Update delivery address information',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        return response()->json(['message' => 'Shipping address updated'], 200);
    }

}