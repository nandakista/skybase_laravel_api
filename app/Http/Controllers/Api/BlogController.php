<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Blog;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Validation\Rule;
use App\Helpers\ValidationHelper;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $this->setLimit($request->per_page);
            $data = new Blog();
            if (!empty($request->title)) $data = $data->where('title', 'LIKE', $request->title);
            $data = $this->sortData($data, $request->sort);
            $data = $data->paginate($perPage);
            return ResponseFormatter::success(data: $data->items(), meta: $data);
        } catch (QueryException $err) {
            return ResponseFormatter::error(error: $err->getMessage());
        } catch (Exception $err) {
            return ResponseFormatter::error(error: $err->getMessage(), code: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validation = Validator::make(
                request()->all(),
                // [
                //     'title' => $request->title,
                //     'body' => $request->body,
                // ],
                [
                    'title' => 'required',
                    'body' => 'required',
                    'photo' => 'required|file|max:5024',
                ],
                // -------- If you want to custom message 
                [
                    'title.required' => ':attribute tidak boleh kosong',
                    'photo.max' => 'Maximum file size to upload is 5MB (:max)'
                    // 'same' => 'The :attribute and :other must match.',
                    // 'size' => 'The :attribute must be exactly :size.',
                    // 'between' => 'The :attribute value :input is not between :min - :max.',
                    // 'in' => 'The :attribute must be one of the following types: :values',
                ]
            );

            /// ------ Validation method
            ///
            /// $validation->fails()                              : isFail
            /// $validation->errors()                             : List of field and error message of every input
            /// $validation->errors()->all()                      : List of error message
            /// $validation->errors()->add('field', 'err msg')    : Add custom error in last of list
            ///

            if ($validation->fails()) {
                // ------- Default response
                // throw new InvalidArgumentException($validation->errors());

                $errors = ValidationHelper::errMobile($validation->errors()->all());
                return ResponseFormatter::error(message: 'Failed to create Blog', error: $errors);
            }

            // $data = Blog::create($request->all());


            // ---- Save photo
            $photoFile = $request->file('photo');
            $photoPath = Storage::putFile('photos', $photoFile);
            $photoUrl = url(Storage::url($photoPath));

            // ---- Atau klo mau manipulate per field bisa gunain cara dibawah
            $data = Blog::create([
                'title' => $request->title,
                'body' => $request->body,
                'photo_path' => $photoUrl,
            ]);

            return ResponseFormatter::created($data);
        } catch (Exception $err) {
            return ResponseFormatter::error(
                message: 'Failed to create Blog',
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $data = Blog::find($id);
            if (is_null($data)) {
                return ResponseFormatter::error(error: 'Data doesn\'t exist', code: 404);
            }

            // ---- Cara ke 2
            // $data = Blog::where('id', '=', $id)->get();
            // if (!$data) {
            //     return ResponseFormatter::error(
            //         message: 'Failed get Blog Detail', 
            //         error: 'Data doesn\'t exist', 
            //         code: 404,
            //     );
            // }

            return ResponseFormatter::success(data: $data);
        } catch (Exception $err) {
            return ResponseFormatter::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validation = Validator::make(
                request()->all(),
                [
                    'title' => 'required',
                    'body' => 'required',
                    'photo' => 'required|image|max:5024',
                ],
            );

            if ($validation->fails()) {
                // throw new InvalidArgumentException($validation->errors());
                $errors = ValidationHelper::errMobile($validation->errors()->all());
                return ResponseFormatter::error(message: 'Failed to update Blog', error: $errors);
            }

            $data = Blog::find($id);
            if (!$data) {
                return ResponseFormatter::error(error: 'Data doesn\'t exist', code: 404);
            }

            // $data->update($request->all());

            // --- Delete old photo
            $dirPath = 'photos';
            $tmp = explode($dirPath, $data->photo_path);
            $storagePath = $dirPath . end($tmp);
            if ($data->photo_path != null) Storage::delete($storagePath);

            // --- Save new photo
            $photoFile = $request->file('photo');
            $photoPath = Storage::putFile($dirPath, $photoFile);
            $photoUrl = url(Storage::url($photoPath));

            $data->title = $request->title;
            $data->body = $request->body;
            $data->photo_path = $photoUrl;
            $data->save();

            return ResponseFormatter::success(data: $data);
        } catch (Exception $err) {
            return ResponseFormatter::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = Blog::find($id);
            if (!$data) {
                return ResponseFormatter::error(error: 'Data doesn\'t exist', code: 404);
            }

            // --- Delete photo
            $dirPath = 'photos';
            $tmp = explode($dirPath, $data->photo_path);
            $storagePath = $dirPath . end($tmp);
            if ($data->photo_path != null) Storage::delete($storagePath);
            
            $data->delete($id);
            return ResponseFormatter::success(data: $data, message: "Successfully delete blog");
        } catch (Exception $err) {
            return ResponseFormatter::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }
}
