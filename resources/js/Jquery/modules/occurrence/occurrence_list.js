var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get("status")

    if (status == null){
        $('#status').val(0)
    }else{
        $('#status').val(status)
    }

    $('#filter').on('click',()=>{
       
        if ($('#card_filter').attr('data-visible') == 'true'){
            //escodido
            $('#card_filter').attr('data-visible','false')
            $('#card_filter').hide()
        }else{
            //visible
            $('#card_filter').attr('data-visible','true')
            $('#card_filter').show()
        }
        
    })
    

    
});