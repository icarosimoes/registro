<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:checkPermission');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = $this->service->index();
        return view('modules/admin/profile/list')->with(['roles' => $role]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $role = $this->service->show($id);
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
        $role = $this->service->destroy($id);
        if ($role) {
           return redirect()->route('list.profile');
        }
    }
}
