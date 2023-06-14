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
        $('#btn_delete').attr('data-id',id)
        $('#modal_delete').modal('show');
    })

    $('#btn_delete').on('click',()=>{
        let id = $('#btn_delete').attr('data-id')
        
        let data = {
            _method : 'DELETE', 
                    }
        let route ='/event/check_suite/'+id
        $.post(route,data,(response)=>{
            $('#modal_delete').modal('hide');
            window.location.replace(base_url + "/event/check_suite");
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