<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\courseSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseSubCategoryController extends Controller
{
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

       $validator = Validator::make($request->all(),[
        'sub_category_name'    =>'required|unique:course_sub_categories',
        'image'                =>'image',
       ]);

       if($validator->fails()){
        return response()->json(['error' => $validator->errors()],401);
       }

       DB::beginTransaction();

       try{

        $subCategory= courseSubCategory::create([
            'sub_category_name'   => $request->sub_category_name,
            'image'               => $this->getImageUrl($request->file('image') ?? null, 'image/sub-category/'),
            'category_id'         => $request->category_id
        ]);

        DB::commit();
        return response()->json([
            'status'            => 'success',
            'message'           => 'sub category added successfully',
            'sub_category_data' =>  $subCategory
        ], 200);
       }
       catch(\Exception $e){
        DB::rollBack();
        return response()->json([
            'status'            => 'failed',
            'message'           => 'sub category Added Failed',
            'error_msg'         => $e->getMessage(),
        ],500);
       }
    }
}
