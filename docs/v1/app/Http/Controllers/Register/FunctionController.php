<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Func;

class FunctionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index',Func::class);
        $functions = Func::get();
        return view('register/func/list')->with(['data' => $functions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('store',Func::class);
        return view('register/func/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store',Func::class);
        $function = new Func();
        $function->name = $request->name;
        $function->save();
        return $function;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Func $function)
    {
        $this->authorize('show',Func::class);
        return view('register/func/view',compact('function'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Func $function)
    {
        $this->authorize('show',Func::class);
        return view('register/func/edit',compact('function'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Func $function )
    {
        $this->authorize('update',Func::class);
        $function->name = $request->name;
        $function->save();
        return $function;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Func $function)
    {
        $this->authorize('delete',Func::class);
        $function->delete();
        return $function;   
    }
}
