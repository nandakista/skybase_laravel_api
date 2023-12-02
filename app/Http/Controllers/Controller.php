<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $defaultLimit = 30;
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
     * Clear existing user token
     */
    public function clearToken($user)
    {  
        $user->tokens->each(function ($token) {
            $token->delete();
        });
    }

    /**
     * Clear existing user token and generate the new one
     */
    public function generateToken($user)
    {  
        return $user->createToken('session')->plainTextToken;
    }

    /**
     * Clear existing user token and generate the new one
     */
    public function generateNewToken($user)
    {  
        self::clearToken($user);
        return $user->createToken('session')->plainTextToken;
    }

    /**
     * Search data by column
     */
    public function searchBy($data, $columnName, $query) 
    {
        if(!empty($query)) {
            return $data->where($columnName, 'LIKE', "%". $query . "%");
        }
        return $data;
    }

    /**
     * Filter data by column
     * It is actually same like a searchBy, but rename for readeable code
     */
    public function filterBy($data, $columnName, $query) 
    {
        return self::searchBy($data, $columnName, $query);
    }

    /**
     * Sort data by column
     */
    public function sortData($data, $column, $sortBy)
    {
        if ($column != null) {
            return $data->orderBy($column, $sortBy ?? 'asc');
        } else {
            return $data;
        }
    }
}
