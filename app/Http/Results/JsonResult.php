<?php

namespace App\Http\Results;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JsonResult implements JsonResultInterface{

    static function response($data, $destroy = false)
    {
        if (null === $data) {
            return [
                'success' => true
            ];
        }

        $resource = strtolower((new \ReflectionClass($data))->getShortName());

        if (($data instanceof Collection) ||
        ($data instanceof LengthAwarePaginator)) {
            $appUrl = env('APP_URL') . $_SERVER['REQUEST_URI'];
            $appUrl = strpos($appUrl, 'page=')?substr($appUrl, 0, strpos($appUrl, 'page=')-1):$appUrl;

            $data->withPath($appUrl);

            return [
                'success' => true,
                'data' => $data->items(),
                'metadata' => [
                    'first' => $data->url(1),
                    'last' => $data->url($data->lastPage()),
                    'page' => $data->currentPage(),
                    'total' => $data->total(),
                    'perPage' => $data->perPage(),
                    'prior' => $data->previousPageUrl(),
                    'current' => $data->url($data->currentPage()),
                    'next' => $data->nextPageUrl()
                ]
            ];
        } else {
            $baseUrl = url('/');

            $metadata = [
                'self' => $baseUrl . '/' . $resource . '/' . $data->id,
            ];

            if ($data->trashed()) {
                $metadata['restore'] = $baseUrl . '/' . $resource . '/' . $data->id . '/restore';
            } else {
                $metadata['delete'] = $baseUrl . '/' . $resource . '/' . $data->id;
            }

            return [
                'success' => true,
                'data' => $data,
                'metadata' => $metadata
            ];
        }
    }
}
