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
 
        $('.overlay').removeClass('d-none');
 

        let status =null 
        if($("#status1").is(":checked")==true){
            status = 'liberado';
        }else{
            status = 'bloqueado';
        } 
        
        const  occurrences_id= [] 
        $('input[name="occurrences_id"]').each((index,element)=>{
            occurrences_id.push($(element).val())
        })

        const valuation = [] 
        $('select[name="item"]').each((index,element)=>{
            valuation.push($(element).val())
            
        })  
        const register = [] 
        $('input[name="register"]').each((index,element)=>{
            register.push($(element).val())
            
        })  
        
        form_data = {
            date:$("#date").val(),
            local_id:$("#local").val(),
            user_id:$("#user").val(),
            status:status,
            maid:$("#maid").val(),
            obs:$("#obs").val(),
            valuation:valuation,
            register:register,
            occurrences_id:occurrences_id
        };

        let route  = '/event/check_suite'
        $.post(route,form_data,(response)=>{
            DefaultAlert("success", 'Salvo com sucesso !');   
            window.location.replace(base_url + "/event/check_suite");
        }).catch(()=>{
            DefaultAlert("error", 'Não foi possivel salvar');   
        }).always(()=>{
            $('.overlay').addClass('d-none');
        })
    });

    $('.filter').on('click',(e)=>{
        const item = $(e.currentTarget).attr('data-item')
        $('#buttonOccurrence').attr('data-item',item)
         $('#ModalSelectOcurrence').modal('show')
    })

    $('#buttonOccurrence').on('click',()=>{
        const item = $('#buttonOccurrence').attr('data-item')
        
        if($('#idOccurence').val()){
            $('#item-'+item).val($('#idOccurence').val())    
            const but_occurrence = $('#item-'+item).siblings('.show_occurence_id')[0]
            $(but_occurrence).removeClass('d-none')
            $(but_occurrence).children('i').html($('#idOccurence').val())
        }
        $('#ModalSelectOcurrence').modal('hide')

    })
    $('#idOccurence').select2({
        theme: 'bootstrap4',
        ajax: {
          url: base_url+'/helper/get_occurrences',
          dataType: 'json',

            data: function (params) {
            var query = {
              term: params.term,
              page: params.page || 1
            }

            // Query parameters will be ?search=[term]&page=[page]
            return query;
          },
          processResults: function (response) {
            //se a primeira paginacao
            if (response.current_page == 1){ data_select = response.data }
            else{ data_select = data_select.concat(response.data) }

            // Transforms the top-level key of the response object from 'items' to 'results'
             let more_pagination = true;
             //se não tem mais paginas
             if (response.next_page_url == null){ more_pagination = false }
             return {
                 results:response.data,
                 pagination: {
                    "more": more_pagination
                  }
                }
           }
        }
    });

    
    data_select = [] // gabiarra para pegar o obj escolhido no select2
    $('#local').select2({
        theme: 'bootstrap4',
        ajax: {
          url: base_url+'/helper/get_locals',
          dataType: 'json',

            data: function (params) {
            var query = {
              term: params.term,
              page: params.page || 1
            }

            // Query parameters will be ?search=[term]&page=[page]
            return query;
          },
          processResults: function (response) {
            //se a primeira paginacao
            if (response.current_page == 1){ data_select = response.data }
            else{ data_select = data_select.concat(response.data) }

            // Transforms the top-level key of the response object from 'items' to 'results'
             let more_pagination = true;
             //se não tem mais paginas
             if (response.next_page_url == null){ more_pagination = false }
             return {
                 results:response.data,
                 pagination: {
                    "more": more_pagination
                  }
                }
           }
        }
    });

    $('#user').select2({
        theme: 'bootstrap4',
        ajax: {
          url: base_url+'/helper/get_users',
          dataType: 'json',

            data: function (params) {
            var query = {
              term: params.term,
              page: params.page || 1
            }

            // Query parameters will be ?search=[term]&page=[page]
            return query;
          },
          processResults: function (response) {
            //se a primeira paginacao
            if (response.current_page == 1){ data_select = response.data }
            else{ data_select = data_select.concat(response.data) }

            // Transforms the top-level key of the response object from 'items' to 'results'
             let more_pagination = true;
             //se não tem mais paginas
             if (response.next_page_url == null){ more_pagination = false }
             return {
                 results:response.data,
                 pagination: {
                    "more": more_pagination
                  }
                }
           }
        }
    });


    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

   
   
});