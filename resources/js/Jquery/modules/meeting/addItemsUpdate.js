var base_url = window.location.origin;
/**
 * Adicionar itens aos assuntos abordados.
 */
export function addItemTopicsCovered() {
    //adicionar item ASSUNTOS ABORDADOS

    var count = 0;
    $("#addItemTopics_covered").click(function () {
        var topics_covered = "<div class='col-sm-6'>" +
            "<div class='card card-secondary shadow-lg' style='transition: all 0.15s ease 0s; height: inherit; width: inherit;'> " +
            "<div class='card-header'>" +
            "<h3 class='card-title'>Item</h3>" +
            "<div class='card-tools'>" +
            "<button type='button' class='btn btn-tool' data-card-widget='collapse'><i class='fas fa-minus'></i></button>" +
            "<button type='button' class='btn btn-tool' data-card-widget='maximize'><i class='fas fa-expand'></i></button>" +
            "<button type='button' class='btn btn-tool' data-card-widget='remove'><i class='fas fa-times'></i></button>" +
            "</div>" +
            "</div>" +
            "<div class='card-body'>" +
            "<div class='form-group'>" +
            "<label>Assuntos Abordados</label>" +
            "<input type='text' class='form-control' name='topics_covered[]' id='topics_covered[]' placeholder='' required>" +
            "</div>" +
            "<div class='form-group'>" +
            "<label>Providências</label>" +
            "<textarea class='form-control' name='providence[]' id='providence[]' cols='30' rows='5' required></textarea>" +
            "</div>" +
            "<input type='hidden' class='IdOccurrence' name='IdOccurrence[]' id='IdOccurrence-" + count + "' value=''>" +
            "<ul class='nav nav-pills flex-column'>" +
            "<li class='nav-item active'>" +
            "<a href='#'>" +
            "<i class='fas fa-clipboard-check'></i>&nbsp;&nbsp;<a data-toggle='modal' id='selectOcurrence-" + count + "' class='selectOcurrence' data-target='#ModalSelectOcurrence' href='javascript:'>Selecionar Ocorrência</a><span class='badge bg-primary float-right' id='numberOccurrence-" + count + "'></span>" +
            "</a>" +
            "</li>" +
            "</ul>" +
            "</div>" +
            "</div>" +
            "</div>";
        $("#divRowtopics_covered").append(topics_covered);
        modalSelectOccurrence();
        count++;
    });
}
export function loadItemTopicsCovered() {
    var meeting_topics_covereds = $("#meeting_topics_covereds").val();
    $.each(JSON.parse(meeting_topics_covereds), function (index, value) {
        var count = 0;
        var topics_covered = "<div class='col-sm-6'>" +
                "<div class='card card-secondary shadow-lg' style='transition: all 0.15s ease 0s; height: inherit; width: inherit;'> " +
                "<div class='card-header'>" +
                "<h3 class='card-title'>Item</h3>" +
                "<div class='card-tools'>" +
                "<button type='button' class='btn btn-tool' data-card-widget='collapse'><i class='fas fa-minus'></i></button>" +
                "<button type='button' class='btn btn-tool' data-card-widget='maximize'><i class='fas fa-expand'></i></button>" +
                // "<button type='button' class='btn btn-tool' data-card-widget='remove'><i class='fas fa-times'></i></button>" +
                "</div>" +
                "</div>" +
                "<div class='card-body'>" +
                "<div class='form-group'>" +
                "<label>Assuntos Abordados</label>" +
                "<input type='text' class='form-control' value='"+ value.subject_addressed +"' name='topics_covered[]' id='topics_covered[]' placeholder='' required>" +
                "<input type='hidden' name='topics_covered_id[]' id='topics_covered_id' value='"+value.id+"'>" +
                "</div>" +
                "<div class='form-group'>" +
                "<label>Providências</label>" +
                "<textarea class='form-control' name='providence[]' id='providence[]' cols='30' rows='5' required>"+value.providence+"</textarea>" +
                "</div>" +
                "<input type='hidden' class='IdOccurrence' value='"+value.occurrences_id+"' name='IdOccurrence[]' id='IdOccurrence-" + count + "' value=''>" +
                "<ul class='nav nav-pills flex-column'>" +
                "<li class='nav-item active'>" +
                "<a href='#'>" +
                //"<i class='fas fa-clipboard-check'></i>&nbsp;&nbsp;<a data-toggle='modal' id='selectOcurrence-" + count + "' class='selectOcurrence' data-target='#ModalSelectOcurrence' href='javascript:'>Selecionar Ocorrência</a><span class='badge bg-primary float-right' id='numberOccurrence-" + count + "'>"+value.occurrences_id+"</span>" +
                "</a>" +
                "</li>" +
                "</ul>" +
                "</div>" +
                "</div>" +
                "</div>";
            $("#divRowtopics_covered").append(topics_covered);
            modalSelectOccurrence();
            count++;
    });
}
/**
 * Adicionar itens a pauta
 */
