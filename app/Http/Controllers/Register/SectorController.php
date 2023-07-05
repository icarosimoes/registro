<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Sector;
use Illuminate\Http\Request;
use Random\Engine\Secure;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index',Sector::class);
        $sectors = Sector::get();
        return view('register/sector/list')->with(['data' => $sectors]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('store',Sector::class);
        return view('register/sector/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->authorize('store',Sector::class);
        $sector = new Sector();
        $sector->name = $request->sector;
        $sector->save();
        return $sector;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Sector $sector)
    {
        $this->authorize('show',Sector::class);
        return view('register/sector/view',compact('sector'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Sector $sector)
    {
        $this->authorize('show',Sector::class);
        return view('register/sector/edit',compact('sector'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sector $sector)
    {
        $this->authorize('update',Sector::class);
        $sector->name = $request->sector;
        $sector->save();
        return $sector;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sector $sector)
    {
        $this->authorize('delete',Sector::class);        
        $sector->delete();
        return $sector;
    }
}
