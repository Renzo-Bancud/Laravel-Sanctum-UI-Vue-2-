<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use App\Models\Slogan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SloganController extends Controller
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

        $query = Slogan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        $slogans = $query->latest('created_at')->paginate($paginate);


        return response()->json($slogans, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'image' => 'required|max:2048',
            'description' => 'required',
        ], [
            'image.required' => 'Background image is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $slogan = Slogan::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->image) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('/storage/slogan'), $imageName);
            $slogan->image = '/storage/slogan/' . $imageName;
            $slogan->save();
        }

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Create slogan',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Slogan added'], 200);
    }


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
            'title' => 'required|max:255',
            'image' => 'sometimes|nullable|max:2048',
            'description' => 'required',
        ], [
            'image.required' => 'Background image is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $slogan = Slogan::find($id);


        // Store the old image path
        $oldImagePath = $slogan->image;

        $slogan->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // If a new image is uploaded, update the image path
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('/storage/slogan'), $imageName);
            $slogan->image = '/storage/slogan/' . $imageName;
            $slogan->save();

            // Unlink the old image file
            if ($oldImagePath) {
                $oldImagePath = public_path($oldImagePath);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            // If no new image is uploaded, retain the old image path
            $slogan->image = $oldImagePath;
            $slogan->save();
        }

        return response()->json(['message' => 'Slogan updated', 'sloganImg' => $slogan->image], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        $slogan = Slogan::find($id);
        if ($slogan) {
            $imagePath = public_path($slogan->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $slogan->delete();

            Activitylog::create([
                'user_id' => Auth::user()->id,
                'activity_type' => 'Trash',
                'activity_description' => 'Delete slogan',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ]);

            return response()->json('success', 200);
        } else {
            return response()->json('Failed', 404);
        }
    }
}