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

  //verifica se é tela de edicao ou visualizacao
  if ($('#show').val() == 'show') {
    $('input').attr('disabled', true)
    $('select').attr('disabled', true)
    $('textarea').attr('disabled', true)
    $('#btn_send_attach').prop('disabled', true);
  }


  // define o tipo de unidade
  $('#type_unit').val($('#type_unit').data('value'))


  $('.hide_all').addClass('d-none')
  if ($('#type_unit').val() == 'dois_quartos') {
    $('#dois_quartos').removeClass('d-none')
  } else if ($('#type_unit').val() == 'quanto_sala_1') {
    $('#quanto_sala_1').removeClass('d-none')
  } else if ($('#type_unit').val() == 'quanto_sala_2') {
    $('#quanto_sala_2').removeClass('d-none')
  } else if ($('#type_unit').val() == 'studio') {
    $('#studio').removeClass('d-none')
  }

  $('#type_unit').on('change', (e) => {
    const type = $(e.currentTarget).val()

    $('.hide_all').addClass('d-none')
    if (type == 'dois_quartos') {

      $('#dois_quartos').removeClass('d-none')
    } else if (type == 'quanto_sala_1') {
      $('#quanto_sala_1').removeClass('d-none')
    } else if (type == 'quanto_sala_2') {
      $('#quanto_sala_2').removeClass('d-none')
    } else if (type == 'studio') {
      $('#studio').removeClass('d-none')
    }
  })


  //carregar os dados 
  let items = JSON.parse($('#items').val())
  items.forEach((item) => {
    let ref = item.ref
    $('#approved-' + ref).val(item.approved)
    $('#appreciation-' + ref).val(item.appreciation)
    $('#attach-' + ref).attr('data-id', item.id)

    if (item.occurrence_id) {
      $('#occurrence-' + ref).val(item.occurrence_id)
      $('#link_register_' + ref).attr('href', base_url + '/occurrence/list/edit/' + item.occurrence_id)
      $('#link_register_' + ref).removeClass('d-none')
      $('#link_register_' + ref).text(item.occurrence_id)
    }
  
  })

//modal de anexos
$('.attach').on('click', (e) => {
  let id = $(e.currentTarget).data('id')
  $('#apartment_inspection_item_id').val(id)
  $("#file").val(null)
  $("#name").val('')
  $('#bodyFile').empty()
  loadAnexos(id)
  $('#anexo').modal('show')
})

//enviar anexo
$('#btn_send_attach').on('click', () => {

  let id = $('#apartment_inspection_item_id').val()
  const formData = new FormData();
  formData.append('file', $("#file").prop('files')[0]);
  formData.append('name', $("#name").val());

  let route = base_url + '/event/apartment_inspection_item/attach/' + id
  $('.loading_attach').removeClass('d-none')
  $.ajax({
    url: route,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (data, textStatus, jqXHR) {
      DefaultAlert('success', 'Anexo enviado com sucesso')
      rederizaAnexos(data)
      $("#file").val(null)
      $("#name").val('')
      //carrega a lista de anexos

    },
    error: function (jqXHR, textStatus, errorThrown) {
      DefaultAlert('error', 'Não foi possível enviar o anexo')
    },
    complete: function () {
      $('.loading_attach').addClass('d-none')
    }
  });

})

//carrega a lista de anexos
function loadAnexos(id) {
  $('.loading_attach').removeClass('d-none')
  let route = base_url + '/event/apartment_inspection_item/attach/' + id
  $.get(route, function (data) {
    rederizaAnexos(data)
  }).always(() => {
    $('.loading_attach').addClass('d-none')
  })
}

