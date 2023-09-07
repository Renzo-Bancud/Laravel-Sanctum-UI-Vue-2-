<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use App\Models\Variation;
use App\Models\Variation_option;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VariationController extends Controller
{


    protected $dateTime;

    public function __construct()
    {
        $now = Carbon::now('Asia/Manila');
        $this->dateTime = $now->toDateTimeString();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $paginate = request('paginate', 10);
        $search = request('search', null);

        $query = Variation::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('variation_name', 'LIKE', "%$search%");
            });
        }

        $variations = $query->with('options')->latest('created_at')->paginate($paginate);

        return response()->json($variations, 200);
    }

    public function variation_selection(): JsonResponse
    {
        $variation = Variation::with('options')->get();
        return response()->json($variation, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'variation' => 'required|max:255',
            'options' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $variation = Variation::create([
            'variation_name' => $request->variation,
            'created_at' => $this->dateTime,
            'updated_at' => $this->dateTime,
        ]);

        // Save variation options
        $options = explode(',', $request->options);
        foreach ($options as $option) {
            Variation_option::create([
                'variation_id' => $variation->id,
                'value' => trim($option),
                'created_at' => $this->dateTime,
                'updated_at' => $this->dateTime,
            ]);
        }

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Create variation',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Variation added'], 200);
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
            'variation' => 'required|max:255',
            'options' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $variation = Variation::find($id);

        if (!$variation) {
            return response()->json(['error' => 'Variation not found'], 404);
        }

        // Update the variation name
        $variation->update([
            'variation_name' => $request->variation,
            'updated_at' => $this->dateTime,
        ]);

        // Delete the existing variation options and then save the updated ones
        Variation_option::where('variation_id', $id)->delete();

        // Save the updated variation options
        $options = explode(',', $request->options);
        foreach ($options as $option) {
            Variation_option::create([
                'variation_id' => $id,
                'value' => trim($option),
                'created_at' => $this->dateTime,
                'updated_at' => $this->dateTime,
            ]);
        }

        Activitylog::create([
            'user_id' => Auth::user()->id,
            'activity_type' => 'Update',
            'activity_description' => 'Update variation',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Variation updated'], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        $variation = Variation::find($id);

        if (!$variation) {
            return response()->json(['error' => 'Variation not found'], 404);
        }

        if ($variation->status == 0) {
            $variation->update(['status' => 1]);
            return response()->json(['message' => 'Variation deactivated successfully'], 200);
        } else {
            $variation->update(['status' => 0]);
            return response()->json(['message' => 'Variation activated successfully'], 200);
        }


    }
}