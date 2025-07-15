const { at } = require("lodash");

var base_url = window.location.origin;

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$(function () {

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  var attachs = new Array()
  // modal de anexos
  $('.attach').on('click', (e) => {
    let ref = $(e.currentTarget).attr('data-ref')
    $('#apartment_inspection_item_id').val(ref)
    $("#file").val(null)
    $("#name").val('')
    rederizaAnexos(ref)
    $('#anexo').modal('show')
  })


  $('#btn_send_attach').on('click', () => {
    let ref = $('#apartment_inspection_item_id').val()
    attachs.push({
      name: $("#name").val(),
      file: $("#file").val(),
      ref: $('#apartment_inspection_item_id').val(),
      attach: $("#file").prop('files')[0],
    })
    rederizaAnexos(ref)
  })

  function rederizaAnexos(ref) {
    $('#bodyFile').empty()
    if (attachs.length > 0) {
      let attachs_items = attachs.filter(attach => attach.ref == ref)
      let html = ''
      attachs_items.forEach((attach, index) => {
        html += `<tr>
          <td>${attach.name}</td>
        <td>
            <button style="float: right;" type="button" class="btn btn-danger btn-sm delete-attach" data-index="${index}" data-toggle="tooltip" data-placement="top" title="Anexos">
            <i class="fas fa-trash"></i>
          </button>
        </td>
        </tr>`
      })

      $('#bodyFile').html(html)
      $("#file").val(null)
      $("#name").val('')
    }
  }

  $(document).on('click','.delete-attach', (e) => {
    let ref = $('#apartment_inspection_item_id').val()
    let index = $(e.currentTarget).attr('data-index')
    console.log(index)
    attachs.splice(index, 1)
    console.log(attachs)
    rederizaAnexos(ref)
  })








  $('form[name="form"]').submit(function (event) {
    event.preventDefault();
    $('.overlay').removeClass('d-none');

    let status = null
    if ($("#status1").is(":checked") == true) {
      status = 'yes';
    } else {
      status = 'not';
    }


    let items = []
    $('input[name="register"]').each((index, element) => {
      let ref = $(element).attr('data-ref')
      let data = {

        appreciation: $(element).val(),
        ref: ref,
        approved: $('#approved-' + ref).val()
      }
      items.push(data)

    })

    // form_data = {
    //   owner: $('#owner').val(),
    //   unit: $('#unit').val(),
    //   inspected_by: $('#inspected_by').val(),
    //   inspection_date: $('#inspection_date').val(),
    //   observation: $('#obs').val(),
    //   approved: status,
    //   items: JSON.stringify(items)
    // };

    formData = new FormData()
    formData.append('owner', $('#owner').val())
    formData.append('unit', $('#unit').val())
    formData.append('inspected_by', $('#inspected_by').val())
    formData.append('inspection_date', $('#inspection_date').val())
    formData.append('observation', $('#obs').val())
    formData.append('approved', status)
    formData.append('items', JSON.stringify(items))

    attachs_names = []
    attachs.forEach((attach, index) => {
      formData.append('attachs_' + index + '_'+attach.ref, attach.attach)

      nameAttach = 'attachs_' + index + '_'+attach.ref
      attachs_names.push({ [nameAttach]: attach.name })
    })
    formData.append('names_attachs', JSON.stringify(attachs_names))


    let route = '/event/apartment_inspection'
    $.ajax({
      url: route,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data, textStatus, jqXHR) {
        DefaultAlert('success', 'Anexo enviado com sucesso')
        // rederizaAnexos(data)
        $("#file").val(null)
        $("#name").val('')
        //carrega a lista de anexos
      },
      error: function (jqXHR, textStatus, errorThrown) {
        DefaultAlert('error', 'Não foi possível enviar o anexo')
      },
      complete: function () {
        $('.overlay').addClass('d-none')
      }
    });
    // $.post(route, form_data, (response) => {
    //   DefaultAlert("success", 'Salvo com sucesso !');
    //   window.location.replace(base_url + "/event/apartment_inspection");
    // }).catch(() => {
    //   DefaultAlert("error", 'Não foi possivel salvar');
    // }).always(() => {
    //   $('.overlay').addClass('d-none');
    // })
  });

  $('.filter').on('click', (e) => {
    const item = $(e.currentTarget).attr('data-item')
    $('#buttonOccurrence').attr('data-item', item)
    $('#ModalSelectOcurrence').modal('show')
  })

  $('#buttonOccurrence').on('click', () => {
    const item = $('#buttonOccurrence').attr('data-item')

    if ($('#idOccurence').val()) {
      $('#item-' + item).val($('#idOccurence').val())
      const but_occurrence = $('#item-' + item).siblings('.show_occurence_id')[0]
      $(but_occurrence).removeClass('d-none')
      $(but_occurrence).children('i').html($('#idOccurence').val())
    }
    $('#ModalSelectOcurrence').modal('hide')

  })
  $('#idOccurence').select2({
    theme: 'bootstrap4',
    ajax: {
      url: base_url + '/helper/get_occurrences',
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
        if (response.current_page == 1) { data_select = response.data }
        else { data_select = data_select.concat(response.data) }

        // Transforms the top-level key of the response object from 'items' to 'results'
        let more_pagination = true;
        //se não tem mais paginas
        if (response.next_page_url == null) { more_pagination = false }
        return {
          results: response.data,
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
      url: base_url + '/helper/get_locals',
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
        if (response.current_page == 1) { data_select = response.data }
        else { data_select = data_select.concat(response.data) }

        // Transforms the top-level key of the response object from 'items' to 'results'
        let more_pagination = true;
        //se não tem mais paginas
        if (response.next_page_url == null) { more_pagination = false }
        return {
          results: response.data,
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
      url: base_url + '/helper/get_users',
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
        if (response.current_page == 1) { data_select = response.data }
        else { data_select = data_select.concat(response.data) }

        // Transforms the top-level key of the response object from 'items' to 'results'
        let more_pagination = true;
        //se não tem mais paginas
        if (response.next_page_url == null) { more_pagination = false }
        return {
          results: response.data,
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