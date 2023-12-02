<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $this->setLimit($request->per_page);
            $data = new SubCategory();
            $data = $this->searchBy($data, 'name', $request->name);
            $data = $this->filterBy($data, 'category_id', $request->category_id);
            $data = $this->sortData($data, $request->sort, $request->sortBy);
            $data = $data->paginate($perPage);
            return ResponseHelper::success(data: $data->items(), meta: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(error: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(error: $err->getMessage(), code: 500);
        }
    }
}
