<?php

namespace App\Http\Controllers\Occurrence;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OccurrenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->service->index();
        return view('occurrence/list')->with(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $getUser = $this->service->getUSer();
        $typeOccurrence = $this->service->getTypeOccurrence();
        return view('occurrence/create')->with(['users' => $getUser, 'types' => $typeOccurrence]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $occurrence = $this->service->store($request->all());
        if ($occurrence) {
            echo json_encode(['success' => true, 'message' => 'Registro Cadastrado com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'erro ao cadastrar registro.']);
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
        $getOccurrenceComments = $this->service->getOccurrenceComments($id);
        $occurrence = $this->service->show($id);
        $receiver = $this->service->getUSer($occurrence->receiver_user);
        return view('occurrence/view')->with([
            'data' => $occurrence,
            'receiver' => $receiver,
            'occurrenceComments' => $getOccurrenceComments,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $occurrence = $this->service->show($id);
        $validateUser = $this->service->validateUser($occurrence->users_id, $occurrence->receiver_user, $occurrence->id);
        if ($validateUser) {
            $receiver = $this->service->getUSer($occurrence->receiver_user);
            $typeOccurrence = $this->service->getTypeOccurrence();
            $getUser = $this->service->getUSer();
            $getOccurrenceComments = $this->service->getOccurrenceComments($id);
            $getParticipants = $this->service->getParticipants($id);
            return view('occurrence/edit')->with([
                'data' => $occurrence,
                'receiver' => $receiver,
                'types' => $typeOccurrence,
                'users' => $getUser,
                'occurrenceComments' => $getOccurrenceComments,
                'participants' => $getParticipants,
            ]);
        }else{
            return Redirect::back()->withErrors(['Acesso não permitido, MOTIVO: Esse registro não foi atribuido a você, ou você não foi o criador.', 'The Message']);
        }
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
        $occurrence = $this->service->update($request->all());
        if ($occurrence) {
            echo json_encode(['success' => true, 'message' => 'Registro Alterado com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'erro ao alterar registro.']);
        }
    }

    public function getOccurrence()
    {
        $data = $this->service->index();
        if($data){
            echo json_encode(['success' => true, 'data' => $data]);
        }else{
            echo json_encode(['success' => false, 'data' => []]);
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
        $occurrence = $this->service->destroy($id);
        if ($occurrence) {
            return redirect()->route('occurrence.list');
        }else{
            return Redirect::back()->withErrors(['Não foi possível remover esse registro, ele pode ter ligações com outras funcionalidades.','']);
        }
    }
}
