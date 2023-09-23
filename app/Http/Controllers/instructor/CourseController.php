<?php

namespace App\Http\Controllers\instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
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
       // dd("working");

        $validator = Validator::make($request->all(),[
           'course_title' => 'required',
        
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        DB::beginTransaction();

        try{

            $course = Course::create([
             'category_id'          => $request->category_id,
             'sub_category_id'      => $request->sub_category_id,
             'course_title'         => $request->course_title,
             'description'          => $request->description,
             'image'                => $this->getImageUrl($request->file('image') ?? null,'image/couse/'),
            ]);

            DB::commit();
            return response()->json([
                "status"   => 'success',
                'message'  => 'course added successfully',
                'data'     => $course
            ]);

        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'    =>  'failed',
                'message'   => 'Course Added Failed',
                'error_msg' => $e->getMessage(),
            ],500);
        }
    }

    public function view(){
        $data=Course::with([
            'courseCategory',
            'courseSubCategory'
        ])->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);     
    }
}
