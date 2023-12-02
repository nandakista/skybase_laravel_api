<?php

namespace App\Helpers;

class ResponseHelper
{
    private static function format($status, $message, $code, $error, $data)
    {
        return [
            'success'   => $status,
            'message'   => $message,
            'status'    => $code,
            'error'     => $error,
            'data'      => $data,
        ];
    }

    public static function meta($from, $to, $currentPage, $lastPage, $perPage, $total)
    {
        return [
            'from'            => $from,
            'to'              => $to,
            'current_page'    => $currentPage,
            'last_page'       => $lastPage,
            'per_page'        => $perPage,
            'total'           => $total,

            # Optional (for web)
            // "first_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'=1',
            // "prev_page_url" =>  $this->previousPageUrl(),
            // "next_page_url" =>  $this->nextPageUrl(),
            // "last_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'='.$this->lastPage(),
            // "path" =>  $this->getOptions()['path'],
        ];
    }

    public static function success($data = [], $meta = null, $message = 'Successfully', $code = 200)
    {
        $response = self::format(true, $message, $code, null, $data);
        if($meta != null) {
            $metaAttr = self::meta(
                $meta->firstItem(),
                $meta->count(),
                $meta->currentPage(),
                $meta->lastPage(),
                $meta->perPage(),
                $meta->total(),
            ); 
            $response['meta'] = $metaAttr;
        }

        return response()->json($response, $code);
    }

    public static function error($error = [], $code = 400, $message = 'Failed')
    {
        $response = self::format(false, $message, $code, $error, []);
        return response()->json($response, $code);
    }

    public static function unauthorized($error = [], $code = 401, $message = 'Unauthorized')
    {
        $response = self::format(false, $message, $code, $error, []);
        return response()->json($response, $code);
    }

    public static function created($data = [], $message = 'Successfully',)
    {
        return self::success(data: $data, message: $message, code: 201);
    }
}
