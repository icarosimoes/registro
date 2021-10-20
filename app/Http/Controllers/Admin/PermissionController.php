<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:checkPermission');
    }
    
    public function index($id)
    {
        $routers = $this->service->index();
        $acls = $this->service->getPermission($id);

        // $novo = array();
        // foreach($acls as $item){
        //     $novo[$item['module']->name][] = $item->name;
        // }
        // dd($novo);

         return view('modules/admin/permission/permission')->with(['permission' => $routers, 'acls' => $acls]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $acl = $this->service->store($request->all(), $id);
        if($acl){
            echo json_encode(['success' => true,'message' => 'Dados cadastrados com sucesso.']);
         }else{
             echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar cadastrar, verifique se o email já é cadastrado em nossa base, contate um administrado do sistema.']);
         }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $module = $this->service->getModule($id);
        $acl = $this->service->destroy($id); 
        if($acl){
            return redirect()->action(
                'Admin\PermissionController@index', ['id' => $module->role_id]
            );
         }else{
             echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar remover, contate um administrado do sistema.']);
         }
    }
}
