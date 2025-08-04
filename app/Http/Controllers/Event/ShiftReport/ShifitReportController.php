<?php

namespace App\Http\Controllers\Event\ShiftReport;

use App\Http\Controllers\Controller;
use App\Models\ShiftReport\ShiftReport;
use Illuminate\Http\Request;

class ShifitReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', ShiftReport::class);


        $shiftReport = ShiftReport::orderBy('created_at', 'desc');

        //se filtro search
        if (request()->has('search')) {

            $shiftReport = $shiftReport->whereHas('users', function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            });
        }
        $shiftReport = $shiftReport->paginate(25);
        $shiftReport->withQueryString();

        $data = $shiftReport;
        return view('event/shiftReport/list')->with(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('store', ShiftReport::class);
        $occurrences = $this->service->getOcurrence();
        return view('event/shiftReport/create')->with([
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
        $this->authorize('store', ShiftReport::class);
        $shiftReport = $this->service->store($request->all());
        if ($shiftReport) {
            echo json_encode(['success' => true, 'message' => "Cadastrado com sucesso"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erro ao cadastrar registro"]);
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
        $this->authorize('show', ShiftReport::class);
        $shiftReport = $this->service->index($id);
        $getShiftReport_frequency = $this->service->getShiftReport_frequency($id);
        $getShiftReport_extra = $this->service->getShiftReport_extra($id);
        $getShiftReport_maintenence = $this->service->getShiftReport_maintenence($id);
        $getShiftReport_customer_comp = $this->service->getShiftReport_customer_comp($id);
        $getShiftReport_comments = $this->service->getShiftReport_comments($id);
        $occurrences = $this->service->getOcurrence();
        return view('event/shiftReport/viewModal')->with([
            'data' => $shiftReport,
            'shiftReport_frequency' => $getShiftReport_frequency,
            'shiftReport_extra' => $getShiftReport_extra,
            'shiftReport_maintenence' => $getShiftReport_maintenence,
            'shiftReport_customer_comp' => $getShiftReport_customer_comp,
            'shiftReport_comments' => $getShiftReport_comments,
            'ocurrences' => $occurrences,
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
        $this->authorize('show', ShiftReport::class);
        $shiftReport = $this->service->index($id);
        $getShiftReport_frequency = $this->service->getShiftReport_frequency($id);
        $getShiftReport_extra = $this->service->getShiftReport_extra($id);
        $getShiftReport_maintenence = $this->service->getShiftReport_maintenence($id);
        $getShiftReport_customer_comp = $this->service->getShiftReport_customer_comp($id);
        $getShiftReport_comments = $this->service->getShiftReport_comments($id);
        $occurrences = $this->service->getOcurrence();

        return view('event/shiftReport/edit')->with([
            'data' => $shiftReport,
            'shiftReport_frequency' => $getShiftReport_frequency,
            'shiftReport_extra' => $getShiftReport_extra,
            'shiftReport_maintenence' => $getShiftReport_maintenence,
            'shiftReport_customer_comp' => $getShiftReport_customer_comp,
            'shiftReport_comments' => $getShiftReport_comments,
            'ocurrences' => $occurrences,
        ]);
    }

    public function tested($id)
    {
        $tested = $this->service->tested($id);
        if ($tested) {
            echo json_encode(['success' => true, 'message' => "Visto Realizado com sucesso!"]);
        } else {
            echo json_encode(['success' => true, 'message' => "Erro ao realizar o visto!"]);
        }
    }

    public function testedRemove($id)
    {
        $tested = $this->service->testedRemove($id);
        if ($tested) {
            echo json_encode(['success' => true, 'message' => "Visto Removido!"]);
        } else {
            echo json_encode(['success' => true, 'message' => "Erro ao realizar ao remover visto!"]);
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
        $this->authorize('update', ShiftReport::class);
        $shiftReport = $this->service->update($request->all());
        if ($shiftReport) {
            echo json_encode(['success' => true, 'message' => "Alterado com sucesso"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erro ao alterar registro"]);
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
        $this->authorize('delete', ShiftReport::class);
        $afectedRows = $this->service->destroy($id);
        if ($afectedRows) {
            return redirect()->route('shiftreport.list');
        } else {
            return redirect()->route('shiftreport.list')->with(['error' => "Não foi possível excluir esse registro!"]);
        }
    }
}
