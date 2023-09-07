<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class CategoryController extends Controller
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

        $query = Category::query();

        if($search) {
            $query->where('name', 'LIKE', "%$search%")
                ->orWhere('slug', 'LIKE', "%$search%");
        }

        $categories = $query->latest()->paginate($paginate);

        return response()->json($categories, 200);
    }

    public function category_selection(): JsonResponse
    {
        $categories = Category::latest()->get();
        return response()->json($categories, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create(): JsonResponse
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse // This will inform PHP that the function is expected to return a JsonResponse object, and the error should be resolved in return that highlighting as error.
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Add',
            'activity_description' => 'Store category',
           
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Category added'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    // public function show(Category $category)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category): JsonResponse
    {
        if ($category) {
            return response()->json($category, 200);
        } else {
            return response()->json('Failed', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,$category->id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        
        Activitylog::create([
            'user_id' => auth::user()->id,
            'activity_type' => 'Update',
            'activity_description' => 'Modify category',
           
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return response()->json(['message' => 'Category updated'], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category): JsonResponse
    {
        if ($category) {
            $category->delete();


            Activitylog::create([
                'user_id' => auth::user()->id,
                'activity_type' => 'Trash',
                'activity_description' => 'Delete category',
               
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ]);

            return response()->json('success', 200);
        } else {
            return response()->json('Failed', 404);
        }
    }

}