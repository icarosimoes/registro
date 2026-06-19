<?php
namespace App\Services;

interface ServiceInterface
{
    public function index(Array $data);
    public function show(Int $id);
    public function store(Array $request);
    public function update(Array $reques, Int $id);
    public function destroy(Int $id);
}