//renderiza a lista de anexos
function rederizaAnexos(data) {
  $('#bodyFile').empty()
  data.forEach(item => {
    $('#bodyFile').append(`
            <tr>
                <td>${item.name}</td>
                <td>${formatDate(item.created_at)}</td>
                <td>
                <a class="btn btn-secondary btn-sm" href="${base_url}/event/apartment_inspection_item/attach_download/${item.id}" target="_blank"><i class="fas fa-download"></i></a>
                <button type="button" class="btn btn-danger btn-sm remove_attach" data-id="${item.id}" target="_blank"><i class="fas fa-trash"></i></button>
                </td>
                
            </tr>
        `)
  })
}

//remove anexo
$(document).on('click', '.remove_attach', (e) => {
  let id = $(e.currentTarget).data('id')
  $('.loading_attach').removeClass('d-none')
  $.post(base_url + '/event/apartment_inspection_item/attach_delete/' + id, {}, function (response) {
    DefaultAlert('success', 'Anexo removido com sucesso')
    rederizaAnexos(response)
  }).catch(() => {
    DefaultAlert('error', 'Não foi possível remover o anexo')
  }).always(() => {
    $('.loading_attach').addClass('d-none')
  })
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
  let type_unit = $('#type_unit').val()
  let items = []
  $('#' + type_unit + ' input[name="register"]').each((index, element) => {
    let ref = $(element).attr('data-ref')
    let data = {
      appreciation: $(element).val(),
      ref: ref,
      approved: $('#approved-' + ref).val(),
      occurrence_id: $('#occurrence-' + ref).val()
    }
    items.push(data)
  })

  let form_data = {
    _method: 'PUT',
    owner: $('#owner').val(),
    unit: $('#unit').val(),
    inspected_by: $('#inspected_by').val(),
    inspection_date: $('#inspection_date').val(),
    observation: $('#obs').val(),
    approved: status,
    type_unit: $('#type_unit').val(),
    items: JSON.stringify(items)
  };

  const apartment_inspection_id = $('#apartment_inspection_id').val()
  let route = '/event/apartment_inspection/' + apartment_inspection_id
  $.post(route, form_data, (response) => {
    DefaultAlert("success", 'Salvo com sucesso !');
    window.location.replace(base_url + "/event/apartment_inspection");
  }).catch(() => {
    DefaultAlert("error", 'Não foi possivel salvar');
  }).always(() => {
    $('.overlay').addClass('d-none');
  })
});

$('.filter').on('click', (e) => {
  const ref = $(e.currentTarget).attr('data-ref')
  $('#register_ref').val(ref)
  $('#ModalSelectOcurrence').modal('show')
})

$('#buttonOccurrence').on('click', () => {
  const ref = $('#register_ref').val()
  $('#occurrence-' + ref).val($('#idOccurence').val())
  $('#link_register_' + ref).attr('href', base_url + '/occurrence/list/edit/' + $('#idOccurence').val())
  $('#link_register_' + ref).removeClass('d-none')
  $('#link_register_' + ref).text($('#idOccurence').val())

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

//carrega os items 
const check_suite_items = JSON.parse($('#check_suite_items').val())


// carrega avaliacao
$('select[name="item"]').each((index, element) => {
  $(element).val(check_suite_items[index].valuation)

})

//carrega campo registro
$('input[name="register"]').each((index, element) => {
  $(element).val(check_suite_items[index].register)
})
//carrega ao registro associados
$('input[name="occurrences_id"]').each((index, element) => {
  $(element).val(check_suite_items[index].occurrences_id)
})

$('.show_occurence_id').each((index, element) => {

  $($(element).children()[0]).html(check_suite_items[index].occurrences_id)

  if (check_suite_items[index].occurrences_id) {
    $(element).removeClass('d-none')
    $(element).attr('href', base_url + '/occurrence/list/edit/' + check_suite_items[index].occurrences_id)
  }
})

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




function formatDate(date) {
  let date_split = date.split('T')[0]
  let date_split_2 = date_split.split('-')
  return date_split_2[2] + '/' + date_split_2[1] + '/' + date_split_2[0]
}

// exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
function DefaultAlert(type, msg) {
  Toast.fire({
    icon: type,
    title: msg
  })
}



});