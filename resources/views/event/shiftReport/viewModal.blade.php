{{-- cabeçalho --}}
<div class="col-md-12">
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">Relatório de Turno</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {{-- $data->beginning --}}
                        <label for="Name">Início</label>  
                        <input class="form-control" type="datetime-local" value="{{ (new DateTime($data->beginning))->format('Y-m-d\TH:i:s') }}" name="beginning" id="beginning" required>
                        <input type="hidden" name="shiftReport_id" id="shiftReport_id" value="{{ $data->id }}">
                    </div>
                    <div class="form-group">
                        <label for="Name">Supervisor:</label>
                        <input type="text" value="{{ $data->supervisor }}" class="form-control" name="supervisor" id="supervisor"
                            placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label for="Name">Quantidade de Entrada:</label>
                        <input type="number" value="{{ $data->inputQuantity }}" class="form-control" name="inputQuantity" id="inputQuantity"
                            placeholder="" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Name">Término:</label>
                        <input class="form-control" type="datetime-local"
                            value="{{ (new DateTime($data->end))->format('Y-m-d\TH:i:s') }}" name="end" id="end" required>
                    </div>
                    <div class="form-group">
                        <label for="Name">Retorno de clientes:</label>
                        <input type="number" value="{{ $data->return_of_customers }}" class="form-control" name="return_of_customers"
                            id="return_of_customers" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label for="Name">Quantidade de Saída:</label>
                        <input type="number" value="{{ $data->outputQuantity }}" class="form-control" name="outputQuantity" id="outputQuantity"
                            placeholder="" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end cabeçalho --}}

{{-- FREQUÊNCIA --}}
<div class="col-md-12">
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">Frequência</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Função</th>
                            
                        </tr>
                    </thead>
                    <tbody id="appendFrequency">
                       <input type="hidden" name="shiftReport_frequency" id="shiftReport_frequency" value="{{ $shiftReport_frequency }}">
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>{{-- end FREQUÊNCIA --}}

{{-- EXTRA --}}
<div class="col-md-12">
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">Extra</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                    <thead>
                        <tr>
                            <th>Mão de obra extra</th>
                            <th>Motivo</th>
                            
                        </tr>
                    </thead>
                    <tbody id="addItemExtra">
                        <input type="hidden" name="shiftReport_extra" id="shiftReport_extra" value="{{ $shiftReport_extra }}">
                        
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>{{-- end extra --}}

{{-- MANUTENÇÃO --}}
<div class="col-md-12">
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">Manutenção</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                    <thead>
                        <tr>
                            <th>UH</th>
                            <th>Status</th>
                            <th>Motivo</th>
                            <th>Providência</th>
                            
                        </tr>
                    </thead>
                    <tbody id="addItemMaintenance">
                        <input type="hidden" name="shiftReport_maintenence" id="shiftReport_maintenence" value="{{ $shiftReport_maintenence }}">
                        
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>{{-- end manutenção --}}

{{-- RECLAMAÇÃO DO CLIENTE --}}
<div class="col-md-12">
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">Reclamação do cliente</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                    <thead>
                        <tr>
                            <th>Problema</th>
                            <th>Providências</th>
                            
                        </tr>
                    </thead>
                    <tbody id="addCustomerComplaint">
                        <input type="hidden" name="shiftReport_customer_comp" id="shiftReport_customer_comp" value="{{ $shiftReport_customer_comp }}">
                       
                    </tbody>
                </table>
            </div>
        </div>
   
    </div>
</div>{{-- end RECLAMAÇÃO DO CLIENTE --}}

{{-- OBSERVAÇÕES --}}
<div class="col-md-12">
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">Observações</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                    <thead>
                        <tr>
                            <th>Observações</th>
                            
                        </tr>
                    </thead>
                    <tbody id="addComments">
                        <input type="hidden" name="shiftReport_comments" id="shiftReport_comments" value="{{ $shiftReport_comments }}">
                        
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>{{-- end OBSERVAÇÕES --}}
