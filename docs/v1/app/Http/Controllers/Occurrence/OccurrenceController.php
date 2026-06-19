<?php

namespace App\Http\Controllers\Occurrence;

use App\Http\Controllers\Controller;
use App\Models\Occurrence;
use App\Models\Notification;
use App\Models\Occurrence_participants;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', Occurrence::class);
        session()->forget('data');
        $data = $this->service->index();
        session()->put('data', $data);

        return view('occurrence/list')->with(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('store', Occurrence::class);
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

        $this->authorize('store', Occurrence::class);
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
        $this->authorize('show', Occurrence::class);
        $occurrence = $this->service->show($id);
        $validateUser = $this->service->validateUser($occurrence->users_id, $occurrence->receiver_user, $occurrence->id);
        if ($validateUser) {
            $receiver = $this->service->getUSer($occurrence->receiver_user);
            $typeOccurrence = $this->service->getTypeOccurrence();
            $getUser = $this->service->getUSer();
            $getOccurrenceComments = $this->service->getOccurrenceComments($id);
            $getParticipants = $this->service->getParticipants($id);
            return view('occurrence/view')->with([
                'data' => $occurrence,
                'receiver' => $receiver,
                'types' => $typeOccurrence,
                'users' => $getUser,
                'occurrenceComments' => $getOccurrenceComments,
                'participants' => $getParticipants,
            ]);
        } else {
            return Redirect::back()->withErrors(['Acesso não permitido, MOTIVO: Esse registro não foi atribuido a você, ou você não foi o criador.', 'The Message']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('show', Occurrence::class);
        DB::beginTransaction();
        //verifica se a origem do link é das notificacoes
        if (request()->notification) {
            $notification = Notification::find(request()->notification);
            $notification->checked = 'yes';
            $notification->save();
        }

        $occurrence = $this->service->show($id);
        // dd($occurrence);
        $validateUser = $this->service->validateUser($occurrence->users_id, $occurrence->receiver_user, $occurrence->id);
        if ($validateUser) {
            $receiver = $this->service->getUSer($occurrence->receiver_user);
            $typeOccurrence = $this->service->getTypeOccurrence();
            $getUser = $this->service->getUSer();
            $getOccurrenceComments = $this->service->getOccurrenceComments($id);
            $getParticipants = $this->service->getParticipants($id);
            DB::commit();
            return view('occurrence/edit')->with([
                'data' => $occurrence,
                'receiver' => $receiver,
                'types' => $typeOccurrence,
                'users' => $getUser,
                'occurrenceComments' => $getOccurrenceComments,
                'participants' => $getParticipants,
            ]);
        } else {
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
        $this->authorize('update', Occurrence::class);

        $occurrence = $this->service->update($request->all());
        if ($occurrence) {
            echo json_encode(['success' => true, 'message' => 'Registro Alterado com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'erro ao alterar registro.']);
        }
    }

    public function downloadFile(Occurrence $occurrence)
    {
        if (Storage::exists($occurrence->file)) {
            return Storage::download($occurrence->file);
        }
        return 'Nenhum arquivo encontrado.';
    }


    public function getOccurrence()
    {
        $data = $this->service->index();
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
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
        $this->authorize('delete', Occurrence::class);
        $occurrence = $this->service->destroy($id);
        if ($occurrence) {
            return redirect()->route('occurrence.list');
        } else {
            return Redirect::back()->withErrors(['Não foi possível remover esse registro, ele pode ter ligações com outras funcionalidades.', '']);
        }
    }

    public function exportPdf($name)
    {

        if (!$name) {
            $name = "Indefinido";
        }

        $data = session()->get('data');
        if (session()->get('params')) {
            $params = session()->get('params');
        } else {
            $params = false;
        }

        $pdf = PDF::loadView('occurrence/export_pdf', compact(['data', 'name']))->setPaper('a4', 'landscape');
        return $pdf->stream('relatorio.pdf');
    }

    /**
     * Clona um registro de ocorrência.
     */
    public function clone(Occurrence $occurrence)
    {
        $occurrence_clone = new Occurrence();
        $occurrence_clone->title = $occurrence->title;
        $occurrence_clone->description = $occurrence->description;
        $occurrence_clone->type_occurrences_id = $occurrence->type_occurrences_id; 
        $occurrence_clone->sector_id = $occurrence->sector_id;
        $occurrence_clone->local_id = $occurrence->local_id;
        $occurrence_clone->unit = $occurrence->unit;
        $occurrence_clone->deadline = $occurrence->deadline;
        $occurrence_clone->receiver_user = $occurrence->receiver_user;
        $occurrence_clone->comments = $occurrence->comments;
        $occurrence_clone->status = $occurrence->status;
        $occurrence_clone->users_id = $occurrence->users_id;
        $occurrence_clone->save();

        // Clona os participantes
        Occurrence_participants::where('occurrences_id', $occurrence->id)
        ->get()
        ->each(function ($participant) use ($occurrence_clone) {            
            $new_participant = new Occurrence_participants();
            $new_participant->occurrences_id = $occurrence_clone->id;
            $new_participant->users_id = $participant->users_id;
            $new_participant->save();
        });

        return response('Clonado com sucesso', 200);
    }
}

