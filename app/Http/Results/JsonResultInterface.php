<?php

namespace App\Http\Results;

interface JsonResultInterface
{
    static function response($data);
}