export function addItemTopic() {
    var countItem = 0;
    $("#addItemTopic").click(function () {
        var topic = "<tr class='item_append_topic'>" +
            "<td><input type='text' name='topic[]' id='topic[]' class='form-control form-control-sm rounded-0 indentificator-" + countItem + "' required></td>" +
            "<td>" +
            "<label class='click-upload-file-topic-" + countItem + "' style='cursor: pointer;' for='upload-file-topic-" + countItem + "'><i class='fas fa-paperclip'></i></label>&nbsp;" +
            "<label id='file-name-" + countItem + "'></label>&nbsp;&nbsp;" +
            "<input style='display:none' id='upload-file-topic-" + countItem + "' name='upload-file-topic-" + countItem + "[]' type='file'>" +
            "<a type='button' class='remove_item_topic' data-toggle='tooltip' data-placement='top' title='Excluir'><i class='far fa-trash-alt'></i></a>" +
            "</td>" +
            "</tr>";

        $("#tbodyItemTopic").append(topic);
        //remover item
        $(document).on('click', '.remove_item_topic', function () {
            $(this).closest('.item_append_topic').remove();
        });

        $(".click-upload-file-topic-" + countItem).click(function () {
            var identifierClass = $(this).attr("class");
            var numberClass = identifierClass.split('-');
            var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição 
            $("#file-name-" + selectNumber).html(1);
        });

        countItem++;
    });
}
export function loadItemTopic() {
    var meeting_subjects = $("#meeting_subjects").val();
    var countItem = 0;
    $.each(JSON.parse(meeting_subjects), function (index, value) {
        var icoDownlaod = "";
        if(!value.url_archive == ""){
            icoDownlaod = "<label style='cursor: pointer;'><a href='"+base_url+"/event/meeting/downlaod/"+value.id+"'><i class='fas fa-download'></i></a></label>&nbsp;";
        }
        var topic = "<tr class='item_append_topic'>" +
            "<td>" +
            "<input type='text' name='topic[]' id='topic[]'  value='" + value.subject + "' class='form-control form-control-sm rounded-0 indentificator-" + countItem + "' required>" +
            "<input type='hidden' name='topics_id[]' id='topics_id' value='"+value.id+"'>" +
            "</td>" +
            "<td>" +
            "<label class='click-upload-file-topic-" + countItem + "' style='cursor: pointer;' for='upload-file-topic-" + countItem + "'><i class='fas fa-paperclip'></i></label>&nbsp;" +
            icoDownlaod +
            "<label id='file-name-" + countItem + "'></label>&nbsp;&nbsp;" +
            "<input style='display:none' id='upload-file-topic-" + countItem + "' name='upload-file-topic-" + countItem + "[]' type='file'>" +
            // "<a type='button' class='remove_item_topic' data-toggle='tooltip' data-placement='top' title='Excluir'><i class='far fa-trash-alt'></i></a>" +
            "</td>" +
            "</tr>";
        $("#tbodyItemTopic").append(topic);
        $(document).on('click', '.remove_item_topic', function () {
            $(this).closest('.item_append_topic').remove();
        });
        countItem++;
    });
}
/**
 * Adicionar usuários cadastrados
 */
