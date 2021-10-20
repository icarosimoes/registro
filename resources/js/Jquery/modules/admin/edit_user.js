var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function(){
    
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

    $('form[name="formUserEdit"]').submit(function(event){
        event.preventDefault();
        var form_data = new FormData();
        form_data.append('name', $("#name").val());
        form_data.append('email', $("#email").val());
        form_data.append('password', $("#password").val());
        form_data.append('profile', $('#profile').val());
        form_data.append('userId', $("#userId").val());
    
        $('.overlay').removeClass('d-none');
        
        $.ajax({
            url: base_url + "/admin/user/update",
            type: "POST",
            data: form_data,
            dataType:'text',
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function(response){
                const obj = JSON.parse(response);
                if(obj.success === true){
                    DefaultAlert("success", obj.message);
                    $('.overlay').addClass('d-none');
                    location.reload();
                }else{
                     DefaultAlert("error", obj.message);
                    $('.overlay').addClass('d-none');
                }
            }
        });
    });

//alterar imagem
$('form[name="formUserEditImage"]').submit(function(event){
    event.preventDefault();
    var form_data = new FormData();
    form_data.append('image', $('#image').prop('files')[0]);                  
    form_data.append('userId', $("#userId").val());

    $('.overlay').removeClass('d-none');
    
    $.ajax({
        url: base_url + "/admin/user/update/image",
        type: "POST",
        data: form_data,
        dataType:'text',
        cache: false,
        contentType: false,
        processData: false,
        enctype: 'multipart/form-data',
        success: function(response){
            const obj = JSON.parse(response);
                if(obj.success === true){
                    DefaultAlert("success", obj.message);
                    $('.overlay').addClass('d-none');
                    location.reload();
                }else{
                     DefaultAlert("error", obj.message);
                    $('.overlay').addClass('d-none');
                }
        }
    });
});    
// exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg){
        Toast.fire({
            icon: type,
            title: msg
          })
    }

    //clear form
    
    function clearForm(){
        $("#title").val("");
        $("#description").val("");
        $("#url").val("");
        $("#source").val("");
        $("#image").val("");
    }
});
