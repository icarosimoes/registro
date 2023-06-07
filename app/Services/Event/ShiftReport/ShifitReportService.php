<?php
namespace App\Services\Event\ShiftReport;

use App\Models\Occurrence;
use App\Models\ShiftReport\ShiftReport;
use App\Models\ShiftReport\ShiftReport_comments;
use App\Models\ShiftReport\ShiftReport_customer_complaint;
use App\Models\ShiftReport\ShiftReport_extra;
use App\Models\ShiftReport\ShiftReport_frequency;
use App\Models\ShiftReport\ShiftReport_maintenence;
use App\Services\Service;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ShifitReportService extends Service
{
    public function index(int $id = null)
    {
        if (isset($id)) {
            $shiftReport = ShiftReport::findOrFail($id);
        } else {
            $shiftReport = ShiftReport::all()->sortByDesc('created_at');
        }
        return $shiftReport;
    }
    public function getOcurrence()
    {
        $occurrence = Occurrence::all();
        return $occurrence;
    }

    public function getShiftReport_frequency($id)
    {
        $shiftReport_frequency = ShiftReport_frequency::with('func')->where([['shift_reports_id', $id]])->get();
        return $shiftReport_frequency;
    }
    public function getShiftReport_extra($id)
    {
        $shiftReport_extra = ShiftReport_extra::where([['shift_reports_id', $id]])->get();
        return $shiftReport_extra;
    }
    public function getShiftReport_maintenence($id)
    {
        $shiftReport_maintenence = ShiftReport_maintenence::with('local')->where([['shift_reports_id', $id]])->get();
        return $shiftReport_maintenence;
    }
    public function getShiftReport_customer_comp($id)
    {
        $shiftReport_customer_complaint = ShiftReport_customer_complaint::where([['shift_reports_id', $id]])->get();
        return $shiftReport_customer_complaint;
    }
    public function getShiftReport_comments($id)
    {
        $shiftReport_comments = ShiftReport_comments::where([['shift_reports_id', $id]])->get();
        return $shiftReport_comments;
    }

    public function store(array $data)
    {
        //Frequência
        $frequency_employee = explode(",", $data['frequency_employee'][0]);
        $frequency_occupation = explode(",", $data['frequency_occupation'][0]);
        //Extra
        $extra_extrawork = explode(",", $data['extra_extrawork'][0]);
        $extra_reasons = explode(",", $data['extra_reasons'][0]);
        //Manutenção
        $maintenence_uh = explode(",", $data['maintenence_uh'][0]);
        $maintenence_status = explode(",", $data['maintenence_status'][0]);
        $maintenence_reason = explode(",", $data['maintenence_reason'][0]);
        $maintenence_providence = explode(",", $data['maintenence_providence'][0]);
        $id_oc_maintenence = explode(",", $data['id_oc_maintenence'][0]);
        //Reclamação do cliente
        $customer_comp_problem = explode(",", $data['customer_comp_problem'][0]);
        $customer_comp_providence = explode(",", $data['customer_comp_providence'][0]);
        $id_oc_customer_comp = explode(",", $data['id_oc_customer_comp'][0]);
        //comments
        if (isset($data['comments'])) {
            $comments = explode(",", $data['comments'][0]);
            $id_oc_comments = explode(",", $data['id_oc_comments'][0]);
        }

        $shiftReport = new ShiftReport();
        $shiftReport->beginning = (new DateTime($data['beginning']))->format('Y-m-d H:i:s');
        $shiftReport->end = (new DateTime($data['end']))->format('Y-m-d H:i:s');
        $shiftReport->supervisor = $data['supervisor'];
        $shiftReport->return_of_customers = $data['return_of_customers'];
        $shiftReport->inputQuantity = $data['inputQuantity'];
        $shiftReport->outputQuantity = $data['outputQuantity'];
        $shiftReport->users_id = Auth::user()->id;
        $shiftReport->save();
        $insertID = $shiftReport->id;

        //Frequência
        for ($i = 0; $i < count($frequency_employee); $i++) {
            $data = [
                'shift_reports_id' => $insertID,
                'employee' => $frequency_employee[$i],
                'func_id' => $frequency_occupation[$i],
                'created_at' => Date('Y-m-d H:i:s'),
            ];
            ShiftReport_frequency::insert($data);
        }

        //extra
       
        if (!empty($extra_extrawork[0])) {
            for ($i = 0; $i < count($extra_extrawork); $i++) {
                $data = [
                    'shift_reports_id' => $insertID,
                    'extrawork' => $extra_extrawork[$i],
                    'reasons' => $extra_reasons[$i],
                    'created_at' => Date('Y-m-d H:i:s'),
                ];
                ShiftReport_extra::insert($data);
            }
        }

        //manutenção
        if (!empty($maintenence_uh[0])) {
            for ($i = 0; $i < count($maintenence_uh); $i++) {
                if ($id_oc_maintenence[$i] == 0) {
                    $data = [
                        'shift_reports_id' => $insertID,
                        'local_id' => $maintenence_uh[$i],
                        'status' => $maintenence_status[$i],
                        'reason' => $maintenence_reason[$i],
                        'providence' => $maintenence_providence[$i],
                        'created_at' => Date('Y-m-d H:i:s'),
                    ];
                } else {
                    $data = [
                        'shift_reports_id' => $insertID,
                        'uh' => $maintenence_uh[$i],
                        'status' => $maintenence_status[$i],
                        'reason' => $maintenence_reason[$i],
                        'providence' => $maintenence_providence[$i],
                        'occurrences_id' => $id_oc_maintenence[$i],
                        'created_at' => Date('Y-m-d H:i:s'),
                    ];
                }
                ShiftReport_maintenence::insert($data);
            }
        }

        //Reclamação do cliente
        if (!empty($customer_comp_problem[0])) {
            for ($i = 0; $i < count($customer_comp_problem); $i++) {
                if ($id_oc_customer_comp[$i] == 0) {
                    $data = [
                        'shift_reports_id' => $insertID,
                        'problem' => $customer_comp_problem[$i],
                        'providence' => $customer_comp_providence[$i],
                        'created_at' => Date('Y-m-d H:i:s'),
                    ];
                } else {
                    $data = [
                        'shift_reports_id' => $insertID,
                        'problem' => $customer_comp_problem[$i],
                        'providence' => $customer_comp_providence[$i],
                        'occurrences_id' => $id_oc_customer_comp[$i],
                        'created_at' => Date('Y-m-d H:i:s'),
                    ];
                }
                ShiftReport_customer_complaint::insert($data);
            }
        }

        //Observações
        if (!empty($comments[0])) {
            for ($i = 0; $i < count($comments); $i++) {
                if ($id_oc_comments[$i] == 0) {
                    $data = [
                        'shift_reports_id' => $insertID,
                        'comments' => $comments[$i],
                        'created_at' => Date('Y-m-d H:i:s'),
                    ];
                } else {
                    $data = [
                        'shift_reports_id' => $insertID,
                        'comments' => $comments[$i],
                        'occurrences_id' => $id_oc_comments[$i],
                        'created_at' => Date('Y-m-d H:i:s'),
                    ];
                }
                ShiftReport_comments::insert($data);
            }
        }

        return $shiftReport;

    }

    public function update(array $data)
    {
        DB::beginTransaction();
        //Frequência
        $frequency_id = explode(",", $data['frequency_id'][0]);
        $frequency_employee = explode(",", $data['frequency_employee'][0]);
        $frequency_occupation = explode(",", $data['frequency_occupation'][0]);
        //Extra
        $extra_id = explode(",", $data['extra_id'][0]);
        $extra_extrawork = explode(",", $data['extra_extrawork'][0]);
        $extra_reasons = explode(",", $data['extra_reasons'][0]);
        //Manutenção
        $maintenence_id = explode(",", $data['maintenence_id'][0]);
        $maintenence_uh = explode(",", $data['maintenence_uh'][0]);
        $maintenence_status = explode(",", $data['maintenence_status'][0]);
        $maintenence_reason = explode(",", $data['maintenence_reason'][0]);
        $maintenence_providence = explode(",", $data['maintenence_providence'][0]);
        $maintenence_oc = explode(",", $data['id_oc_maintenence'][0]);
         
        //Reclamação do cliente
        $customer_comp_problem = explode(",", $data['customer_comp_problem'][0]);
        $customer_comp_providence = explode(",", $data['customer_comp_providence'][0]);
        $customer_comp_id = explode(",", $data['customer_comp_id'][0]);
        //comments
        if (isset($data['comments'])) {
            $comments = explode(",", $data['comments'][0]);
            $comments_id = explode(",", $data['comments_id'][0]);
        }

        $shiftReport = $this->index($data['shiftReport_id']);
        $shiftReport->beginning = (new DateTime($data['beginning']))->format('Y-m-d H:i:s');
        $shiftReport->end = (new DateTime($data['end']))->format('Y-m-d H:i:s');
        $shiftReport->supervisor = $data['supervisor'];
        $shiftReport->return_of_customers = $data['return_of_customers'];
        $shiftReport->inputQuantity = $data['inputQuantity'];
        $shiftReport->outputQuantity = $data['outputQuantity'];
        //$shiftReport->users_id = Auth::user()->id;
        $shiftReport->save();
        $insertID = $shiftReport->id;

        //Frequência
        
        ShiftReport_frequency::where('shift_reports_id', $insertID)->delete();
        for ($i = 0; $i < count($frequency_employee); $i++) {
            $shiftReport_frequency = new ShiftReport_frequency();   
            $shiftReport_frequency->shift_reports_id = $insertID;
            $shiftReport_frequency->employee = $frequency_employee[$i];  
            $shiftReport_frequency->func_id = $frequency_occupation[$i];
            $shiftReport_frequency->occupation = null;
            $shiftReport_frequency->save();
            
        }

        //extra
        ShiftReport_extra::where('shift_reports_id',$insertID)->delete();
        if (!empty($extra_extrawork[0])) {
            for ($i = 0; $i < count($extra_extrawork); $i++) {
                $shiftReport_extra = new ShiftReport_extra();
                $shiftReport_extra->shift_reports_id = $insertID;
                $shiftReport_extra->extrawork = $extra_extrawork[$i];
                $shiftReport_extra->reasons = $extra_reasons[$i];
                $shiftReport_extra->save();
                
            }
        }
        
        //manutenção
        ShiftReport_maintenence::where('shift_reports_id', $insertID)->delete();
        if (!empty($maintenence_uh[0])) {
            for ($i = 0; $i < count($maintenence_uh); $i++) {
                $shiftReport_maintenence =  new ShiftReport_maintenence();
                $shiftReport_maintenence->shift_reports_id = $insertID; 
                $shiftReport_maintenence->local_id = $maintenence_uh[$i]; 
                $shiftReport_maintenence->uh = null; 
                $shiftReport_maintenence->status = $maintenence_status[$i]; 
                $shiftReport_maintenence->reason = $maintenence_reason[$i]; 
                $shiftReport_maintenence->providence = $maintenence_providence[$i]; 
                $shiftReport_maintenence->occurrences_id = $maintenence_oc[$i]==''?null:$maintenence_oc[$i]; 
                $shiftReport_maintenence->save();
            }
        }

        //Reclamação do cliente
        if (!empty($customer_comp_problem[0])) {
            for ($i = 0; $i < count($customer_comp_problem); $i++) {
                $data = [
                    'shift_reports_id' => $insertID,
                    'problem' => $customer_comp_problem[$i],
                    'providence' => $customer_comp_providence[$i],
                    'created_at' => Date('Y-m-d H:i:s'),
                ];
                ShiftReport_customer_complaint::where('id', $customer_comp_id[$i])->update($data);
            }
       }

        //Observações
        if (!empty($comments[0])) {
            for ($i = 0; $i < count($comments); $i++) {
                $data = [
                    'shift_reports_id' => $insertID,
                    'comments' => $comments[$i],
                    'created_at' => Date('Y-m-d H:i:s'),
                ];
                ShiftReport_comments::where('id', $comments_id[$i])->update($data);
            }
        }
        DB::commit();
        return $shiftReport;

    }

    public function tested(int $id)
    {
        $shiftReport = ShiftReport::findOrFail($id);
        $shiftReport->viewed = 1;
        $shiftReport->save();
        return $shiftReport;
    }

    public function testedRemove($id)
    {
        $shiftReport = ShiftReport::findOrFail($id);
        $shiftReport->viewed = null;
        $shiftReport->save();
        return $shiftReport;
    }

    public function destroy(int $id)
    {
        try {
            //remover relacionamentos
            ShiftReport_frequency::where('shift_reports_id', $id)->delete();
            ShiftReport_extra::where('shift_reports_id', $id)->delete();
            ShiftReport_maintenence::where('shift_reports_id', $id)->delete();
            ShiftReport_customer_complaint::where('shift_reports_id', $id)->delete();
            ShiftReport_comments::where('shift_reports_id', $id)->delete();
            $afectedRows = ShiftReport::where('id', $id)->delete();
            return $afectedRows;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
