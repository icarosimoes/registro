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
        let route ='/event/inspection_suite/'+id
        $.post(route,data,(response)=>{
            $('#modal_delete').modal('hide');
            window.location.replace(base_url + "/event/inspection_suite");
        }).catch(()=>{
            DefaultAlert('error','Não foi possível')
        }).always(()=>{
            
        })
    })
    
    $('#filter').on('click', () => {

        if ($('#card_filter').attr('data-visible') == 'true') {
            //escodido
            $('#card_filter').attr('data-visible', 'false')
            $('#card_filter').hide()
        } else {
            //visible
            $('#card_filter').attr('data-visible', 'true')
            $('#card_filter').show()
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
        
        
    
        }

    })

    //Exportar Excel
    $('#btnExportExcel').on('click',()=>{
        $('#exportExcel').modal('show')
    })

    $('#descriptionExportExcel').on('keyup',()=>{
       let description =  $('#descriptionExportExcel').val()
       let route = $('#btnExportExcelModal').attr('data-href')
       let href = route+'?description='+description
       $('#btnExportExcelModal').attr('href',href)
    })

    $('#btnExportExcelModal').on('click',()=>{
        $('#descriptionExportExcel').val('')
        $('#exportExcel').modal('hide')
    })

    //exportasr PDF
    $('#btnExportPdf').on('click',()=>{
        $('#exportPdf').modal('show')
    })

    $('#descriptionExportPdf').on('keyup',()=>{
       let description =  $('#descriptionExportPdf').val()
       let route = $('#btnExportPdfModal').attr('data-href')
       let href = route+'?description='+description
       $('#btnExportPdfModal').attr('href',href)
    })

    $('#btnExportPdfModal').on('click',()=>{
        $('#descriptionExportPdf').val('')
        $('#exportPdf').modal('hide')
    })


    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

    
});