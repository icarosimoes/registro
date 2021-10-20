<?php

namespace App\Services\Event\Meeting;

use App\Exceptions\ValidationException;
use App\Models\Meeting\meeting;
use App\Models\Meeting\meeting_invited_participants;
use App\Models\Meeting\meeting_registered_participants;
use App\Models\Meeting\meeting_subjects;
use App\Models\Meeting\meeting_topics_covered;
use App\Models\Meeting\participants;
use App\Models\Occurrence;
use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MeetingService extends Service
{

    const STATUS = ["TODOS" => 0, "EM ABERTO" => 1, "EM ANDAMENTO" => 2, "ENCERRADO" => 3, "FECHADO" => 4];

    public function index()
    {
        $meeting = meeting::all();
        return $meeting;
    }

    public function show(int $id)
    {
        $meeting = meeting::findOrFail($id);
        return $meeting;
    }

    public function meeting_subjects(int $id)
    {
        $meeting_subjects = meeting_subjects::where([['meetings_id', $id]])->get();
        return $meeting_subjects;
    }

    public function meeting_topics_covereds(int $id)
    {
        $meeting_topics_covered = meeting_topics_covered::where([['meetings_id', $id]])->get();
        return $meeting_topics_covered;
    }

    public function meeting_registered_participants(int $id)
    {
        $meeting_registered_participants = meeting_registered_participants::where([['meetings_id', $id]])->get();
        foreach($meeting_registered_participants as $meeting_registered_participant){
            $meeting_registered_participant['users'];
        }
        return $meeting_registered_participants;
    }

    public function meeting_invited_participants(int $id)
    {
        $meeting_invited_participants = meeting_invited_participants::where([['meetings_id', $id]])->get();
        foreach($meeting_invited_participants as $meeting_invited_participant){
            $meeting_invited_participant['participants'];
        }
        return $meeting_invited_participants;
    }

    public function getOcurrence()
    {
        $occurrence = Occurrence::all();
        return $occurrence;
    }

    public function getMeetingSubjectID(int $id)
    {
        $meeting_subjects = meeting_subjects::findOrFail($id);
        return $meeting_subjects;
    }

    public function usersRegistered($id=null)
    {
        if(isset($id)){
            $usersRegistered = User::find($id);
        }else{
            $usersRegistered = User::all();
        }
        return $usersRegistered;
    }

    public function store(array $data)
    {
        $topics = explode(",", $data['topics'][0]);
        $topics_covered = explode(",", $data['topics_covered'][0]);
        $providence = explode(",", $data['providence'][0]); 
        $users_registered = explode(",", $data['users_registered'][0]);
        $IdOccurrence = explode(",", $data['IdOccurrence'][0]);
        if(isset($data['invited_users'][0])){
        $invited_users = explode(",", $data['invited_users'][0]);
        }
        $files = $data['files'];

        $meeting = new meeting();
        $meeting->users_id = Auth::user()->id;
        $meeting->save();
        $insertID = $meeting->id;

        for($i=0; $i < count($topics); $i++){
            if($files[$i]->getClientOriginalName() == "empty"){
                $path = "";
            }else{
                $path = $files[$i]->store('files');
            }
            $data = [
                'meetings_id' => $insertID,
                'subject' => $topics[$i],
                'url_archive' => $path,
                'created_at' => date('Y-m-d H:i:s')
            ];
            meeting_subjects::insert($data);
        }

        for($i=0; $i < count($users_registered); $i++){
            $data = [
                'meetings_id' => $insertID,
                'users_id' => $users_registered[$i],
                'created_at' => date('Y-m-d H:i:s')
            ];
            meeting_registered_participants::insert($data);
        }

        if(isset($invited_users)){
            for($i=0; $i < count($invited_users); $i++){
                $data = [
                    'meetings_id' => $insertID,
                    'participants_id' => $invited_users[$i],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                meeting_invited_participants::insert($data);
            }
        }

        for($i=0; $i < count($topics_covered); $i++){
            $data = [
                'meetings_id' => $insertID,
                'subject_addressed' => $topics_covered[$i],
                'providence' => $providence[$i],
                'occurrences_id' => $IdOccurrence[$i],
                'created_at' => date('Y-m-d H:i:s')
            ];
            meeting_topics_covered::insert($data);
        }
        return $meeting;
    }

    public function store_participants(array $data)
    {
        $participants = new participants();
        $participants->name = $data['name'];
        $participants->email = $data['email'];
        $participants->telephone = $data['telephone'];
        $participants->profession = $data['profession'];
        $participants->url_image = "img/user_default.png";
        $participants->save();
        return $participants;
    }

    public function update(array $data)
    {
        $topics = explode(",", $data['topics'][0]);
        $topics_id = explode(",", $data['topics_id'][0]);

        $topics_covered = explode(",", $data['topics_covered'][0]);
        $topics_covered_id = explode(",", $data['topics_covered_id'][0]);

        $providence = explode(",", $data['providence'][0]); 
        $users_registered = explode(",", $data['users_registered'][0]);
        $IdOccurrence = explode(",", $data['IdOccurrence'][0]);
        if(isset($data['invited_users'][0])){
        $invited_users = explode(",", $data['invited_users'][0]);
        }
        $files = $data['files'];

        //$meeting = new meeting();
        $meeting = $this->show($data['meeting_id']);
        $meeting->users_id = Auth::user()->id;
        $meeting->save();
        $insertID = $meeting->id;

        for($i=0; $i < count($topics); $i++){
            if($files[$i]->getClientOriginalName() == "empty"){
                $path = "";
            }else{
                $path = $files[1]->store('files');
            }
            if($path == ""){
                $data = [
                    'meetings_id' => $insertID,
                    'subject' => $topics[$i],
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }else{
                $data = [
                    'meetings_id' => $insertID,
                    'subject' => $topics[$i],
                    'url_archive' => $path,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            meeting_subjects::where('id', $topics_id[$i])->update($data);
        }

        for($i=0; $i < count($users_registered); $i++){
            $data = [
                'meetings_id' => $insertID,
                'users_id' => $users_registered[$i],
                'created_at' => date('Y-m-d H:i:s')
            ];
            meeting_registered_participants::where('meetings_id', $insertID)->update($data);
        }

        if(isset($data['invited_users'][0])){
            for($i=0; $i < count($invited_users); $i++){
                $data = [
                    'meetings_id' => $insertID,
                    'participants_id' => $invited_users[$i],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                meeting_invited_participants::where('meetings_id', $insertID)->update($data);
            }
        }

        for($i=0; $i < count($topics_covered); $i++){
            $data = [
                'meetings_id' => $insertID,
                'subject_addressed' => $topics_covered[$i],
                'providence' => $providence[$i],
                // 'occurrences_id' => ($IdOccurrence[$i] == 'null' ?  : $IdOccurrence[$i]),
                'created_at' => date('Y-m-d H:i:s')
            ];
            meeting_topics_covered::where('id', $topics_covered_id[$i])->update($data);
        }
        return $meeting;
    }

    //Excluir registro e relacionados
    public function destroy($id)
    {
        meeting_subjects::where('meetings_id', $id)->delete();
        meeting_topics_covered::where('meetings_id', $id)->delete();
        meeting_registered_participants::where('meetings_id', $id)->delete();
        meeting_invited_participants::where('meetings_id', $id)->delete();
        $afectedRows = meeting::where('id',$id)->delete();
        return $afectedRows;
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'title' => 'required',
                'description' => 'required',
                'type_occurrence' => 'required',
                'deadline' => 'required',
                'receiver' => 'required',
                'comments' => 'required',
            ],
            $this->getDefaultMessages()
        );

        if ($validator->fails()) {
            $e = new ValidationException('INVALID_DATA', 400);
            $e->setMessages($validator->errors()->getMessages());
            throw $e;
        }

        return $validator->fails();
    }
}
