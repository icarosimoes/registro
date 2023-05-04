var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {

    const urlParams = new URLSearchParams(window.location.search);

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });


    $('.remove').on('click',(e)=>{
        let id = $(e.currentTarget).data('id')
        $('#delete_sector').attr('data-id',id)
        $('#modal_delete').modal('show');
    })

    $('#delete_sector').on('click',()=>{
        let id = $('#delete_sector').attr('data-id')
        
        let data = {
            _method : 'DELETE', 
                    }
        let route ='/register/sector/'+id
        $.post(route,data,(response)=>{
            $('#modal_delete').modal('hide');
            window.location.replace(base_url + "/register/sector");
        }).catch(()=>{
            DefaultAlert('error','Não foi possível')
        }).always(()=>{
            
        })
    })
    
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

    
});