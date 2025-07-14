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
  if($('#show').val()=='show'){
    $('input').attr('disabled',true)
    $('select').attr('disabled',true)
    $('textarea').attr('disabled',true)
  }

  //carregar os dados 
  let items = JSON.parse($('#items').val())
  items.forEach((item) => {
    let ref = item.ref
    $('#approved-' + ref).val(item.approved)
    $('#appreciation-' + ref).val(item.appreciation)
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
    let form_data = {
      _method: 'PUT',
      owner: $('#owner').val(),
      unit: $('#unit').val(),
      inspected_by: $('#inspected_by').val(),
      inspection_date: $('#inspection_date').val(),
      observation: $('#obs').val(),
      approved: status,
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
      $(but_occurrence).attr('href', base_url + '/occurrence/list/edit/' + $('#idOccurence').val())
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


  // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
  function DefaultAlert(type, msg) {
    Toast.fire({
      icon: type,
      title: msg
    })
  }



});