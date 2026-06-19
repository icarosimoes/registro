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
      

    $('form[name="formPermission"]').submit(function(event){
        event.preventDefault();
        var url_id = location.href.substring(location.href.lastIndexOf('/') + 1)
        var checked = []
        $("input[name='permission[]']:checked").each(function ()
        {
            checked.push(parseInt($(this).val()));
        });
        var form_data = new FormData();   
        form_data.append('data', checked);
        $('.overlay').removeClass('d-none');
        $.ajax({
            url: base_url + "/admin/permission/create/" + url_id,
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


    $('#selec_all').on('click',()=>{
        $('input').attr('checked','checked')
        
    })

// exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg){
        Toast.fire({
            icon: type,
            title: msg
          })
    }

    //clear form
    
    function clearForm(){
        $("#name").val("");
        $("#email").val("");
        $("#password").val("");
    }
});
