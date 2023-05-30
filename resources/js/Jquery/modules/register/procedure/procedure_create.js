var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {

    
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $('form[name="form"]').submit(function(event) {

        event.preventDefault();

        var form_data = new FormData();

        form_data.append('name', $("#name").val());
        form_data.append('link', $("#link").val());
        form_data.append('file', $('#file').prop('files')[0]);                  
        $('.overlay').removeClass('d-none');

        $.ajax({
            url: base_url + "/register/procedure",
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

    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

   
   
});