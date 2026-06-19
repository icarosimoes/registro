var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {

    //Initialize Select2 Elements
    $('.select2').select2({
        theme: 'bootstrap4',
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    //Adicionar Itens 
    $("#addFrequency").click(function () {
        var html = "<tr class='itemFrequency'>" +
            "<td width='500'><input id='frequency_employee[]' name='frequency_employee[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td width='500'><select name='frequency_occupation[]' class='form-control form-control-sm function'></select> </td>" +
            "<td></td>" +
            "<td class='text-center'>" +
            "<button  data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemFrequency'><i class='fas fa-trash'></i></button>" +
            "</td>" +
            "</tr>";
        addItem(html, "#appendFrequency", ".removeItemFrequency", ".itemFrequency");
        activeSelectFunction()
    });

    //carregar itens da frequência
    var shiftReport_frequency = $("#shiftReport_frequency").val();

    $.each(JSON.parse(shiftReport_frequency), function (index, value) {
        var html = "<tr class='itemFrequency'>" +
            "<td width='500'><input id='frequency_employee[]' value='" + value.employee + "' name='frequency_employee[]' type='text' class='form-control form-control-sm' required></td>" +
            "<input type='hidden' name='frequency_id[]' id='frequency_id[]' value='" + value.id + "'>" +
            "<td width='500'><select required class='form-control function' name='frequency_occupation[]'>"

        if (value.func) {
            html += "<option value='" + value.func.id + "' >" + value.func.id + " - " + value.func.name + "</option>"
        }

        html += "</select>" +
            // "<input  value='" + value.occupation + "' type='text' class='form-control form-control-sm' required>"+
            "</td>"

        if (value.occupation) {
            html += "<td width='100'><input  readonly value='" + value.occupation + "' type='text' class='form-control form-control-sm' ></td>"
        } else {
            html += "<td></td>"
        }

        // "<td width='100'><input  readonly value='" + value.occupation + "' type='hidden' class='form-control form-control-sm' ></td>" +
        html += "<td class='text-center'>" +
            "<button data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemFrequency'><i class='fas fa-trash'></i></button>" +
            "</td>" +
            "</tr>";

        addItem(html, "#appendFrequency", ".removeItemFrequency", ".itemFrequency");

    });
    activeSelectFunction()


    $("#addExtra").click(function () {
        var html = "<tr class='itemExtra'>" +
            "<td width='500'><input id='extra_extrawork[]' name='extra_extrawork[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td width='500'><input id='extra_reasons[]' name='extra_reasons[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td class='text-center'>" +
            "<button  data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemExtra'><i class='fas fa-trash'></i></button>" +
            "</td>" +
            "</tr>";
        addItem(html, "#addItemExtra", ".removeItemExtra", ".itemExtra");
    });
    //carregar itens extra
    var shiftReport_extra = $("#shiftReport_extra").val();
    $.each(JSON.parse(shiftReport_extra), function (index, value) {
        var html = "<tr class='itemExtra'>" +
            "<td width='500'><input id='extra_extrawork[]' value='" + value.extrawork + "' name='extra_extrawork[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td width='500'><input id='extra_reasons[]' value='" + value.reasons + "' name='extra_reasons[]' type='text' class='form-control form-control-sm' required></td>" +
            "<input type='hidden' name='extra_id[]' id='extra_id[]' value='" + value.id + "'>" +
            "<td class='text-center'>" +
            "<button  data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemExtra'><i class='fas fa-trash'></i></button>" +
            "</td>" +
            "</tr>";
        addItem(html, "#addItemExtra", ".removeItemExtra", ".itemExtra");
    });

    //ADD MANUTENCAO
    var countMaintenance = 0;
    $("#addMaintenance").click(function () {
        var html = "<tr class='itemMaintenance-" + countMaintenance + "'>" +
            "<td width='300'><select name='maintenence_uh[]' class='form-control form-control-sm local' required>" +
            " <input type='hidden' name='id_oc_maintenence[]' id='id_oc_maintenence-" + countMaintenance + "' value=''></td>" +
            "<td></td>" +
            "<td>" +
            "<select id='maintenence_status[]' name='maintenence_status[]' class='form-control form-control-sm' required>" +
            "<option value='BLOQUEADO'>BLOQUEADO</option>" +
            "<option value='DISPONÍVEL'>DISPONÍVEL</option>" +
            "</select>" +
            "</td>" +
            "<td><input id='maintenence_reason[]' name='maintenence_reason[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td><input id='maintenence_providence[]' name='maintenence_providence[]' type='text' class='form-control form-control-sm' required></td>" +
            // "<td><input  id='id_oc_maintenence-"+countMaintenance+"'   name='id_oc_maintenence[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td class='text-center'>" +
            "<button  type='button' data-toggle='tooltip' data-count='" + countMaintenance + "' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemMaintenance'><i class='fas fa-trash'></i></button> " +
            "<button  type='button' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceMaintenence'><i class='fas fa-filter'></i></button> " +
            "<a  id='showIdOccurenceMaintenence-" + countMaintenance + "' class='d-none btn btn-sm btn-success codeOccurenceMaintenence-" + countMaintenance + "'><i class='far fa-registered'></i></a>" +
            "</td>" +
            "</tr>";
        addItem(html, "#addItemMaintenance", ".removeItemMaintenance", ".itemMaintenance");
        activeSelectLocal()
        countMaintenance++;
        // $(".searchItemOccurenceMaintenence").click(function () {
        //     debugger
        //     var parent_element = $(this).parent().parent().attr('class');
        //     var numberClass = parent_element.split('-');
        //     var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição
        //     $("#buttonOccurrence").click(function () {
        //         $("#showIdOccurenceMaintenence-" + selectNumber).removeClass('d-none');
        //         var idOccurence = $("#idOccurence").val();
        //         $("#id_oc_maintenence-" + selectNumber).val(idOccurence);
        //         $(".codeOccurenceMaintenence-" + selectNumber).html("<i class='far fa-registered'></i> " + idOccurence + "");
        //         $("#ModalSelectOcurrence").modal('hide');
        //         selectNumber = null;
        //     });
        // });
    });

    //carregar itens da manutenção
    var shiftReport_maintenence = $("#shiftReport_maintenence").val();
    $.each(JSON.parse(shiftReport_maintenence), function (index, value) {
        var html = "<tr class='itemMaintenance-" + countMaintenance + "'>" +
            "<td width='300'><select name='maintenence_uh[]' class='form-control local' required>"

        if (value.local) {
            html += "<option value='" + value.local.id + "'>" + value.local.id + " - " + value.local.name + "</option>"
        }

        html += "</select></td>"

        if (value.uh) {
            html += "<td width='100'><input   value='" + value.uh + "'  type='text' class='form-control form-control-sm' readonly></td>"
        } else {
            html += "<td></td>"
        }

        html += "<input type='hidden' name='maintenence_id[]' id='maintenence_id[]' value='" + value.id + "'>" +
            "<td>" +
            "<select id='maintenence_status[]' value='" + value.status + "' name='maintenence_status[]' class='form-control form-control-sm' required>" +
            "<option value='BLOQUEADO'>BLOQUEADO</option>" +
            "<option value='DISPONÍVEL'>DISPONÍVEL</option>" +
            "</select>"
        if (value.occurrences_id) {
            html += "<input type='hidden' name='id_oc_maintenence[]' id='id_oc_maintenence-" + countMaintenance + "' value='" + value.occurrences_id + "'></td>"
        } else {
            html += "<input type='hidden' name='id_oc_maintenence[]' id='id_oc_maintenence-" + countMaintenance + "' value=''></td>"
        }
        html += "<td><input id='maintenence_reason[]' value='" + value.reason + "' name='maintenence_reason[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td><input id='maintenence_providence[]' value='" + value.providence + "' name='maintenence_providence[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td class='text-center'>" +
            "<button type='button'  data-toggle='tooltip' data-placement='top' data-count='" + countMaintenance + "' title='Excluir' class='btn btn-sm btn-default removeItemMaintenance'><i class='fas fa-trash'></i></button>" +
            "<button type='button'  data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceMaintenence'><i class='fas fa-filter'></i></button> "
        if (value.occurrences_id) {
            html += "<a href='" + base_url + "/occurrence/list/edit/" + value.occurrences_id + " ' id='showIdOccurenceMaintenence-" + countMaintenance + "' class='btn btn-sm btn-success  codeOccurenceMaintenence-" + countMaintenance + "'><i class='far fa-registered'>" + value.occurrences_id + "</i></a>"
        } else {
            html += "<a  id='showIdOccurenceMaintenence-" + countMaintenance + "' class='btn btn-sm btn-success d-none codeOccurenceMaintenence-" + countMaintenance + "'><i class='far fa-registered'>" + value.occurrences_id + "</i></a>"
        }

        // "<small id='showIdOccurenceMaintenence' class='badge d-none badge-success codeOccurenceMaintenence'><i class='far fa-registered'></i> " + value.occurrences_id + "</small>" +
        html += "</td>" +
            "</tr>";



        addItem(html, "#addItemMaintenance", ".removeItemMaintenance", ".itemMaintenance");
        countMaintenance++;
        activeSelectLocal()

    });


    //remove itens de manutencao
    $(document).on('click', ".removeItemMaintenance", function () {
        let count = $(this).attr('data-count')
        $('.itemMaintenance-' + count).remove()
    })
    $(document).on('click', ".searchItemOccurenceMaintenence", function () {
        var parent_element = $(this).parent().parent().attr('class');
        var numberClass = parent_element.split('-');
        var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição
        $("#buttonOccurrence").click(function () {
            $("#showIdOccurenceMaintenence-" + selectNumber).removeClass('d-none');
            var idOccurence = $("#idOccurence").val();
            
            $("#id_oc_maintenence-" + selectNumber).val(idOccurence);
            $(".codeOccurenceMaintenence-" + selectNumber).html("<i class='far fa-registered'></i> " + idOccurence + "");
            $("#ModalSelectOcurrence").modal('hide');
            selectNumber = null;
        });
    });
    
    //reclamacao do cliente
    var countCustomerComplaint = 0;
    $("#btnAddCustomerComplaint").click(function () {
        var html = "<tr class='itemCustomerComplaint-" + countCustomerComplaint + "'>" +
            "<td><input id='customer_comp_problem[]' name='customer_comp_problem[]' type='text' class='form-control form-control-sm' required></td>" +
            "<input type='hidden' name='id_oc_customer_comp[]' id='id_oc_customer_comp-" + countCustomerComplaint + "' value=''>" +
            "<td><input id='customer_comp_providence[]' name='customer_comp_providence[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td class='text-center'>" +
            "<button type='button' data-count='"+countCustomerComplaint+"' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemCustomerComplaint'><i class='fas fa-trash'></i></button> " +
            "<button type='button' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceCustomerComp'><i class='fas fa-filter'></i></button> " +
            "<a id='showIdOccurenceCustomerComp-" + countCustomerComplaint + "' class='btn d-none btn-sm btn-success codeOccurenceCustomerComp-" + countCustomerComplaint + "'></a>" +
            "</td>" +
            "</tr>";
        addItem(html, "#addCustomerComplaint", ".removeItemCustomerComplaint", ".itemCustomerComplaint");
        countCustomerComplaint++;
        
    });

    
    //carregar reclamação do cliente
    var shiftReport_customer_comp = $("#shiftReport_customer_comp").val();
    $.each(JSON.parse(shiftReport_customer_comp), function (index, value) {
        var html = "<tr class='itemCustomerComplaint-"+countCustomerComplaint+"'>" +
            "<td><input id='customer_comp_problem[]' value='" + value.problem + "' name='customer_comp_problem[]' type='text' class='form-control form-control-sm' required>"
            if(value.occurrences_id){
               html+= "<input type='hidden' name='id_oc_customer_comp[]' id='id_oc_customer_comp-" + countCustomerComplaint + "' value='"+value.occurrences_id+"'>" 
            }else{
               html+= "<input type='hidden' name='id_oc_customer_comp[]' id='id_oc_customer_comp-" + countCustomerComplaint + "' value=''>"  
            } 
            html+="</td>" +
            "<td><input id='customer_comp_providence[]' value='" + value.providence + "' name='customer_comp_providence[]' type='text' class='form-control form-control-sm' required></td>" +
            "<td class='text-center'>" +
            "<button type='button' data-count='"+countCustomerComplaint+"' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemCustomerComplaint'><i class='fas fa-trash'></i></button> " +
            "<button type='button' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceCustomerComp'><i class='fas fa-filter'></i></button> " 
            
            if(value.occurrences_id){
                html +="<button type='button' id='showIdOccurenceCustomerComp-"+countCustomerComplaint+"' class='btn   btn-success btn-sm codeOccurenceCustomerComp-"+countCustomerComplaint+"'><i class='far fa-registered'></i>" + value.occurrences_id + "</button>" 
            }else{
                html +="<button type='button' id='showIdOccurenceCustomerComp-"+countCustomerComplaint+"' class='btn d-none  btn-success btn-sm codeOccurenceCustomerComp-"+countCustomerComplaint+"'></button>"   
            }
            
            
            html+="</td>" +
            "</tr>";
        addItem(html, "#addCustomerComplaint", ".removeItemCustomerComplaint", ".itemCustomerComplaint");
        countCustomerComplaint++;
    });

   
  
    //button de perquisar registro de reclamacao de clinte
    $(document).on('click',".searchItemOccurenceCustomerComp",function () {
        
        var parent_element = $(this).parent().parent().attr('class');
        var numberClass = parent_element.split('-');
        var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição
        $("#buttonOccurrence").click(function () {
            $("#showIdOccurenceCustomerComp-" + selectNumber).removeClass('d-none');
            var idOccurence = $("#idOccurence").val(); //modal
            $("#id_oc_customer_comp-" + selectNumber).val(idOccurence);
            $(".codeOccurenceCustomerComp-" + selectNumber).html("<i class='far fa-registered'></i> " + idOccurence + "");
            $("#ModalSelectOcurrence").modal('hide');
            selectNumber = null;
        });
    });
    
    //remove itens de reclamacao de cliente
    $(document).on('click', ".removeItemCustomerComplaint", function () {
        let count = $(this).attr('data-count')
        console.log(count)
        $('.itemCustomerComplaint-' + count).remove()
    })   

    var countComments = 0;
    $("#btnAddComments").click(function () {
        var html = "<tr class='itemComments-" + countComments + "'>" +
            "<td width='900'><input id='comments[]' name='comments[]' type='text' class='form-control form-control-sm' required></td>" +
            "<input type='hidden' name='id_oc_comments[]' id='id_oc_comments-" + countComments + "' value=''>" +
            "<td class='text-center'>" +
            "<button type='button'  data-toggle='tooltip' data-count='"+countComments+"' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemComments'><i class='fas fa-trash'></i></button> " +
            "<button type='button' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceComments'><i class='fas fa-filter'></i></button> " +
            "<a id='showIdOccurenceComments-" + countComments + "' class='btn d-none btn-success btn-sm codeOccurenceComments-" + countComments + "'></a>" +
            "</td>" +
            "</tr>";
        addItem(html, "#addComments", ".removeItemComments", ".itemComments");
        countComments++;


    });
    //carregar observações
    var shiftReport_comments = $("#shiftReport_comments").val();
    $.each(JSON.parse(shiftReport_comments), function (index, value) {
        var html = "<tr class='itemComments-"+countComments+"'>" +
            "<td><input id='comments[]' value='" + value.comments + "' name='comments[]' type='text' class='form-control form-control-sm' required>" 
            if(value.occurrences_id ){
                html += "<input type='hidden' name='id_oc_comments[]' id='id_oc_comments-" + countComments + "' value='"+value.occurrences_id +"'>"
            }else{
                html += "<input type='hidden' name='id_oc_comments[]' id='id_oc_comments-" + countComments + "' value=''>"
            }
             html +="</td> <input type='hidden' name='comments_id[]' id='comments_id[]' value='" + value.id + "'>"+
            "<td class='text-center'>"+
            "<button type='button' data-toggle='tooltip' data-count='"+countComments+"' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemComments'><i class='fas fa-trash'></i></button> " +
            "<button type='button' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceComments'><i class='fas fa-filter'></i></button> " 
            if(value.occurrences_id){
                html +="<small id='showIdOccurenceComments-"+countComments+"'  class='btn btn-sm btn-success codeOccurenceComments-"+countComments+"'><i class='far fa-registered'></i>" + value.occurrences_id + "</small>" 
            }else{
                html +="<small id='showIdOccurenceComments-"+countComments+"'  class='btn btn-sm  d-none btn-success codeOccurenceComments-"+countComments+"'></small>"  
            }
            html +="</td>" +
            "</tr>";
        addItem(html, "#addComments", ".removeItemComments", ".itemComments");
        countComments++;
    });
    
    $(document).on('click', ".removeItemComments", function () {
        let count = $(this).attr('data-count')
        $('.itemComments-' + count).remove()
    })   

    //button de perquisar registro de observacoes
    $(document).on('click',".searchItemOccurenceComments",function () {
      
        var parent_element = $(this).parent().parent().attr('class');
        var numberClass = parent_element.split('-');
        var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição
        $("#buttonOccurrence").click(function () {
            $("#showIdOccurenceComments-" + selectNumber).removeClass('d-none');
            var idOccurence = $("#idOccurence").val(); //modal
            $("#id_oc_comments-" + selectNumber).val(idOccurence);
            $(".codeOccurenceComments-" + selectNumber).html("<i class='far fa-registered'></i> " + idOccurence + "");
            $("#ModalSelectOcurrence").modal('hide');
            selectNumber = null;
        });
    });
    
    //CRIAR RELATÓRIO DE TURNO
    $('form[name="formShiftReportEdit"]').submit(function (event) {
        event.preventDefault();
        var form_data = new FormData();
        var valid = 0;
        // CABEÇALHO
        form_data.append('shiftReport_id', $("#shiftReport_id").val());
        form_data.append('beginning', $("#beginning").val());
        form_data.append('end', $("#end").val());
        form_data.append('supervisor', $("#supervisor").val());
        form_data.append('return_of_customers', $('#return_of_customers').val());
        form_data.append('inputQuantity', $('#inputQuantity').val());
        form_data.append('outputQuantity', $('#outputQuantity').val());
        //form_data.append('photo', $('#photo').prop('files')[0]);

        //VALIDAÇÕES
        if (!$('input[name="frequency_employee[]"]').length) {
            valid = 1;
            DefaultAlert('error', "<strong>Opps!</strong> Todos os campos da 'FREQUÊNCIA' são obrigatórios.")
            // $("#alertError").removeClass('d-none');
            // $("#alertError").html("<strong>Opps!</strong> Todos os campos da 'FREQUÊNCIA' são obrigatórios.");
        }
        // if (!$('input[name="extra_extrawork[]"]').length) {
        //     valid = 1;
        //     $("#alertError").removeClass('d-none');
        //     $("#alertError").html("<strong>Opps!</strong> Todos os campos da 'EXTRA' são obrigatórios.");
        // }
        // if (!$('input[name="maintenence_uh[]"]').length) {
        //     valid = 1;
        //     $("#alertError").removeClass('d-none');
        //     $("#alertError").html("<strong>Opps!</strong> Todos os campos da 'MANUTENÇÃO' são obrigatórios.");
        // }
        // if (!$('input[name="customer_comp_problem[]"]').length) {
        //     valid = 1;
        //     $("#alertError").removeClass('d-none');
        //     $("#alertError").html("<strong>Opps!</strong> Todos os campos da 'RECLAMAÇÃO DO CLIENTE' são obrigatórios.");
        // }

        //FREQUÊNCIA
        var frequency_employee = new Array();
        $('input[name="frequency_employee[]"]').each(function () {
            frequency_employee.push($(this).val());
        });
        form_data.append('frequency_employee[]', frequency_employee);

        var frequency_occupation = new Array();
        $('select[name="frequency_occupation[]"]').each(function () {
            if ($(this).val()) {
                frequency_occupation.push($(this).val());
            }
        });
        form_data.append('frequency_occupation[]', frequency_occupation);

        var frequency_id = new Array();
        $('input[name="frequency_id[]"]').each(function () {
            frequency_id.push($(this).val());
        });
        form_data.append('frequency_id[]', frequency_id);

        //EXTRA
        var extra_extrawork = new Array();
        $('input[name="extra_extrawork[]"]').each(function () {
            extra_extrawork.push($(this).val());
        });
        form_data.append('extra_extrawork[]', extra_extrawork);

        var extra_reasons = new Array();
        $('input[name="extra_reasons[]"]').each(function () {
            extra_reasons.push($(this).val().replace(",", "-"));
        });
        form_data.append('extra_reasons[]', extra_reasons);

        var extra_id = new Array();
        $('input[name="extra_id[]"]').each(function () {
            extra_id.push($(this).val().replace(",", "-"));
        });
        form_data.append('extra_id[]', extra_id);

        //MANUTENÇÃO
        var maintenence_uh = new Array();
        $('select[name="maintenence_uh[]"]').each(function () {
            maintenence_uh.push($(this).val());
        });
        form_data.append('maintenence_uh[]', maintenence_uh);

        var maintenence_status = new Array();
        $('select[name="maintenence_status[]"]').each(function () {
            maintenence_status.push($(this).val());
        });
        form_data.append('maintenence_status[]', maintenence_status);

        var maintenence_reason = new Array();
        $('input[name="maintenence_reason[]"]').each(function () {
            maintenence_reason.push($(this).val());
        });
        form_data.append('maintenence_reason[]', maintenence_reason);

        var maintenence_providence = new Array();
        $('input[name="maintenence_providence[]"]').each(function () {
            maintenence_providence.push($(this).val().replace(",", "-"));
        });
        form_data.append('maintenence_providence[]', maintenence_providence);

        var id_oc_maintenence = new Array();
        $('input[name="id_oc_maintenence[]"]').each(function () {
            if ($(this).val()) {
                id_oc_maintenence.push($(this).val());
            } else {
                id_oc_maintenence.push(0);
            }
        });
        form_data.append('id_oc_maintenence[]', id_oc_maintenence);

        var maintenence_id = new Array();
        $('input[name="maintenence_id[]"]').each(function () {
            maintenence_id.push($(this).val());
        });
        form_data.append('maintenence_id[]', maintenence_id);


        //RECLAMAÇÃO DO CLIENTE
        var customer_comp_problem = new Array();
        $('input[name="customer_comp_problem[]"]').each(function () {
            customer_comp_problem.push($(this).val().replace(",", "-"));
        });
        form_data.append('customer_comp_problem[]', customer_comp_problem);

        var customer_comp_providence = new Array();
        $('input[name="customer_comp_providence[]"]').each(function () {
            customer_comp_providence.push($(this).val().replace(",", "-"));
        });
        form_data.append('customer_comp_providence[]', customer_comp_providence);

        var id_oc_customer_comp = new Array();
        $('input[name="id_oc_customer_comp[]"]').each(function () {
            if ($(this).val()) {
                id_oc_customer_comp.push($(this).val());
            } else {
                id_oc_customer_comp.push(0);
            }
        });
        form_data.append('id_oc_customer_comp[]', id_oc_customer_comp);

        var customer_comp_id = new Array();
        $('input[name="customer_comp_id[]"]').each(function () {
            customer_comp_id.push($(this).val().replace(",", "-"));
        });
        form_data.append('customer_comp_id[]', customer_comp_id);

        // //OBSERVAÇÕES
        var comments = new Array();
        $('input[name="comments[]"]').each(function () {
            comments.push($(this).val().replace(",", "-"));
        });
        form_data.append('comments[]', comments);

        var id_oc_comments = new Array();
        $('input[name="id_oc_comments[]"]').each(function () {
            if ($(this).val()) {
                id_oc_comments.push($(this).val());
            } else {
                id_oc_comments.push(0);
            }
        });
        form_data.append('id_oc_comments[]', id_oc_comments);

        var comments_id = new Array();
        $('input[name="comments_id[]"]').each(function () {
            comments_id.push($(this).val());
        });
        form_data.append('comments_id[]', comments_id);

        if (valid === 0) {
            $('.overlay').removeClass('d-none');
            $.ajax({
                url: base_url + "/event/shiftreport/update",
                type: "POST",
                data: form_data,
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                enctype: 'multipart/form-data',
                success: function (response) {
                    const obj = JSON.parse(response);
                    if (obj.success === true) {
                        DefaultAlert("success", obj.message);
                        $('.overlay').addClass('d-none');
                        window.location.replace(base_url + "/event/list/shiftreport");
                    } else {
                        DefaultAlert("error", obj.message);
                        $('.overlay').addClass('d-none');
                    }
                }
            }).catch(() => {
                DefaultAlert('error', 'Não foi possível salvar')
            }).always(() => {
                $('.overlay').addClass('d-none');
            })
        }
    });
  
const $modalSelectOccurrence = $('#ModalSelectOcurrence');
    $('#idOccurence').select2({
        theme: 'bootstrap4',
      dropdownParent: $modalSelectOccurrence,
        ajax: {
          url: base_url+'/helper/get_occurrences',
          dataType: 'json',

            data: function (params) {
            var query = {
              term: params.term,
              page: params.page || 1
            }

            // Query parameters will be ?search=[term]&page=[page]
            return query;
          },
          processResults: function (response) {
            //se a primeira paginacao
            if (response.current_page == 1){ data_select = response.data }
            else{ data_select = data_select.concat(response.data) }

            // Transforms the top-level key of the response object from 'items' to 'results'
             let more_pagination = true;
             //se não tem mais paginas
             if (response.next_page_url == null){ more_pagination = false }
             return {
                 results:response.data,
                 pagination: {
                    "more": more_pagination
                  }
                }
           }
        }
    });
    function activeSelectLocal() {
        $('.local').select2({
            theme: 'classic',
            ajax: {
                url: base_url + '/helper/get_locals',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (response) {

                    // Transforms the top-level key of the response object from 'items' to 'results'
                    let more_pagination = true;
                    //se não tem mais paginas
                    if (response.next_page_url == null) {
                        more_pagination = false
                    }
                    return {
                        results: response.data,
                        pagination: {
                            "more": more_pagination
                        }
                    }
                }
            }
        })
    }
    //ativa select 2 nos select de funcao
    function activeSelectFunction() {
        $('.function').select2({
            theme: 'classic',
            ajax: {
                url: base_url + '/helper/get_functions',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (response) {

                    // Transforms the top-level key of the response object from 'items' to 'results'
                    let more_pagination = true;
                    //se não tem mais paginas
                    if (response.next_page_url == null) {
                        more_pagination = false
                    }
                    return {
                        results: response.data,
                        pagination: {
                            "more": more_pagination
                        }
                    }
                }
            }
        })
    }

    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

    function addItem(html, IdAppend, btnClassRemove, classItemRemove) {
        $(IdAppend).append(html);
        $(btnClassRemove).click(function () {
            $(this).closest(classItemRemove).remove();
        });
    }
});