export function addItemRegisteredUsers() {

    $("#buttonUserRegistered").click(function () {
        var userRegistered = $("#userRegistered").val();

        $.get(base_url + "/event/meeting/getUsersRegistered/" + userRegistered, function (data) {
            const obj = JSON.parse(data);

            var html = "<div class='col-sm-6 user-block-registered'>" +
                " <div class='user-block'>" +
                "<img class='img-circle' src='/storage/" + obj.data.image + "' alt='User Image'>" +
                "<span class='username'>" + obj.data.name + "</span>" +
                "<input type='hidden' name='idUserRegistered[]' value='" + obj.data.id + "'>" +
                "<span class='description'>Sem função&nbsp;&nbsp;<a type='button' class='removeRegisteredUser' href='#'><i class='far fa-trash-alt'></i></a></span>" +
                "</div>" +
                "</div>";

            $("#registered_users").append(html);

            /** Remover participantes cadastrados */
            $(".removeRegisteredUser").click(function () {
                $(this).closest('.user-block-registered').remove();
            });
        });
    });
}
export function loadItemRegisteredUSers() {
    var meeting_registered_participants = $("#meeting_registered_participants").val();
    $.each(JSON.parse(meeting_registered_participants), function (index, value) {
        var html = "<div class='col-sm-6 user-block-registered'>" +
            " <div class='user-block'>" +
            "<img class='img-circle' src='/storage/" + value.users.image + "' alt='User Image'>" +
            "<span class='username'>" + value.users.name + "</span>" +
            "<input type='hidden' name='idUserRegistered[]' value='" + value.users.id + "'>" +
            "<span class='description'>Sem função</span>" +
            "</div>" +
            "</div>";
        $("#registered_users").append(html);

        /** Remover participantes cadastrados */
        $(".removeRegisteredUser").click(function () {
            $(this).closest('.user-block-registered').remove();
        });
    });
}
/**
 * Adicionar usuários convidados
 */
export function addItemInvitedUsers() {

    $('form[name="formAddParticipantGuest"]').submit(function (event) {
        event.preventDefault();
        var form_data = new FormData();
        form_data.append('name', $("#name").val());
        form_data.append('email', $("#email").val());
        form_data.append('telephone', $("#telephone").val());
        form_data.append('profession', $("#profession").val());

        $.ajax({
            url: base_url + "/event/meeting/store/participants",
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
                    invitedUsers(obj.data);
                    clear_form_invited();
                } else {
                    DefaultAlert("error", obj.message);
                }
            }
        });
    });
}

export function loadItemInvitedUSers() {
    var meeting_invited_participants = $("#meeting_invited_participants").val();
    $.each(JSON.parse(meeting_invited_participants), function (index, value) {
        var html = "<div class='col-sm-6 user-block-invited'>" +
            " <div class='user-block'>" +
            "<img class='img-circle' src='/" + value.participants.url_image + "' alt='User Image'>" +
            "<span class='username'>" + value.participants.name + "</span>" +
            "<input type='hidden' name='idInvitedUsers[]' value='" + value.participants.id + "'>" +
            "<span class='description'>" + value.participants.profession + "</span>" +
            "</div>" +
            "</div>";
        $("#invited_users").append(html);
        /** Remover participantes cadastrados */
        $(".remove_invited_users").click(function () {
            $(this).closest('.user-block-invited').remove();
        });
    });
}

function invitedUsers(data) {
    var html = "<div class='col-sm-6 user-block-invited'>" +
        " <div class='user-block'>" +
        "<img class='img-circle' src='/" + data.url_image + "' alt='User Image'>" +
        "<span class='username'>" + data.name + "</span>" +
        "<input type='hidden' name='idInvitedUsers[]' value='" + data.id + "'>" +
        "<span class='description'>" + data.profession + "&nbsp;&nbsp;<a type='button' class='remove_invited_users' href='#'><i class='far fa-trash-alt'></i></a></span>" +
        "</div>" +
        "</div>";
    $("#invited_users").append(html);
    /** Remover participantes cadastrados */
    $(".remove_invited_users").click(function () {
        $(this).closest('.user-block-invited').remove();
    });
}

function modalSelectOccurrence() {
    //var selectNumber = null;
    $(".selectOcurrence").click(function () {
        var identifierClass = this.id;
        var numberClass = identifierClass.split('-');
        var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição

        $(".buttonOccurrence").click(function () {
            var idOccurrence = $(".idOccurence").val();
            $("#IdOccurrence-" + selectNumber).val(idOccurrence);
            $("#numberOccurrence-" + selectNumber).html(idOccurrence);
            $("#ModalSelectOcurrence").modal('hide');
            selectNumber = null;
        });
    });



}

function clear_form_invited() {
    $("#name").val("");
    $("#email").val("");
    $("#telephone").val("");
    $("#profession").val("");
}