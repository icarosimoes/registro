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

        let obs_subjects_ids = []
        let obs_subjects_values = []
        $(".obs_subject").each((index,item)=>{
            const id = $(item).attr('data-id')
            const value = $(item).val()
            obs_subjects_ids.push(id) 
            obs_subjects_values.push(value) 
        })
        form_data.append('obs_subjects_ids', obs_subjects_ids);        
        form_data.append('obs_subjects_values', obs_subjects_values);        

        //novas pautas
        let obs_new_subjects = []
        $(".obs_new_subject").each((index,item)=>{
            const value = $(item).val()
            obs_new_subjects.push(value) 
            
        })
        form_data.append('obs_new_subjects', obs_new_subjects);        
        
        let new_subjects = []
        $(".new_subject").each((index,item)=>{
            const value = $(item).val()
            new_subjects.push(value) 
            
        })
        form_data.append('new_subjects', new_subjects);        

        
        
        form_data.append('datetime', $('#datetime').val());        
        form_data.append('local', $('#local').val());        
        form_data.append('approval', $('#approval').val());        
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

    $('#approval').val($('#approval').attr('data-value'))


    $('#btn_start_meeting').on('click',()=>{
        $('.overlay').removeClass('d-none');
        const id = $("#meeting_id").val()
        const route = base_url+'/event/meeting/start_meeting/'+id
        const data = {}
        $.post(route,data,(response)=>{
            DefaultAlert("success", 'Reunião Iniciada');  
            $('#btn_start_meeting').text('Reunião iniciada: '+response)
            $('#btn_start_meeting').attr('disabled','disabled')
        }).catch()
        .always(()=>{
            $('.overlay').addClass('d-none');
        })
    })

    let count_new_subject = 0
    $('#add_new_subject').on('click',()=>{
        count_new_subject++
      let html = '<div id="a-'+count_new_subject+'">'+
           '<div class="row mt-3">'+
           '<div class="col">'+
           '<label for="">Nova Pauta</label>'+
           '<div class="input-group">'+
           '<input class="form-control new_subject"  type="text" '+
           'value="">'+
           '<div class="input-group-append">'+
           '<button data-id="a-'+count_new_subject+'" class="btn btn-secondary btn-sm trash_subject"'+
               'type="button"><i class="fas fa-trash"></i></button>'+
       '</div>'+
       '</div>'+
       '</div>'+
   '</div>'+
   '<div class="row mt-2">'+
       '<div class="col">'+
           '<label for="">Observações</label>'+
           '<textarea data-id="" class="form-control obs_new_subject" name="" cols="30"'+
               'rows="5"></textarea>'+
       '</div>'+
   '</div>'+
   '</div>'
   
   $('#list_meeting').append(html)
    })


    //novas pautas 
    $(document).on('click','.trash_subject',(e)=>{
       const id =  $(e.currentTarget).attr('data-id')
       $('#'+id).remove()
    })

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
