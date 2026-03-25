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
 
let apartment_inspection = {}  
let apartment_inspections= {}
  load_apartment_inspections();
  function load_apartment_inspections() {
    let route = "/event/apartment_inspection_v2/load_apartment_inspections/" + $('#apartment_inspection_id').val();
    $.get(route, {}, function (data) {
      apartment_inspections = data;
      createApartamnetInspectionItems();
    });
  }

function createApartamnetInspectionItems() {
  console.log(apartment_inspections)  
  let html = "";
    let backgroundColor = "#f8f9fa";
    Object.entries(apartment_inspections.items).forEach(
      ([name_group, item]) => {
        //auterna a cor de backgroud dos grupos
         backgroundColor = backgroundColor == "#f8f9fa" ? "#ececec" : "#f8f9fa";


        item.forEach((item, index) => {
          html += `
            <tr style="background:${backgroundColor}" >
                <td  style="width: 120px ">${name_group}
                <button style="float: right;" type="button" class="btn btn-danger btn-sm remove_item_group" data-index="${index}" data-group="${name_group}" data-toggle="tooltip" data-placement="top" title="Remover item">
                <i class="fas fa-trash"></i> 
              </button>
                <button style="float: right;" type="button" class="btn btn-primary btn-sm add_item_group" data-group="${name_group}" data-toggle="tooltip" data-placement="top" title="Adicionar item">
                <i class="fas fa-plus"></i>
              </button>
              
                </td>
                <td style="width: 120px">
                   <input data-index="${index}" data-group="${name_group}"  data-column="service" style="width: 120px" class="form-control form-control-sm change" value="${
                     item.service
                   }"></input>
                </td>
                <td>
                  <input data-index="${index}" data-group="${name_group}"  data-column="item_verification" style="width: 500px" class="form-control form-control-sm change" value="${
                    item.item_verification
                  }"></input>
                </td>
                <td>
                             <select required class="form-control form-control-sm change" data-index="${index}" data-group="${name_group}" data-column="approved" name="item" id="approved-100">
                                <option value="yes" ${
                                  item.approved === "yes" ? "selected" : ""
                                } >APROVADO</option>
                                <option value="not" ${
                                  item.approved === "not" ? "selected" : ""
                                } >REPROVADO</option>
                              </select>
                            </td>
                            <td>
                              <input data-index="${index}" data-group="${name_group}" data-column="appreciation" id="appreciation-100" type="text" style="width: 200px"
                                class="form-control form-control-sm change" name="register" value="${
                                  item.appreciation
                                }">
                                                              
                            </td>
                            <td>
                              <button type="button" id="attach-100" data-toggle="tooltip" data-placement="top"
                                title="Anexos" class="btn btn-secondary btn-sm attach"><i
                                  class="fas fa-download"></i></button>
                                  <button data-index="${index}" data-group="${name_group}" data-ref="${name_group+index}" type="button" class="btn btn-secondary btn-sm filter "><i class="fas fa-filter"></i></button>
                                  <input type="hidden" id="occurrence-${name_group+index}" >
                                  
                                
                            </td>
                            <td>
                              <a id="link_register_${name_group+index}" href="http://aero.test/occurrence/list/edit/" style="width:50px" class="btn btn-sm btn-success d-none "></a>
                            </td>
    
                          </tr>
    `;
        });
      },
    );

    $("#apartment_items").html(html);
  }


