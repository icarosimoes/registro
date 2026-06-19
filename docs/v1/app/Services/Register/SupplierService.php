<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\InputGroup;
use App\Models\Contact;
use App\Models\Register\SupplierInputGroup;
use App\Models\Register\Supplier;
use App\Services\Service;
use App\Models\ConsultCNPJ;
use Illuminate\Support\Facades\Validator;


class SupplierService extends Service  
{

    public function index()
    {
        $supplier = Supplier::All();
        return $supplier;
    }

    public function show(int $id) 
    {
        return Supplier::findOrFail($id);
    }

    public function getInputGroup() 
    {
        return InputGroup::all();
    }
    public function getSplliersInputGroup($id) 
    {
        return SupplierInputGroup::where([['suppliers_id', $id]])->get();
    }

    public function getCNPJ($data)
    {
        $cnpj = preg_replace("/[^0-9]/", "", $data['cnpj']);
        $revenueData = ConsultCNPJ::getCNPJ($cnpj);
        return $revenueData;
    }

    public function store(Array $data)
    {
            $this->validate($data);
            $supplier = new Supplier();
            $supplier->cnpj  = preg_replace("/[^0-9]/", "", $data['cnpj']);
            $supplier->fantasy_name = $data['fantasy_name'];
            $supplier->company_name = $data['company_name'];
            $supplier->state_registration = $data['state_registration'];
            $supplier->municipal_registration = $data['municipal_registration'];
            $supplier->state = $data['state'];
            $supplier->cep = $data['cep'];
            $supplier->address = $data['address'];
            $supplier->email = $data['email'];
            $supplier->city = $data['city'];
            $supplier->save();
            $insertID = $supplier->id;
            //insert contacts
            $contact_name = explode(',', $data['contact_name'][0]);
            $contact_occupation = explode(',', $data['contact_occupation'][0]);
            $contact_telephone = explode(',', $data['contact_telephone'][0]);
            $contact_email = explode(',', $data['contact_email'][0]);
            
            
            for($i=0; $i < count($contact_name); $i++){
                $data = [
                    'name' => $contact_name[$i],
                    'occupation' => $contact_occupation[$i],
                    'telephone' => $contact_telephone[$i],
                    'email' => $contact_email[$i],
                    'supplier_id' => $insertID
                ];
               Contact::insert($data);
            }
            
            if(!empty($data['input_group'])){
            //input_group
            $input_group = explode(',', $data['input_group']);

            for($i=0; $i < count($input_group); $i++){
                $data = [
                    'suppliers_id' => $insertID,
                    'input_groups_id' => $input_group[$i]
                ];
                SupplierInputGroup::insert($data);
            }
        }
            return $supplier;
    }
    
    public function update(Array $data)
    {
        $this->validate($data); 
        
        $supplier = $this->show($data['id']);

        $supplier->cnpj = preg_replace("/[^0-9]/", "", $data['cnpj']) ?? $supplier->cnpj;
        $supplier->company_name = $data['company_name'] ?? $supplier->company_name;
        $supplier->fantasy_name = $data['fantasy_name'] ?? $supplier->fantasy_name;
        $supplier->state_registration = $data['state_registration'] ?? $supplier->state_registration;
        $supplier->municipal_registration = preg_replace("/[^0-9]/", "", $data['municipal_registration']) ?? $supplier->municipal_registration;
        $supplier->state = $data['state'] ?? $supplier->state;
        $supplier->address = $data['address'] ?? $supplier->address;
        $supplier->email = $data['email'] ?? $supplier->email;
        $supplier->city = $data['city'] ?? $supplier->city;
        $supplier->cep = $data['cep'] ?? $supplier->cep;
        $supplier->save();

        $input_group = explode(',', $data['input_group']);
        $this->deleteSupplierInputGroup($supplier->id);
        for($i=0; $i < count($input_group); $i++){
            $data = [
                'suppliers_id' => $supplier->id,
                'input_groups_id' => $input_group[$i]
            ];
            SupplierInputGroup::insert($data);
        }

        return $supplier;
    }

    public function deleteSupplierInputGroup($suppliers_id)
    {
        $supplierInputGroup = SupplierInputGroup::where([['suppliers_id', $suppliers_id]])->delete();
        return $supplierInputGroup;
    }

    // START | ENDPOINTS CONTACTS
    public function getContacts($id)
    {
        $contact = Contact::where([['supplier_id', $id]])->get();
        return $contact;
    }
    public function show_contacts($id)
    {
        $contact = Contact::findOrFail($id);
        return $contact;
    }

    public function store_contacts($data)
    {
        $contact = new Contact();
        $contact->name = $data['name'];
        $contact->occupation = $data['occupation'];
        $contact->telephone = $data['telephone'];
        $contact->email = $data['email'];
        $contact->supplier_id = $data['supplier_id'];
        $contact->save();
        return $contact;
    }
    public function update_contacts($data)
    {
        $contact = $this->show_contacts($data['id']);

        $contact->name = $data['name'] ?? $contact->name;
        $contact->occupation = $data['occupation'] ?? $contact->occupation;
        $contact->telephone = $data['telephone'] ?? $contact->telephone;
        $contact->email = $data['email'] ?? $contact->email;

        $contact->save();

        return $contact;
    }

    public function destroy_destroy($id)
    {
        $contact = $this->show_contacts($id);
        $contact->delete();
        return $contact;
    }
    // END | ENDPOINTS CONTACTS

    public function destroy_all_contacts($id)
    {
        $contact = Contact::where([['supplier_id', $id]])->delete();
        return $contact;
    }
    public function destroy_all_supplier_input_group($id)
    {
        $supplierInputGroup = SupplierInputGroup::where([['suppliers_id', $id]])->delete();
        return $supplierInputGroup;
    }
    public function destroy($id)
    {
        try {
        $this->destroy_all_contacts($id);
        $this->destroy_all_supplier_input_group($id);
        $client = $this->show($id);
        $client->delete();
        return $client;
    } catch (\Exception $e) {
          
        //return $e->getMessage();
        return redirect()->back()->with('alert', 'Desculpe, não é possível excluir o dado selecionado, Verifique se o dado não está sendo ultilizado em outras tabelas!');
    }
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'cnpj' => 'required', 
                'fantasy_name' => 'required',
                'company_name' => 'required', 
                'state_registration' => 'required',
                'municipal_registration' => 'required',
                'state' => 'required',
                'address' => 'required',
                'email' => 'required',
                'city' => 'required',
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