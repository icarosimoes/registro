<?php

namespace App\Services\Occurrence;


use App\Exceptions\ValidationException;
use App\Models\Occurrence;
use App\Models\Occurrence_comments;
use App\Models\Occurrence_participants;
use App\Models\TypeOccurrence;
use App\Models\User;
use App\Models\Notification;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OccurrenceService extends Service
{

    const STATUS = ["EM ABERTO" => 1, "ENCERRADO" => 2, "TODOS" => 0];

    public function index()
    {
        $occurrence = Occurrence::orderBy('created_at','desc');


        if(request()->local){
            $occurrence->where('local_id',request()->local);
        }

        if(request()->unit){
            $occurrence->where('unit',request()->unit);
        }

        if(request()->sector){
            $occurrence->where('sector_id',request()->sector);
        }

        if( request()->status != 0 ){
            $occurrence->where('status',request()->status);
        }elseif(request()->status == null){
            $occurrence->whereIn('status',[1,2]);
        }
        
        $occurrence = $occurrence->get();
        return $occurrence;
    }

    public function show(int $id)
    {
        return Occurrence::findOrFail($id);
    }

    /** GETs Home Dashboard */
    public function totalOccurrence()
    {
        $totalOccurrence = Occurrence::all()->count();
        return $totalOccurrence;
    }

    public function totalOccurrenceOpen()
    {
        $totalOccurrenceOpen = Occurrence::where([['status', 1]])->count();
        return $totalOccurrenceOpen;
    }

    public function totalOccurrenceClosed()
    {
        $totalOccurrenceClosed = Occurrence::where([['status', 3]])->count();
        return $totalOccurrenceClosed;
    }

    public function totalOccutotalUsersrrence()
    {
        $totalOccutotalUsersrrence = User::all()->count();
        return $totalOccutotalUsersrrence;
    }
    /** end GETs Home Dashboard */

    public function getUSer($id = null)
    {
        if ($id) {
            $user = User::find($id);
        } else {
            $user = User::all();
        }
        return $user;
    }

    public function getOccurrenceComments(int $id)
    {
        $occurrence_comments = Occurrence_comments::where([['occurrences_id', $id]])->get();
        return $occurrence_comments;
    }
    public function getParticipants(int $occurrenceId)
    {
        $occurrence_participants = Occurrence_participants::where([['occurrences_id', $occurrenceId]])->get();
        return $occurrence_participants;
    }
    public function getTypeOccurrence()
    {
        $typeOccurrence = TypeOccurrence::all();
        return $typeOccurrence;
    }

    public function store($data)
    {
        $this->validate($data);

        $filePath=null;
        if (request()->hasFile('file') && request()->file->isValid() ){
           $filePath = request()->file('file')->store('registers');
        }
          
        DB::beginTransaction();
        $occurrence = new Occurrence();
        $occurrence->title = $data['title'];
        $occurrence->deadline = $data['deadline'];
        $occurrence->receiver_user = $data['receiver'];
        $occurrence->local_id = $data['local_id'];
        $occurrence->unit = $data['unit'];
        $occurrence->sector_id = $data['sector_id'];
        
        $occurrence->file = $filePath;
        if (!empty($data['comments'])){
            $occurrence->comments = $data['comments'];
        }
        $occurrence->users_id = Auth::user()->id;
        $occurrence->save();
        $insertID = $occurrence->id;

        if (!empty($data['participants'])) {
            $participants = explode(",", $data['participants']);
            for ($i = 0; $i < count($participants); $i++) {
                $data = [
                    'occurrences_id' => $insertID,
                    'users_id' => $participants[$i],
                    'created_at' => date("Y-m-d H:i:s"),
                ];
                Occurrence_participants::insert($data);
                //REGISTRA AS NOTIFICACOES
                $notification = new Notification();
                $notification->user_id = $participants[$i];
                $notification->occurrence_id = $insertID;
                $notification->checked = 'not';
                $notification->msg = 'Novo registro criado.';
                $notification->save();

                
            }
        }
        
        
        if (!empty(request()->all()['description'])) {
            
            $Occurrence_comments = [
                'occurrences_id' => $insertID,
                'comments' => request()->all()['description'],
                'users_id' => Auth::user()->id,
                'created_at' => Date("Y-m-d H:i:s"),
            ];
            Occurrence_comments::insert($Occurrence_comments);
        }
        DB::commit();
        return $occurrence;
    }

    public function update(array $data)
    {
        DB::beginTransaction();
        $this->validate($data);
        
        $filePath = null;
        if (request()->hasFile('file') && request()->file->isValid() ){
            $occurrence =  Occurrence::find($data['id']);
            Storage::delete($occurrence->file);
            $filePath = request()->file('file')->store('registers');
            $occurrence->file = $filePath ;
            $occurrence->save();
        }

        
        if (!empty($data['id'])) {
            $occurrence = $this->show($data['id']);
            $occurrence->title = $data['title'] ?? $occurrence->title;
            $occurrence->status = $data['status'] ?? $occurrence->status;
            $occurrence->deadline = $data['deadline'] ?? $occurrence->deadline;
            $occurrence->receiver_user = $data['receiver'] ?? $occurrence->receiver_user;
            $occurrence->local_id = $data['local_id']; 
            $occurrence->unit = $data['unit']; 
            $occurrence->sector_id = $data['sector_id']; 
            
            
            if (!empty($data['comments'])) {
                $occurrence->comments = $data['comments'] ?? $occurrence->comments;
            }
            
            $occurrence->save();
            $insertID = $occurrence->id;

            //rotina  para ferificar se modificou o campo NOTIFICAR 
            $participants = Occurrence_participants::where('occurrences_id', $insertID)->get();
            
            if (!empty($data['participants'])) {
                $participants_request = explode(",", $data['participants']);
                foreach ($participants_request  as $participant_request ) {
                    $participant_exist = false;
                    foreach($participants as $parti){
                        if ($parti->users_id == $participant_request){
                            $participant_exist = true;   
                        }
                    }
                    if($participant_exist){
                        continue;
                    }else{
                        //se encontrou alguma diferença : modifica o campo updated_at só pra entrar no evento updating e gerar as notificacoes
                        $occurrence->updated_at = now();
                        $occurrence->save();
                    }
                }
            }

            
            
            
            
            
            if (!empty($data['participants'])) {
                $participants = explode(",", $data['participants']);
                for ($i = 0; $i < count($participants); $i++) {
                    $dataParticipants = [
                        'occurrences_id' => $insertID,
                        'users_id' => $participants[$i],
                        'updated_at' => date("Y-m-d H:i:s"),
                    ];
                    $resultFind = Occurrence_participants::where([['occurrences_id', $insertID],['users_id', $participants[$i]]])->first();
                    if (!$resultFind) {
                        Occurrence_participants::insert($dataParticipants);
                    }
                }
            }
            
            if (!empty($data['evolution'])) {
                
                $Occurrence_comments = [
                    'occurrences_id' => $insertID,
                    'comments' => $data['evolution'],
                    'users_id' => Auth::user()->id,
                    'created_at' => Date("Y-m-d H:i:s"),
                ];
                Occurrence_comments::insert($Occurrence_comments);
                //atualiza e envia as notificacoes
                $occurrence->updated_at = now();
                $occurrence->save();
            }

            DB::commit();
            return $occurrence;
        } else {
            return false;
        }
    }

    //verificar permisão ao usuário editar um registro(Ocorrência)

    /**
     * O primeiro nível de validação verrifica se o usuário é o criador ou o atribuido no registro
     *
     * @return bool
     */
    public function validateUser(int $userID, int $receiverUserID, int $occurrenceID)
    {
        if ($userID != Auth::user()->id) {
            $result1 = $this->validateUserLevel2($receiverUserID);
            if ($result1) {
                return (Boolean) true;
            } else {
                $result2 = $this->validateUserLevel3($occurrenceID, Auth::user()->id);
                if ($result2) {
                    return (Boolean) true;
                } else {
                    if (Auth::user()->isAdmin == 1) {
                        return (Boolean) true;
                    } else {
                        return (Boolean) false;
                    }
                }
            }
        }
        return (Boolean) true;
    }

    /**
     * O Segundo nível de validação verrifica se o usuário foi mencionado como destinatário
     *
     * @return void
     */
    public function validateUserLevel2(int $receiverUserID)
    {
        if ($receiverUserID != Auth::user()->id) {
            return (Boolean) true;
        }
        return (Boolean) false;
    }

    /**
     * O Terceiro nível de validação verrifica se o usuário faz parte do clico de participantes do registro
     *
     * @return void
     */
    public function validateUserLevel3(int $occurrenceID, int $userID)
    {
        $occurrence_participants = Occurrence_participants::where([
            ['occurrences_id', $occurrenceID],
            ['users_id', $userID],
        ])->first();
        if ($occurrence_participants) {
            return (Boolean) true;
        }
        return (Boolean) false;
    }

    public function destroy($id)
    {
        try {
            $occurrence = $this->show($id);
            $occurrence->delete();
            return $occurrence;
        } catch (\Throwable $th) {
            return false;
        }
       
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'title' => 'required',
                'deadline' => 'required',
                'receiver' => 'required',
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
