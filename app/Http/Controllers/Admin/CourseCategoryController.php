<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\imageTrait;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseCategoryController extends Controller
{
    // use imageTrait;
    public function getImageUrl($file, $path)
    {
        $file_path = null;
        if ($file && $file !== 'null') {
            $file_name = date('Ymd-his') . '.' . $file->getClientOriginalName();
            $destinationPath = public_path($path);      
            // Move the uploaded file to the destination path without resizing
            $file->move($destinationPath, $file_name);           
            $file_path = $path . $file_name;
        }
        return $file_path ?? null;
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'      => 'required|unique:categories',
            'image'     => 'image',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        DB::beginTransaction();
        try{
            $category            = Category::create([
                'name'          => $request->name,
                'image'         => $this->getImageUrl($request->file('image') ?? null, 'image/category/'),
            ]);

            DB::commit();
            return response()->json([
                'status'        => 'success',
                'message'       => 'category added successfully',
                'category_data' => $category
            ], 200);
            

        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Category Added Failed',
                'error_msg' => $e->getMessage(),
            ],500);
        }
    }

    public function view(){
        $data=Category::all();

        return response()->json([
            'data' =>$data,
        ]);
    }

    public function update(Request $request){
        $id=$request->id;

        $data = Category::findOrFail($id);

        $data->name = $request->name;
        

    }
}
