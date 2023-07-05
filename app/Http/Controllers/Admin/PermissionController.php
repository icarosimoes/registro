<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acl;
use App\Models\Role;
use App\Models\Routers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function __construct()
    // {
    //     // parent::__construct();
    //     // $this->middleware('can:checkPermission');
    // }
    
    public function index($id)
    {
        $role = Role::find($id);
        $permissions = $role->acl;
        $acls = Acl::orderBy('controller')->get(); 
                 
        return view('modules/admin/permission/permission')->with(['role'=> $role ,'permission'=>$permissions,'acls' => $acls]);
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
        DB::beginTransaction();
        $role = Role::find(request()->role_id);
        $role->acl()->detach($id);
        DB::commit();

        return back();
        // $module = $this->service->getModule($id);
        // $acl = $this->service->destroy($id); 
        // if($acl){
        //     return redirect()->action(
        //         'Admin\PermissionController@index', ['id' => $module->role_id]
        //     );
        //  }else{
        //      echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar remover, contate um administrado do sistema.']);
        //  }
    }
}
