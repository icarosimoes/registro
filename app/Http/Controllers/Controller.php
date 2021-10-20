<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $service;

    public function __construct()
    {
        try {
            $class =  ucfirst(str_replace(['Http\\', 'Controller'], ['', 'Service'], get_called_class()));
            $this->service = new $class();
        } catch (\Exception $e) {
            throw new \Exception('Internal Server Error', 500);
        } 
    }
}
