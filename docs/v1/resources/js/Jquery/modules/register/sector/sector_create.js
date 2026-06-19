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

    $('form[name="formSector"]').submit(function(event) {

        event.preventDefault();

        var form_data = new FormData();

        form_data.append('sector', $("#sector").val());
        $('.overlay').removeClass('d-none');
 
        form_data = {
            sector:$("#sector").val()
        };

        let route  = '/register/sector'
        $.post(route,form_data,(response)=>{
            DefaultAlert("success", 'Salvo com sucesso !');   
            window.location.replace(base_url + "/register/sector");
        }).catch(()=>{
            DefaultAlert("error", 'Não foi possivel salvar');   
        }).always(()=>{
            $('.overlay').addClass('d-none');
        })

        // $.ajax({
        //     url: base_url + "register/sector",
        //     type: "POST",
        //     data: form_data,
        //     dataType: 'text',
        //     cache: false,
        //     contentType: false,
        //     processData: false,
        //     enctype: 'multipart/form-data',
        //     success: function(response) {
        //         const obj = JSON.parse(response);
        //         if (obj.success === true) {
        //             DefaultAlert("success", obj.message);
        //             $('.overlay').addClass('d-none');
        //             window.location.replace(base_url + "/occurrence/list/occurrence");
        //         } else {
        //             DefaultAlert("error", obj.message);
        //             $('.overlay').addClass('d-none');
        //         }
        //     }
        // });
    });

    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

   
   
});