var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {
 
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end' ,
        showConfirmButton: false,
        timer: 3000
    });

    $('form[name="form"]').submit(function(event) {

        event.preventDefault();

        var form_data = new FormData();

        form_data.append('name', $("#name").val());
        form_data.append('link', $("#link").val());
        form_data.append('file', $('#file').prop('files')[0]);                  
        form_data.append('_method', 'PUT');
        $('.overlay').removeClass('d-none');
        
        let id = $('#id').val()

        $.ajax({
            url: base_url + "/register/procedure/"+id,
            type: "POST",
            data: form_data,
            dataType:'text',
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function(response){
                DefaultAlert("success", 'Salvo com sucesso !');       
                window.location.replace(base_url + "/register/procedure");
            }
        }).catch(()=>{
            DefaultAlert("error", 'Não foi possivel salvar');   
        }).always(()=>{
            $('.overlay').addClass('d-none');
        })
        

    });

     //ANEXO

    $(document).on('click', '.md', function(){
        
        var id = $(this).data('id');
        $('#procedure_id_atach').val(id);
        
        refreshListAttach()
       
        $('#anexo').modal('show');
    });

    //atualizar lista anexo
    function refreshListAttach(){
        $('.overlay').removeClass('d-none');
        let id =  $('#procedure_id_atach').val();
        $('#bodyFile tr').remove();

        $.get(base_url + "/register/procedure/files/" + id, function(data) {
        
            $.each(data, function(index, value) {
                var data =  new Date(value.created_at);
                var html = "<tr>" +
                    "<td>" + value.name + "</td>" +
                    "<td>" + data.toLocaleDateString('pt-BR') + " - " + data.toLocaleTimeString('pt-BR') + "</td>" +
                    "<td>" + 
                    "<a target='_blanck' href='" + base_url + "/register/procedure/download/" + value.id + "' class='btn btn-sm btn-default mb'><i class='fas fa-download'></i></a>" +
                    "<a href='' data-toggle='modal' data-id='"+ value.id +"' data-target='#deleteFile' class='btn btn-sm ml-1 btn-danger btnDelete'><i class='fas fa-trash-alt'></i></a></td>" +
                    "</tr>";
                $('#bodyFile').append(html);
            });
        }).always(()=>{
            $('.overlay').addClass('d-none');
        })

    }

    
    $('form[name="formFileDownload"]').submit(function(event) {
        event.preventDefault();

        let id = $("#procedure_id_atach").val()
        var form_data = new FormData();
        form_data.append('file', $("#file").prop('files')[0]);
        form_data.append('name', $("#name").val());
        form_data.append('procedure_id', id);

        $('.overlay').removeClass('d-none');

        $.ajax({
            url: base_url + "/register/procedure/upload/"+ id,
            type: "POST",
            data: form_data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function(response) {
                DefaultAlert("success", response);
                refreshListAttach()
                $("#name").val('')
                $("#file").val(null)
           
            }
        }).catch((response)=>{
            DefaultAlert("error", response.responseJSON);
        })
        .always(()=>{
            $('.overlay').addClass('d-none');
        })
    });

    $(document).on('click', '.btnDelete', function(){
        $('#anexo').modal('hide');
        idFile = $(this).attr('data-id');
    });
    
    $(document).on('click', '#cancelDeleteTheFile', function(){
        $('#deleteFile').modal('hide')
            $('#anexo').modal('show');
    });



    $(document).on('click', '#deleteTheFile', function(){
        $.post(base_url + "/register/procedure/files/" + idFile,{_method:'DELETE'}, function(data){
            refreshListAttach()
            $('#deleteFile').modal('hide')
            $('#anexo').modal('show');

            
        });
    });


    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

   
   
});