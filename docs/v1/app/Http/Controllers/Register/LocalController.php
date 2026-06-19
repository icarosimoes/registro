<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Local;
use Illuminate\Http\Request;

class LocalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $this->authorize('index',Local::class);
        $locals = Local::get();
        return view('register/local/list')->with(['data' => $locals]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('store',Local::class);
        return view('register/local/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store',Local::class);
        $local = new Local();
        $local->name = $request->name;
        $local->save();
        return $local;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Local $local)
    {
        $this->authorize('show',Local::class);
        return view('register/local/view',compact('local'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Local $local)
    {
        $this->authorize('show',Local::class);
        return view('register/local/edit',compact('local'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Local $local)
    {
        $this->authorize('update',Local::class);
        $local->name = $request->name;
        $local->save();
        return $local;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Local $local)
    {
        $this->authorize('delete',Local::class);
        $local->delete();
        return $local;   
    }
}
