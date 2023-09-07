<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Notifications\AccountCreatedNotification;
use App\Models\User;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $paginate = request('paginate', 10);
        $search = request('search', null);

        $query = User::query()->with('user_contact')->where('role', 1);

        if ($search) {
            $query->select('users.id as user_id', 'users.*', 'user_contacts.id as contact_id', 'user_contacts.*')
                ->leftjoin('user_contacts', 'users.id', '=', 'user_contacts.user_id')->where('isCurrentLocation', 1)
                ->where(function ($q) use ($search) {
                    $q->where('user_contacts.province', 'LIKE', "%$search%")
                        ->orWhere('user_contacts.city', 'LIKE', "%$search%")
                        ->orWhere('user_contacts.barangay', 'LIKE', "%$search%")
                        ->orWhere('users.name', 'LIKE', "%$search%")
                        ->orWhere('users.email', 'LIKE', "%$search%");
                });
        }

        $customers = $query->latest()->paginate($paginate);


        return response()->json($customers, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = User::create([
            'name' => $request->firstname . ' ' . $request->lastname,
            'email' => $request->email,
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make($request->password)
        ]);



        // Send account information to user's email
        $user->notify(new AccountCreatedNotification($user, $request->password));

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Create customer',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Customer added'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'sometimes|nullable|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($id);


        $user->update([
            'name' => $request->firstname.' '.$request->lastname,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Update',
            'activity_description' => 'Update customer id'.' '.$id,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);



        return response()->json(['message' => 'Customer updated'], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) :JsonResponse
    {
        $user = User::find($id);
        $user->delete();

        return response()->json(['message' => 'Customer deleted'], 200);
    }
}