import { addItemTopicsCovered } from './addItemsUpdate.js';
import { loadItemTopicsCovered } from './addItemsUpdate.js';
import { addItemTopic } from './addItemsUpdate.js';
import { loadItemTopic } from './addItemsUpdate.js';
import { addItemRegisteredUsers } from './addItemsUpdate.js';
import { loadItemRegisteredUSers } from './addItemsUpdate.js';
import { addItemInvitedUsers } from './addItemsUpdate.js';
import { loadItemInvitedUSers } from './addItemsUpdate.js';

var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    /**  addItemTopicsCovered() - Inserir itens aos assuntos abordados */
    addItemTopicsCovered();
    loadItemTopicsCovered();
    /** addItemTopic() - Inserir itens a pauta */
    addItemTopic();
    //carregar os topicos da pauta
    loadItemTopic();
    /** addItemRegisteredUsers() - adicionar participante cadastrado */
    addItemRegisteredUsers();
    loadItemRegisteredUSers();
    /** addItemInvitedUsers() - adicionar participante convidados */
    addItemInvitedUsers();
    loadItemInvitedUSers();
    /** Alterar uma reunião */
    
    //Inicialização Select2 Elemento
    $('.select2').select2({
        theme: 'bootstrap4',
    });

    //Inicialização Toast
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $('form[name="formMeetingEdit"]').submit(function (event) {

        event.preventDefault();
        var count = 0;
        var selectNumberArray = new Array();
        var form_data = new FormData();
        var valid = 0;

        //Validações
        if (!$('input[name="topic[]"]').length) {
            valid = 1;
            $("#alertError").removeClass('d-none');
            $("#alertError").html("<strong>Opps!</strong> Todos os campos da 'PAUTA' são obrigatórios.");
        }
        if (!$('input[name="topics_covered[]"]').length) {
            valid = 1;
            $("#alertError").removeClass('d-none');
            $("#alertError").html("<strong>Opps!</strong> Todos os campos dos 'ASSUNTOS ABORDADOS' são obrigatórios.");
        }
        if (!$('input[name="idUserRegistered[]"]').length) {
            valid = 1;
            $("#alertError").removeClass('d-none');
            $("#alertError").html("<strong>Opps!</strong> Todos os campos dos 'USUÁRIOS' cadastrados são obrigatórios.");
        }

        $('input[name="IdOccurrence[]"]').each(function () {
            if (this.value <= 0) {
                valid = 1;
                $("#alertError").removeClass('d-none');
                $("#alertError").html("<strong>Opps!</strong> Ao criar um item 'ASSUNTOS ABORDADOS' torna se obrigatório selecionar uma ocorrência ao item.");
            }
        });


        var topics = new Array();
        $('input[name="topic[]"]').each(function () {
            topics.push($(this).val());

            /** O objetivo a baixo é identificar o numero alocado nas classes dos itens e armazenar no array {selectNumberArray} */
            var identifierClass = $(this).attr("class");
            var numberClass = identifierClass.split('-');
            var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição 
            selectNumberArray.push(selectNumber);
        });
        form_data.append('topics[]', topics);
        /**
         * A regra a baixo tem como obejtivo identificar os itens que tem Anexos, o {for} 
         * percorre os itens criados, trazendo na classe um indetificador de cada registro.
         * {selectNumberArray} tem a função de trazer o identificador das classes dos itens da 
         * pauta.
         * {fileEmpty} Variavel do tipo File vazio, para identificar os intens sem anexo.
         */
        var fileEmpty = new File([""], "empty");
        for (var i = 0; i < selectNumberArray.length; i++) {
            $('input[name="upload-file-topic-' + selectNumberArray[i] + '[]"]').each(function () {
                var prop = $(this).prop('files');
                if (prop[0]) {
                    form_data.append('files[]', prop[0]);
                } else {
                    form_data.append('files[]', fileEmpty);
                }
            });
        }

        var topics_covered = new Array();
        $('input[name="topics_covered[]"]').each(function () {
            topics_covered.push($(this).val());
        });
        form_data.append('topics_covered[]', topics_covered);

        var providence = new Array();
        $('textarea[name="providence[]"]').each(function () {
            providence.push($(this).val());
        });
        form_data.append('providence[]', providence);

        var users_registered = new Array();
        $('input[name="idUserRegistered[]"]').each(function () {
            users_registered.push($(this).val());
        });
        form_data.append('users_registered[]', users_registered);

        var invited_users = new Array();
        $('input[name="idInvitedUsers[]"]').each(function () {
            invited_users.push($(this).val());
        });
        form_data.append('invited_users[]', invited_users);

        var IdOccurrence = new Array();
        $('input[name="IdOccurrence[]"]').each(function () {
            IdOccurrence.push($(this).val());
        });
        form_data.append('IdOccurrence[]', IdOccurrence);

        //ids
        form_data.append('meeting_id', $("#meeting_id").val());

        var topics_id = new Array();
        $('input[name="topics_id[]"]').each(function () {
            topics_id.push($(this).val());
        });
        form_data.append('topics_id[]', topics_id);

        var topics_covered_id = new Array();
        $('input[name="topics_covered_id[]"]').each(function () {
            topics_covered_id.push($(this).val());
        });
        form_data.append('topics_covered_id[]', topics_covered_id);

        form_data.append('datetime', $('#datetime').val());        
        form_data.append('local', $('#local').val());        

        if (valid === 0) {
            $('.overlay').removeClass('d-none');
            $.ajax({
                url: base_url + "/event/meeting/update",
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
              
                    window.location.replace(base_url + "/event/list/meeting");
                    } else {
                        DefaultAlert("error", obj.message);
              
                    }
                }
            }).catch()
            .always(()=>{
                $('.overlay').addClass('d-none');
            })
        }
    });
    /**
     * 
     * @param {string} type 
     * @param {string} msg 
     * exemplo: DefaultAlert("success","Cadastro efetuado com sucesso.");
     */
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }
});