//CARREGA OS TIPOS DE UNIDADE INICIAL MNETE
  $.get("/event/apartment_inspection_v2/load_types_unit", (response) => {
    $("#type_unit").html("");
    response.forEach((type_unit) => {
      $("#type_unit").append(
        `<option  value="${type_unit.id}">${type_unit.name}</option>`,
      );
    });
  });
  
  
  //modifica os campos 
  $(document).on('change', '.change', (e) => {
    const index = $(e.currentTarget).attr('data-index')
    const column = $(e.currentTarget).attr('data-column')
    const group = $(e.currentTarget).attr('data-group')
    const value = $(e.currentTarget).val()
    apartment_inspections.items[group][index][column] = value
    console.log(apartment_inspections)
  })
  
  

  //ADICIONAR NOVO ITEM EM UM GRUPO
  $(document).on("click", ".add_item_group", (e) => {
    let name_group = $(e.currentTarget).attr("data-group");
    
    apartment_inspections.items[name_group].push({
      group: name_group,
      service: "",
      item_verification: "",
      approved: "yes",
      appreciation: "",
      occurrence_id: "",
    });
    createApartamnetInspectionItems();
  })
  

  //REMOVE ITEM DO GRUPO
  $(document).on('click', '.remove_item_group', (e) => {
    const index = $(e.currentTarget).attr('data-index')
    const group = $(e.currentTarget).attr('data-group')
    apartment_inspections.items[group].splice(index, 1)
    createApartamnetInspectionItems()
  })
  
  
  
  
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
  } else if ($('#type_unit').val() == 'loft') {
    $('#loft').removeClass('d-none')
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
    }else if (type == 'loft') {
      $('#loft').removeClass('d-none')
    }
  })


  // //carregar os dados 
  // let items = JSON.parse($('#items').val())
  // items.forEach((item) => {
  //   let ref = item.ref
  //   $('#approved-' + ref).val(item.approved)
  //   $('#appreciation-' + ref).val(item.appreciation)
  //   $('#attach-' + ref).attr('data-id', item.id)

  //   if (item.occurrence_id) {
  //     $('#occurrence-' + ref).val(item.occurrence_id)
  //     $('#link_register_' + ref).attr('href', base_url + '/occurrence/list/edit/' + item.occurrence_id)
  //     $('#link_register_' + ref).removeClass('d-none')
  //     $('#link_register_' + ref).text(item.occurrence_id)
  //   }
  
  // })

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
  // $('#' + type_unit + ' input[name="register"]').each((index, element) => {
  //   let ref = $(element).attr('data-ref')
  //   let data = {
  //     appreciation: $(element).val(),
  //     ref: ref,
  //     approved: $('#approved-' + ref).val(),
  //     occurrence_id: $('#occurrence-' + ref).val()
  //   }
  //   items.push(data)
  // })

  let form_data = {
    _method: 'PUT',
    owner: $('#owner').val(),
    unit: $('#unit').val(),
    inspected_by: $('#inspected_by').val(),
    inspection_date: $('#inspection_date').val(),
    observation: $('#obs').val(),
    approved: status,
    type_unit: $('#type_unit').val(),
    items: JSON.stringify(apartment_inspections.items)
  };

  const apartment_inspection_id = $('#apartment_inspection_id').val()
  let route = '/event/apartment_inspection_v2/' + apartment_inspection_id
  $.post(route, form_data, (response) => {
    DefaultAlert("success", 'Salvo com sucesso !');
   // window.location.replace(base_url + "/event/apartment_inspection_v2");
  }).catch(() => {
    DefaultAlert("error", 'Não foi possivel salvar');
  }).always(() => {
    $('.overlay').addClass('d-none');
  })
});

$(document).on("click",".filter", (e) => {
  const ref = $(e.currentTarget).attr('data-ref')
  const index = $(e.currentTarget).attr('data-index')
  const group = $(e.currentTarget).attr('data-group')
  apartment_inspection = apartment_inspections.items[group][index]
  $('#register_ref').val(ref)
  $('#ModalSelectOcurrence').modal('show')
})

$('#buttonOccurrence').on('click', () => {
  const ref = $('#register_ref').val()
  $('#occurrence-' + ref).val($('#idOccurence').val())
  $('#link_register_' + ref).attr('href', base_url + '/occurrence/list/edit/' + $('#idOccurence').val())
  $('#link_register_' + ref).removeClass('d-none')
  $('#link_register_' + ref).text($('#idOccurence').val())
  apartment_inspection.occurrence_id = $('#idOccurence').val()
  $('#ModalSelectOcurrence').modal('hide')
  console.log(apartment_inspections)
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

// //carrega os items 
// const check_suite_items = JSON.parse($('#check_suite_items').val())


// // carrega avaliacao
// $('select[name="item"]').each((index, element) => {
//   $(element).val(check_suite_items[index].valuation)

// })

// //carrega campo registro
// $('input[name="register"]').each((index, element) => {
//   $(element).val(check_suite_items[index].register)
// })
// //carrega ao registro associados
// $('input[name="occurrences_id"]').each((index, element) => {
//   $(element).val(check_suite_items[index].occurrences_id)
// })

// $('.show_occurence_id').each((index, element) => {

//   $($(element).children()[0]).html(check_suite_items[index].occurrences_id)

//   if (check_suite_items[index].occurrences_id) {
//     $(element).removeClass('d-none')
//     $(element).attr('href', base_url + '/occurrence/list/edit/' + check_suite_items[index].occurrences_id)
//   }
// })

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