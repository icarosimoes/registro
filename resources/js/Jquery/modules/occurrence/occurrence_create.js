var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {
 
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

    $('form[name="formOccurrence"]').submit(function(event) {

        event.preventDefault();

        var form_data = new FormData();

        form_data.append('title', $("#title").val());
        form_data.append('description', $("#description").val());
        form_data.append('participants', $("#participants").val());
        form_data.append('deadline', $("#deadline").val());
        form_data.append('receiver', $('#receiver').val());
        form_data.append('receiver', $('#receiver').val());
        form_data.append('comments', $('#comments').val());
        form_data.append('local_id', $('#local').val());
        form_data.append('sector_id', $('#sector').val());
        form_data.append('file', $('#file').prop('files')[0]);     
        $('.overlay').removeClass('d-none');

        $.ajax({
            url: base_url + "/occurrence/occurrence/store",
            type: "POST",
            data: form_data,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function(response) {
                const obj = JSON.parse(response);
                if (obj.success === true) {
                    DefaultAlert("success", obj.message);
                    $('.overlay').addClass('d-none');
                    window.location.replace(base_url + "/occurrence/list/occurrence");
                } else {
                    DefaultAlert("error", obj.message);
                    $('.overlay').addClass('d-none');
                }
            }
        }).catch()
        .always(()=>{
          $('.overlay').addClass('d-none');
        })
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

    data_select = [] // gabiarra para pegar o obj escolhido no select2
    $('#sector').select2({
        theme: 'bootstrap4',
        ajax: {
          url: base_url+'/helper/get_sectors',
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

    //clear form

    function clearForm() {
        $("#name").val("");
        $("#email").val("");
        $("#password").val("");
    }
});