<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'category' => CategoryResource::collection(Category::latest()->get())
        ];
        return send_response('CategoryResource Retrieved SuccessFul.', $data, Response::HTTP_FOUND);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // 'slug' => 'required|string|slug|max:255|unique:categories',
        ]);
        if ($validator->fails()) {
            return send_error('Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = Category::create([
            'name' => $request['name'],
            'slug' => Str::slug($request['name'])
        ]);

        $data = [
            'category' => $category
        ];
        return send_response('CategoryResource Created SuccessFul.', $data, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            $data = [
                'category' => new CategoryResource($category)
            ];
            return send_response('CategoryResource Retrieved SuccessFul.', $data, Response::HTTP_FOUND);
        }
        return send_error('CategoryResource Not Found!', null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request,Category $category)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return send_error('Validation Error.', $validator->errors(), Response::HTTP_FOUND);;
        }

        $category->name = $input['name'];
        $category->save();

        return send_response( 'CategoryResource Updated Successfully.', $category,Response::HTTP_FOUND);;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'CategoryResource deleted successfully.',]);
        }
        return response()->json(['success' => false, 'message' => 'No CategoryResource found.',]);
    }
}
