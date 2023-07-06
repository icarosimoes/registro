<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $this->authorize('index',Role::class);
        // $role = $this->service->index();
        $roles = Role::all();
        return view('modules/admin/profile/list')->with(['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $this->authorize('store',Role::class);
        return view('modules/admin/profile/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $this->authorize('store',Role::class);
        $role = $this->service->store($request->all());
        if($role){
           echo json_encode(['success' => true,'message' => 'Dados cadastrados com sucesso.']);
        }else{
            echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar cadastrar, verifique se o email já é cadastrado em nossa base, contate um administrado do sistema.']);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $this->authorize('show',Role::class);
        $role = Role::findOrFail($id);
        return view('modules/admin/profile/edit')->with(['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('update',Role::class);
        $role = $this->service->update($request->all());
        if($role){
           echo json_encode(['success' => true,'message' => 'Dados cadastrados com sucesso.']);
        }else{
            echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar cadastrar, verifique se o email já é cadastrado em nossa base, contate um administrado do sistema.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete',Role::class);
        $role = $this->service->destroy($id);
        if ($role) {
           return redirect()->route('list.profile');
        }
    }
}
