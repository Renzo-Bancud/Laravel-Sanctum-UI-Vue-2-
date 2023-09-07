<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\User_contact;
use App\Models\Activitylog;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    use SendsPasswordResetEmails;

    public function update_user(Request $request)
    {
        $user = auth()->user();


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => "required|unique:users,email, $user->id",
            'password' => 'sometimes|nullable|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user->update([
            'name' => $request->name,
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
            'activity_description' => 'Update user account',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json($user, 200);
    }


    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verificationCode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $email = $request->query('email');
        $decryptedEmail = Crypt::decrypt($email);
        $user = User::where('email', $decryptedEmail)->first();
        $verification = implode('', $request->input('verificationCode'));

        if ($user && $user->email_verification_code == $verification) {
            $expirationTime = Carbon::parse($user->created_at)->addMinutes(30); // add minutes to date account created
            $currentTime = Carbon::now();

            if ($currentTime->lt($expirationTime)) { // lt() the lt means less than lte means less than equal this will execute it the current time is less than to expiration time
                $user->markEmailAsVerified();

                // Generate a new token
                $tokenResult = $user->createToken('authToken');
                $token = $tokenResult->plainTextToken;

                Activitylog::create([
                    'user_id' => auth::user()->id,
                    'activity_type' => 'Verification',
                    'activity_description' => 'Email Verification',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                ]);

                // Return the token in the response
                return Response::json(['call' => 'kKks*^M8x6tU04@p*k%pu0a2Ab9JSn', 200]);
            } else {
                return Response::json(['message' => 'Verification code has expired.'], 400);
            }
        }

        return Response::json(['message' => 'Invalid verification code.'], 400);
    }

    public function requestAnotherCode()
    {
        $user = Auth::user();
        if ($user->email_verified_at == null) {
            $verificationCode = $user->generateEmailVerificationCode();
            $user->sendEmailVerificationCode($verificationCode);
        }
        $user->created_at = Carbon::now('Asia/Manila');
        $user->save();

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Verification Code',
            'activity_description' => 'Another request of verification code',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);
    }

    public function getNewCode(Request $request)
    {
        $verificationCode = $request->input('verificationCode');

        // Update the email_verification_code for the authenticated user
        $user = auth()->user();
        $user->email_verification_code = Crypt::encrypt($verificationCode);
        $user->save();

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'New Code',
            'activity_description' => 'Get another code for verification',

            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        // Return a response indicating success
        return response()->json(['message' => 'Email verification code updated successfully'], 200);
    }


    public function load_user()
    {
        $uid = Auth::user()->id;
        $user = User::where('id', $uid)->first();
        return response()->json(['user' => $user], 200);
    }


    public function edit_account_information(Request $request, $user_id)
    {
        $user_role = Auth::user()->role;
        if ($user_role == 1) {

            $get_contact = User_contact::where('user_id', $user_id)->where('isCurrentLocation', 1)->limit(1)->first();

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => "required|email|unique:users,email, $user_id",
                'password' => 'sometimes|nullable|min:8|confirmed',
                'number' => 'required|regex:/^09\d{9}$/',
                'selectedProvince' => 'required',
                'profile' => 'sometimes|nullable|max:2048',
            ], [
                'selectedProvince.required' => 'The province field is required.',
            ]);


            if ($get_contact->landmark == null || $get_contact->house_no == null) {
                $validator = Validator::make($request->all(), [
                    'houseDetails' => 'required',
                    'landmark' => 'required',
                ]);
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }



            if ($request->number || $request->houseDetails && $request->landmark && $request->selectedProvince && $request->selectedMunicipality && $request->selectedBarangay) {

                if (empty($request->selectedMunicipality) && empty($request->selectedBarangay)) {

                    User_contact::where('user_id', $user_id)->where('isCurrentLocation', 1)->limit(1)->update([
                        'mobile_number' => $request->number,
                        'house_no' => $request->houseDetails,
                        'landmark' => $request->landmark,
                        'province' => $request->selectedProvince,
                        'city' => $get_contact->city,
                        'barangay' => $get_contact->barangay,
                    ]);
                } else {

                    User_contact::where('user_id', $user_id)->where('isCurrentLocation', 1)->limit(1)->update([
                        'mobile_number' => $request->number,
                        'house_no' => $request->houseDetails,
                        'landmark' => $request->landmark,
                        'province' => $request->selectedProvince,
                        'city' => $request->selectedMunicipality,
                        'barangay' => $request->selectedBarangay
                    ]);
                }
            }
        } else {


            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => "required|unique:users,email,$user_id",
                'password' => 'sometimes|nullable|min:8|confirmed',
                'old_password' => 'nullable',
                'profile' => 'sometimes|nullable|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::find($user_id);

            if (!empty($request->old_password)) {
                // Check if the old password is provided and matches the user's current password
                if ($request->has('old_password') && !Hash::check($request->old_password, $user->password)) {
                    return response()->json(['errors' => ['old_password' => ['Invalid old password.']]], 422);
                }
            }

            // Store the old image path
            $oldImagePath = $user->profile;

            if ($request->hasFile('profile')) {
                // If a new image is uploaded, update the image path
                $imageName = time() . '_' . uniqid() . '.' . $request->profile->getClientOriginalExtension();
                $request->profile->move(public_path('/storage/profiles'), $imageName);
                $user->profile = '/storage/profiles/' . $imageName;

                // Unlink the old image file
                if ($oldImagePath) {
                    $oldImagePath = public_path($oldImagePath);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                // If no new image is uploaded, retain the old image path
                $user->profile = $oldImagePath;
            }

            $user->name = $request->name;
            $user->email = $request->email;

            if (!empty($request->password)) {
                // Update the password only if it is provided and not empty
                $user->password = Hash::make($request->password);
            }

            $user->save();

            Activitylog::create([
                'user_id' => auth::user()->id,
                'activity_type' => 'Edit',
                'activity_description' => 'Edit account information',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ]);

            return response()->json(['message' => 'Account updated'], 200);
        }
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'));

        if ($response === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent. Please check your email.']);
        } else {
            return response()->json(['message' => 'Failed to send password reset link.'], 500);
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    //'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.']);
        } elseif ($response === Password::INVALID_USER) {
            return response()->json(['message' => 'Invalid user.'], 422);
        } elseif ($response === Password::INVALID_TOKEN) {
            return response()->json(['message' => 'Invalid token.'], 422);
        } else {
            return response()->json(['message' => 'Failed to reset password.'], 500);
        }
    }

    public function renew_password(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = auth()->user();
        $user->update([
            'email_verification_code' => Crypt::encrypt('Password is Okay'),
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated'], 200);
    }

    public function delete_unverified()
    {
        $user = auth()->user();
        if ($user->email_verified_at == null) {
            $user->delete();
        }

        return response()->json(['message' => 'Unverified remove'], 200);
    }
}
