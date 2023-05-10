var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function(){
    
    

    // //Initialize Select2 Elements
    // $('.select2').select2({
    //     theme: 'bootstrap4',
    // });
    

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    }); 
    
    



    $(document).on('click','.remove',(e)=>{
        let id = $(e.currentTarget).attr('data-id')
        let route = base_url+'/admin/user/delete/'+id
        $('#btn_delete').attr('href',route)
        $('#modal_delete').modal('show')
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
