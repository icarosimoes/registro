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
        let route ='/event/apartment_inspection/'+id
        
        // $.post(route,data,(response)=>{
        //     $('#modal_delete').modal('hide');
        //     window.location.replace(base_url + "/event/apartment_inspection");
        // }).catch(()=>{
        //     DefaultAlert('error','Não foi possível')
        // }).always(()=>{
            
        // })
    })

    $('.attach').on('click',(e)=>{
        let id = $(e.currentTarget).data('id')
        $('#apartment_inspection_id').val(id)
        $("#file").val(null)
        $("#name").val('')
        $('#bodyFile').empty()
        loadAnexos(id)
        $('#anexo').modal('show')
    })

    //enviar anexo
    $('#btn_send_attach').on('click',()=>{
        
        let id = $('#apartment_inspection_id').val()
        const formData = new FormData();
        formData.append('file', $("#file").prop('files')[0]);
        formData.append('name', $("#name").val());

        let route =base_url+'/event/apartment_inspection/attach/'+id
        $('.overlay').removeClass('d-none')
        $.ajax({
            url : route,
            type: "POST",
            data : formData,
            processData: false,
            contentType: false,
            success:function(data, textStatus, jqXHR){
                DefaultAlert('success','Anexo enviado com sucesso') 
                rederizaAnexos(data)
                $("#file").val(null)
                $("#name").val('')
                //carrega a lista de anexos
                
            },
            error: function(jqXHR, textStatus, errorThrown){
                DefaultAlert('error','Não foi possível enviar o anexo')
            },
            complete:function(){
                $('.overlay').addClass('d-none')
            }
        });
        
    })

    //carrega a lista de anexos
    function loadAnexos(id){
        $('.overlay').removeClass('d-none')
        let route = base_url+'/event/apartment_inspection/attach/'+id
        $.get(route,function(data){
            rederizaAnexos(data)    
        }).always(()=>{
            $('.overlay').addClass('d-none')
        })
    }

    //renderiza a lista de anexos
    function rederizaAnexos(data){
        $('#bodyFile').empty()
        data.forEach(item=>{
            $('#bodyFile').append(`
                <tr>
                    <td>${item.name}</td>
                    <td>${formatDate(item.created_at)}</td>
                    <td>
                    <a class="btn btn-secondary btn-sm" href="${base_url}/event/apartment_inspection/attach_download/${item.id}" target="_blank"><i class="fas fa-download"></i></a>
                    <button type="button" class="btn btn-danger btn-sm remove_attach" data-id="${item.id}" target="_blank"><i class="fas fa-trash"></i></button>
                    </td>
                    
                </tr>
            `)
        })
    }

    //remove anexo
    $(document).on('click','.remove_attach',(e)=>{
        let id = $(e.currentTarget).data('id')
        $('.overlay').removeClass('d-none')
        $.post(base_url+'/event/apartment_inspection/attach_delete/'+id,{},function(response){
            DefaultAlert('success','Anexo removido com sucesso')
            rederizaAnexos(response)
        }).catch(()=>{
            DefaultAlert('error','Não foi possível remover o anexo')
        }).always(()=>{
            $('.overlay').addClass('d-none')
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

    function formatDate(date){
        let date_split = date.split('T')[0]
        let date_split_2 = date_split.split('-')
        return date_split_2[2]+'/'+date_split_2[1]+'/'+date_split_2[0]
    }

    
});