<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Helpers\StorageHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $this->setLimit($request->per_page);
            $data = new Article();
            $data = $this->searchBy($data, 'name', $request->title);
            $data = $this->sortData($data, $request->sort, $request->sortBy);
            $data = $data->paginate($perPage);
            return ResponseHelper::success(data: $data->items(), meta: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(error: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(error: $err->getMessage(), code: 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $data = Article::find($request->id);
            if ($data == null) return ResponseHelper::error(error: 'Article not found');
            
            return ResponseHelper::success(data: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(error: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(error: $err->getMessage(), code: 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sub_category_id' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'images' => 'required|max:5',
                'images.*' => 'required|file|max:2048',
            ], [
                'image.max' => 'Maximum image is 5 file',
                'image.uploaded' => 'Maximum size in each file is 2MB'
            ]);

            if ($validator->fails()) {
                $errors = ValidationHelper::mobile($validator->errors()->all());
                return ResponseHelper::error(message: 'Failed to add Article', error: $errors);
            }

            $article = Article::find($request->id);
            if ($article == null) return ResponseHelper::error(error: 'Article not found');

            $images = StorageHelper::updateFiles(
                        config('constant.articles_path'),  
                        $article->images, 
                        $request->file('images'),
                    );

            $article->update([
                'user_id'           => auth('sanctum')->user()->id,
                'sub_category_id'   => $request->sub_category_id,
                'title'             => $request->title,
                'content'           => $request->content,
                'images'            => json_encode($images),
            ]);

            return ResponseHelper::success(message: 'Update Article Successfully', data: $article);
        } catch (QueryException $err) {
            return ResponseHelper::error(message: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sub_category_id' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'images' => 'required|max:5',
                'images.*' => 'required|file|max:2048',
            ], [
                'image.max' => 'Maximum image is 5 file',
                'image.uploaded' => 'Maximum size in each file is 2MB'
            ]);

            if ($validator->fails()) {
                $errors = ValidationHelper::mobile($validator->errors()->all());
                return ResponseHelper::error(message: 'Failed to add Article', error: $errors);
            }

            $images = [];
            foreach ($request->file('images') as $file) {
                $savedPath = StorageHelper::saveFile(config('constant.articles_path'), $file);
                $images[] = $savedPath;
            }

            $data = Article::create([
                'user_id'           => auth('sanctum')->user()->id,
                'sub_category_id'   => $request->sub_category_id,
                'title'             => $request->title,
                'content'           => $request->content,
                'images'            => json_encode($images),
            ]);

            return ResponseHelper::success(message: 'Add Article Successfully', data: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(message: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = Article::find($request->id);
            if ($data == null) return ResponseHelper::error(error: 'Article not found');
            $data->delete();
            return ResponseHelper::success(message: 'Delete Article Successfully', data: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(message: $err->getMessage());
        }  catch (Exception $err) {
            return ResponseHelper::error(
                message: 'Failed to delete account',
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }
}
