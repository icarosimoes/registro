var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function(){
    
      //Initialize Select2 Elements
    //   $('.select2').select2({
    //     theme: 'bootstrap4',
    //    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      }); 

    $(document).on('change','.checkbox_active',(e)=>{
        value = $(e.currentTarget).is(':checked')
        if (value){
            value = 'yes'
        }else{
            value = 'not'
        }
               
        id = $(e.currentTarget).attr('data-id')
        route = base_url+'/admin/config/forms/'+ id   
        data = {
            active:value
        }    
        $.post(route,data).done((response)=>{
            location.reload(); 
            
            DefaultAlert('success','Salvo com sucesso.')
        })      
    })

    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg){
        Toast.fire({
            icon: type,
            title: msg
          })
    }


    
});
