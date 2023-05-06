<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->middleware('can:checkPermission');
    // }

    public function index()
    {
        $users = $this->service->getUser();
        return view('modules/admin/user/list')->with(['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $profile = $this->service->getProfile();
        return view('modules/admin/user/create')->with(['profiles' => $profile]);
    }

    public function profile()
    {
        $id = Auth::user()->id;
        $user = $this->service->show($id );
        return view('modules/admin/user/profile')->with('data', $user);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $this->service->store($request->all());
        if($user){
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
        $user = $this->service->show($id);
        $profile = $this->service->getProfile();
        return view('modules/admin/user/edit')->with(['data' => $user, 'profiles' => $profile]);
    }

    public function editPassword()
    {
        return view('modules/admin/user/editPassword');
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
        $user = $this->service->update($request->all());
        if($user){
            echo json_encode(['success' => true,'message' => 'Dados alterados com sucesso.']);
         }else{
             echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar alterar, verifique se o email já é cadastrado em nossa base, contate um administrado do sistema.']);
         } 
    }

    public function updateImage(Request $request)
    {
        $user = $this->service->updateImage($request->all());
        if($user){
            echo json_encode(['success' => true,'message' => 'Foto alterada com sucesso.']);
         }else{
             echo json_encode(['success' => false, 'message' => 'Opps, aconteceu um erro ao tentar alterar, verifique se o email já é cadastrado em nossa base, contate um administrado do sistema.']);
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
        
        $user = $this->service->destroy($id);
        if($user){
            return redirect()->route('list.users');
        }
    }
}
