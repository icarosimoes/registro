<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\Client;
use App\Services\Service;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class ClientService extends Service  
{

    public function index()
    {
        $client = Client::All();
        return $client;
    }

    public function show(int $id) 
    {
        return Client::findOrFail($id);
    }

    public function store(Array $data)
    {
           $this->validate($data);
            $client = new Client();
            $client->nome  = $data['nome'];
            $client->inscricaoMunicipal = $data['inscricaoMunicipal']; 
            $client->email = $data['email'];
            $cep = preg_replace("/[^0-9]/", "", $data['cep']);
            $client->cep = $cep;
            $cpf_cnpj = preg_replace("/[^0-9]/", "", $data['cpf_cnpj']); 
            $client->cpf_cnpj = $cpf_cnpj;
            $client->endereco = $data['endereco'];
            $client->inscricaoEstadual = $data['inscricaoEstadual'];
            $telefone = preg_replace("/[^0-9]/", "", $data['telefone']); 
            $client->telefone = $telefone;
            $client->save();
            return $client;
    }
    
    public function update(Array $data)
    {
        $this->validate($data); 
        
        $client = $this->show($data['id']);

        $client->nome = $data['nome'] ?? $client->nome;
        $client->inscricaoMunicipal = $data['inscricaoMunicipal'] ?? $client->inscricaoMunicipal;
        $client->email = $data['email'] ?? $client->email;
        $cep = preg_replace("/[^0-9]/", "", $data['cep']);
        $client->cep = $cep ?? $client->cep;
        $cpf_cnpj = preg_replace("/[^0-9]/", "", $data['cpf_cnpj']); 
        $client->cpf_cnpj = $cpf_cnpj ?? $client->cpf_cnpj;
        $client->endereco = $data['endereco'] ?? $client->endereco;
        $client->inscricaoEstadual = $data['inscricaoEstadual'] ?? $client->inscricaoEstadual;
        $telefone = preg_replace("/[^0-9]/", "", $data['telefone']); 
        $client->telefone = $telefone;
        $client->save();

        return $client;
    }

    public function destroy($id)
    {
        $client = $this->show($id);
        $client->delete();
        return $client;
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return $user;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'nome' => 'required', 
                'inscricaoMunicipal' => 'required',
                'email' => 'required', 
                'cep' => 'required',
                'cpf_cnpj' => 'required',
                'endereco' => 'required',
                'inscricaoEstadual' => 'required',
                'telefone' => 'required' 
				
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