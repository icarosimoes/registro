<?php

namespace App\Http\Controllers\Event\Meeting;

use App\CheckSuite;
use App\Http\Controllers\Controller;
use App\Models\Meeting\meeting;
use App\Models\Notification;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->service->index();
        return view('event/meeting/list')->with(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usersRegistered = $this->service->usersRegistered();
        $occurrences = $this->service->getOcurrence();
        return view('event/meeting/create')->with([
            'usersRegistered' => $usersRegistered,
            'ocurrences' => $occurrences,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store',meeting::class);
        $meeting = $this->service->store($request->all());
        if ($meeting) {
            echo json_encode(['success' => true, 'message' => 'Registro inserido com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao tentar cadastrar']);
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
        //verifica se a origem é de notificaçoes
        if (request()->notification) {
            $notification = Notification::find(request()->notification);
            $notification->checked = 'yes';
            $notification->save();
        }



        $usersRegistered = $this->service->usersRegistered();
        $occurrences = $this->service->getOcurrence();
        $meeting = $this->service->show($id);
        $meeting_new_subjects = $this->service->meeting_new_subjects($id);
        $meeting_subjects = $this->service->meeting_subjects($id);
        $meeting_topics_covereds = $this->service->meeting_topics_covereds($id);
        $meeting_registered_participants = $this->service->meeting_registered_participants($id);
        $meeting_invited_participants = $this->service->meeting_invited_participants($id);
        return view('event/meeting/view')->with([
            'usersRegistered' => $usersRegistered,
            'ocurrences' => $occurrences,
            'meeting' => $meeting,
            'meeting_subjects' => $meeting_subjects,
            'meeting_new_subjects' => $meeting_new_subjects,
            'meeting_topics_covereds' => $meeting_topics_covereds,
            'meeting_registered_participants' => $meeting_registered_participants,
            'meeting_invited_participants' => $meeting_invited_participants
        ]);
    }

    public function file_download($id)
    {
        $getMeetingSubject = $this->service->getMeetingSubjectID($id);
        return Storage::download($getMeetingSubject->url_archive);       
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //verifica se a origem é de notificaçoes
        if (request()->notification) {
            $notification = Notification::find(request()->notification);
            $notification->checked = 'yes';
            $notification->save();
        }



        $usersRegistered = $this->service->usersRegistered();
        $occurrences = $this->service->getOcurrence();
        $meeting = $this->service->show($id);
        $meeting_new_subjects = $this->service->meeting_new_subjects($id);
        $meeting_subjects = $this->service->meeting_subjects($id);
        $meeting_topics_covereds = $this->service->meeting_topics_covereds($id);
        $meeting_registered_participants = $this->service->meeting_registered_participants($id);
        $meeting_invited_participants = $this->service->meeting_invited_participants($id);
        return view('event/meeting/edit')->with([
            'usersRegistered' => $usersRegistered,
            'ocurrences' => $occurrences,
            'meeting' => $meeting,
            'meeting_subjects' => $meeting_subjects,
            'meeting_new_subjects' => $meeting_new_subjects,
            'meeting_topics_covereds' => $meeting_topics_covereds,
            'meeting_registered_participants' => $meeting_registered_participants,
            'meeting_invited_participants' => $meeting_invited_participants
        ]);
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
        $this->authorize('update',meeting::class);

        $meeting = $this->service->update($request->all());
        if ($meeting) {
            echo json_encode(['success' => true, 'message' => 'Registro Alterado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao Alterar registro']);
        }
    }

    public function getUserRegistered($id)
    {
        $getUserRegistered = $this->service->usersRegistered($id);
        if ($getUserRegistered) {
            echo json_encode(['success' => true, 'data' => $getUserRegistered]);
        } else {
            echo json_encode(['success' => false, 'data' => []]);
        }
    }

    public function store_participants(Request $request)
    {
        $participants = $this->service->store_participants($request->all());
        if ($participants) {
            echo json_encode(['success' => true, 'data' => $participants]);
        } else {
            echo json_encode(['success' => false, 'data' => []]);
        }
    }

    public function getInvitedUsers()
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
        $afectedRows = $this->service->destroy($id);
        if ($afectedRows) {
            return redirect()->route('meeting.list');
        }
    }

    public function startMeeting(meeting $meeting)
    {
        $meeting->start_meeting = now();
        $meeting->save();
        return  date('d/m/Y - H:i', strtotime($meeting->start_meeting));
    }

    public function exportPdfMeeting(meeting $meeting)
    {
        $usersRegistered = $this->service->usersRegistered();
        $occurrences = $this->service->getOcurrence();
        $meeting = $this->service->show($meeting->id);
        $meeting_new_subjects = $this->service->meeting_new_subjects($meeting->id);
        $meeting_subjects = $this->service->meeting_subjects($meeting->id);
        $meeting_topics_covereds = $this->service->meeting_topics_covereds($meeting->id);
        $meeting_registered_participants = $this->service->meeting_registered_participants($meeting->id);
        $meeting_invited_participants = $this->service->meeting_invited_participants($meeting->id);
        
        $name = 'Indefinido';
        if (request()->name){
           $name = request()->name; 
        }
                
        $pdf = PDF::loadView('event/meeting/export_pdf', compact([
            'meeting',
            'usersRegistered',
            'occurrences',
            'meeting',
            'meeting_subjects',
            'meeting_new_subjects',
            'meeting_topics_covereds',
            'meeting_registered_participants',
            'meeting_invited_participants',
            'name',

        ]))->setPaper('a4', 'landscape');
        return $pdf->stream('relatorio.pdf');
       
    }
}
