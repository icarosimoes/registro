$(document).ready(function () {
    var base_url = window.location.origin;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $(document).on('click', '#ModalView', function () {
        var id = $(this).attr('data-id');
        $.get(base_url + '/event/shiftreport/view/' + id, function (response) {
            $("#insertView #trAppend").remove();
            $("#insertView").append("<div id='trAppend'>" + response + "</div>");

            //carregar itens da frequência
            var shiftReport_frequency = $("#shiftReport_frequency").val();
            $.each(JSON.parse(shiftReport_frequency), function (index, value) {
                var html = "<tr class='itemFrequency'>" +
                    "<td width='500'><input id='frequency_employee[]' value='" + value.employee + "' name='frequency_employee[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<input type='hidden' name='frequency_id[]' id='frequency_id[]' value='" + value.id + "'>" +
                    "<td width='500'><select disabled required class=' form-control form-control-sm  function' name='frequency_occupation[]'>"

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
                    // "<a href='#' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemFrequency'><i class='fas fa-trash'></i></a>" +
                    "</td>" +
                    "</tr>";
                addItem(html, "#appendFrequency", ".removeItemFrequency", ".itemFrequency");
            });
            //carregar itens extra
            var shiftReport_extra = $("#shiftReport_extra").val();
            $.each(JSON.parse(shiftReport_extra), function (index, value) {
                var html = "<tr class='itemExtra'>" +
                    "<td><input id='extra_extrawork[]' value='" + value.extrawork + "' name='extra_extrawork[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<td><input id='extra_reasons[]' value='" + value.reasons + "' name='extra_reasons[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<input type='hidden' name='extra_id[]' id='extra_id[]' value='" + value.id + "'>" +
                    "<td>" +
                    // "<a href='#' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemExtra'><i class='fas fa-trash'></i></a>" +
                    "</td>" +
                    "</tr>";
                addItem(html, "#addItemExtra", ".removeItemExtra", ".itemExtra");
            });
            //carregar itens da manutenção
            var shiftReport_maintenence = $("#shiftReport_maintenence").val();
            $.each(JSON.parse(shiftReport_maintenence), function (index, value) {
                var html = "<tr class='itemMaintenance'>" +
                    "<td width='300'><select name='maintenence_uh[]' class='form-control form-control-sm local' required>"

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
                    "</select>" +
                    "</td>" +
                    "<td><input id='maintenence_reason[]' value='" + value.reason + "' name='maintenence_reason[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<td><input id='maintenence_providence[]' value='" + value.providence + "' name='maintenence_providence[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<td>" +
                    // "<a href='#' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemMaintenance'><i class='fas fa-trash'></i></a> " +
                    // "<a href='#' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceMaintenence'><i class='fas fa-filter'></i></a> " +
                    // "<small id='showIdOccurenceMaintenence' class='badge d-none badge-success codeOccurenceMaintenence'><i class='far fa-registered'></i> " + value.occurrences_id + "</small>" +
                    "<a href='"+base_url+"/occurrence/list/edit/"+value.occurrences_id+" '><small id='showIdOccurenceMaintenence' class='badge d-none badge-success codeOccurenceMaintenence'><i class='far fa-registered'></i> " + value.occurrences_id + "</small></a>" +
                    "</td>" +
                    "</tr>";

                addItem(html, "#addItemMaintenance", ".removeItemMaintenance", ".itemMaintenance");
                if (value.occurrences_id > 0) {
                    $("#showIdOccurenceMaintenence").removeClass('d-none');
                }
            });
            //carregar reclamação do cliente
            var shiftReport_customer_comp = $("#shiftReport_customer_comp").val();
            $.each(JSON.parse(shiftReport_customer_comp), function (index, value) {
                var html = "<tr class='itemCustomerComplaint'>" +
                    "<td><input id='customer_comp_problem[]' value='" + value.problem + "' name='customer_comp_problem[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<input type='hidden' name='customer_comp_id[]' id='customer_comp_id[]' value='" + value.id + "'>" +
                    "<td><input id='customer_comp_providence[]' value='" + value.providence + "' name='customer_comp_providence[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<td>" +
                    // "<a href='#' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemCustomerComplaint'><i class='fas fa-trash'></i></a> " +
                    // "<a href='#' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceCustomerComp'><i class='fas fa-filter'></i></a> " +
                    "<a href='" + base_url + "/occurrence/list/edit/" + value.occurrences_id + " '><small id='showIdOccurenceCustomerComp' class='badge d-none badge-success codeOccurenceCustomerComp'><i class='far fa-registered'></i> " + value.occurrences_id + "</small></a>" +
                    "</td>" +
                    "</tr>";
                addItem(html, "#addCustomerComplaint", ".removeItemCustomerComplaint", ".itemCustomerComplaint");
                if (value.occurrences_id > 0) {
                    $("#showIdOccurenceCustomerComp").removeClass('d-none');
                }
            });
            //carregar observações
            var shiftReport_comments = $("#shiftReport_comments").val();
            $.each(JSON.parse(shiftReport_comments), function (index, value) {
                var html = "<tr class='itemComments'>" +
                    "<td><input id='comments[]' value='" + value.comments + "' name='comments[]' type='text' class='form-control form-control-sm' required></td>" +
                    "<input type='hidden' name='comments_id[]' id='comments_id[]' value='" + value.id + "'>" +
                    "<td>" +
                    //"<a href='#' data-toggle='tooltip' data-placement='top' title='Excluir' class='btn btn-sm btn-default removeItemComments'><i class='fas fa-trash'></i></a> " +
                    //"<a href='#' data-toggle='modal' data-target='#ModalSelectOcurrence' class='btn btn-sm btn-default searchItemOccurenceComments'><i class='fas fa-filter'></i></a> " +
                    "<a href='" + base_url + "/occurrence/list/edit/" + value.occurrences_id + " '><small id='showIdOccurenceComments' class='badge d-none badge-success codeOccurenceComments'><i class='far fa-registered'></i> " + value.occurrences_id + "</small></a>" +
                    "</td>" +
                    "</tr>";
                addItem(html, "#addComments", ".removeItemComments", ".itemComments");
                if (value.occurrences_id > 0) {
                    $("#showIdOccurenceComments").removeClass('d-none');
                }
            });

            $('#ModalViewId').modal('show');
        });

    });

    $(document).on('change', '.tested', function (e) {
        var id = $(this).attr('data-id');

        if ($('#visto-' + id).is(':checked')) {
            $.get(base_url + '/event/shiftreport/tested/' + id, function (response) {
                var obj = JSON.parse(response);
                if (obj.success == true) {
                    DefaultAlert("success", obj.message);
                }
            });
        } else {
            $.get(base_url + '/event/shiftreport/tested/remove/' + id, function (response) {
                var obj = JSON.parse(response);
                if (obj.success == true) {
                    DefaultAlert("error", obj.message);
                }
            });
        }
    });

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