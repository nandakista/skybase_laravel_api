<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $defaultLimit = 10;
    public $maxLimit = 100;

    /**
     * Use this to set max per_page of pagination data
     */
    public function setLimit($limitRequest)
    {
        if (empty($limitRequest))
            return $this->defaultLimit;
        if ($limitRequest <= $this->maxLimit)
            return $limitRequest;
        return $this->defaultLimit;
    }

    /**
     * Search data by column
     */
    public function searchBy($model, $columnName, $column) 
    {
        if(!empty($column)) {
            return $model->where($columnName, 'LIKE', $column);
        }
        return $model;
    }

    /**
     * Sort data by column
     */
    public function sortData($model, $column)
    {
        $sortBy = $this->sortBy($column);
        $sort = $this->sort($column);
        if (!empty($column)) {
            return $model->orderBy($sortBy, $sort);
        }
        return $model->latest();
    }

    public function sortBy($column)
    {
        if (str_starts_with($column, '-')) {
            $column = substr($column, 1);
        }
        return $column;
    }

    public function sort($column)
    {
        $sort = 'asc';
        if (str_starts_with($column, '-'))
            $sort = 'desc';
        return $sort;
    }
}
