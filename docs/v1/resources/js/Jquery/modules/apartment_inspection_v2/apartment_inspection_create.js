const { at } = require("lodash");

var base_url = window.location.origin;

$.ajaxSetup({
  headers: {
    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
  },
});

$(function () {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
  });
  let attachs = [];
  let apartment_inspection = {};
  let apartment_inspections = [];

  load_apartment_inspections();
  function load_apartment_inspections(type_unit = null) {
    let route = "/event/apartment_inspection_v2/load_apartment_inspections";
    $.post(route, { type_unit: type_unit }, function (data) {
      apartment_inspections = data;
      //limpa os dados dos items
       Object.entries(apartment_inspections.items).forEach(([name_group, items]) => {
  items.forEach(item => {
    item.service = "";
    item.item_verification = "";
    item.approved = "yes";
    item.appreciation = "";
    item.occurrence_id = "";
  });
});


      createApartamnetInspectionItems();
    });
  }

  function createApartamnetInspectionItems() {
    let html = "";
    let backgroundColor = "#f8f9fa";
    Object.entries(apartment_inspections.items).forEach(
      ([name_group, item]) => {
        //auterna a cor de backgroud dos grupos
         backgroundColor = backgroundColor == "#f8f9fa" ? "#ececec" : "#f8f9fa";

        let nameGroupShow = true
        
        const lastIndex = item.length - 1;
        item.forEach((item, index) => {
          html += `
            <tr style="background:${backgroundColor}" >
                <td  style="width: 120px ">${nameGroupShow ? name_group : ''}
                <button style="float: right;" type="button" class=" btn btn-danger btn-sm remove_item_group" data-index="${index}" data-group="${name_group}" data-toggle="tooltip" data-placement="top" title="Remover item">
                <i class="fas fa-trash"></i> 
              </button>
                <button style="float: right;" type="button" class="btn btn-primary btn-sm add_item_group ${index === lastIndex ? '' : 'd-none'}" data-group="${name_group}" data-toggle="tooltip" data-placement="top" title="Adicionar item">
                <i class="fas fa-plus"></i>
              </button>
                </td>
                <td style="width: 120px">
                   <input style="width: 120px" class="form-control form-control-sm" value="${
                     item.service
                   }"></input>
                </td>
                <td>
                  <input style="width: 500px" class="form-control form-control-sm" value="${
                    item.item_verification
                  }"></input>
                </td>
                <td>
                             <select required class="form-control form-control-sm" name="item" id="approved-100">
                                <option value="yes" ${
                                  item.approved === "yes" ? "selected" : ""
                                } >APROVADO</option>
                                <option value="not" ${
                                  item.approved === "not" ? "selected" : ""
                                } >REPROVADO</option>
                              </select>
                            </td>
                            <td>
                              <input data-ref='100' id="appreciation-100" type="text" style="width: 200px"
                                class="form-control form-control-sm" name="register" value="${
                                  item.appreciation
                                }">
                                                              
                            </td>
                            <td>
                              <button data-group="${name_group}" data-index="${index}" type="button" id="attach-100" data-toggle="tooltip" data-placement="top"
                                title="Anexos" class="btn btn-secondary btn-sm attach"><i
                                  class="fas fa-download"></i></button>
                                  <button data-ref="100" type="button" class="btn btn-secondary btn-sm filter "><i class="fas fa-filter"></i></button>
                                  <input type="hidden" id="occurrence-100" >
                                
                            </td>
                            <td>
                              <a id="link_register_100" href="http://aero.test/occurrence/list/edit/" style="width:50px" class="btn btn-sm btn-success d-none "></a>
                            </td>
    
                          </tr>
    `;
    nameGroupShow = false
        });
      },
    );

    $("#apartment_items").html(html);
  }

  //ao mudar tipo de unidade, limpa os itens e carrega os itens do tipo selecionado
  $("#type_unit").on("change", (e) => {
    const type = $(e.currentTarget).val();
    load_apartment_inspections(type);
  });


  //ABRIR MODAL DE TIPOS DE UNIDADE
  $("#addTypeUnit").on("click", () => {
    $("#modal_type_unit").modal("show");
  });

  //SALVA O TIPO DE UNIDADE
  $("#btn_save_type_unit").on("click", () => {
    let new_type_unit = $("#new_type_unit").val();
    if (new_type_unit != "") {
      let route = "/event/apartment_inspection_v2/save_type_unit";
      $.post(route, { new_type_unit }, (response) => {
        let new_type = response.new_type;
        $("#type_unit").html("");
        response.types.forEach((type_unit) => {
          $("#type_unit").append(
            `<option ${type_unit.id === new_type.id ? "selected" : ""} value="${
              type_unit.id
            }">${type_unit.name}</option>`,
          );
        });

        DefaultAlert("success", "Tipo de unidade salvo com sucesso !");
        $("#new_type_unit").val("");
        $("#modal_type_unit").modal("hide");
      })
        .catch(() => {
          DefaultAlert("error", "Não foi possível salvar o tipo de unidade !");
        })
        .always(() => {
          $(".overlay").addClass("d-none");
        });
    }
  });

  //CARREGA OS TIPOS DE UNIDADE INICIAL MNETE
  $.get("/event/apartment_inspection_v2/load_types_unit", (response) => {
    $("#type_unit").html("");
    response.forEach((type_unit) => {
      $("#type_unit").append(
        `<option  value="${type_unit.id}">${type_unit.name}</option>`,
      );
    });
  });

  //ABRIR MODAL DE ADD GRUPO
  $("#add_group").on("click", () => {
    if($('#type_unit').val() == null){
      DefaultAlert("error", "Selecione um tipo de unidade para adicionar um grupo !");
      return;
    }
    
    $("#modal_add_group").modal("show");
    $("#name_new_group").val('')
  });

  //ADICIONAR NOVO GRUPO DE ITENS
  $("#btn_add_new_group").on("click", () => {
    let name_group = $("#name_new_group").val();
    //verificar se o grupo já existe
    if (apartment_inspections.items[name_group.toUpperCase()]) {
      DefaultAlert("error", "Grupo já existe !");
      return;
    }

    let group = {};
    group[name_group.toUpperCase()] = [
      {
        group: name_group.toUpperCase(),
        service: "",
        item_verification: "",
        approved: "yes",
        appreciation: "",
        occurrence_id: "",
      },
    ];
    
    apartment_inspections.items = { ...apartment_inspections.items, ...group };
    createApartamnetInspectionItems();
    $("#modal_add_group").modal("hide");
  });


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
  $(document).on("click", ".remove_item_group", (e) => {
    const index = $(e.currentTarget).attr("data-index");
    const group = $(e.currentTarget).attr("data-group");
    apartment_inspections.items[group].splice(index, 1);
    if (apartment_inspections.items[group].length === 0) {
      delete apartment_inspections.items[group];
    }
    createApartamnetInspectionItems();
  });


//modal de anexos
  $(document).on("click", ".attach", (e) => {
    let index = $(e.currentTarget).attr("data-index");
    let group = $(e.currentTarget).attr("data-group");
    apartment_inspection = apartment_inspections.items[group][index];

    $("#file").val(null);
    $("#name").val("");
    $("#bodyFile").empty();
    rederizaAnexos(apartment_inspection.atachments);
    $("#anexo").modal("show");
  });

  // add anexos
  $("#btn_send_attach").on("click", () => {
    attachs.push({
      index:
        apartment_inspections.items[apartment_inspection.group].indexOf(
          apartment_inspection,
        ),
      group: apartment_inspection.group,
      name: $("#name").val(),
      file: $("#file").prop("files")[0],
    });
    rederizaAnexos(apartment_inspection.atachments);
  });

//renderiza a lista de anexos
  function rederizaAnexos(data) {
    $("#bodyFile").empty();
    data.forEach((item) => {
      $("#bodyFile").append(`
            <tr>
                <td>${item.name}</td>
                <td>${formatDate(item.created_at)}</td>
                <td>
                <a class="btn btn-secondary btn-sm" href="${base_url}/event/apartment_inspection_item_v2/attach_download/${
        item.id
      }" target="_blank"><i class="fas fa-download"></i></a>
                <button type="button" class="btn btn-danger btn-sm remove_attach" data-id="${
                  item.id
                }" target="_blank"><i class="fas fa-trash"></i></button>
                </td>
                
            </tr>
        `);
    });
    //verifica se tem anexos novos para mostrar
    attachs.forEach((item) => {
      if (
        item.group == apartment_inspection.group &&
        item.index ==
          apartment_inspections.items[item.group].indexOf(apartment_inspection)
      ) {
        $("#bodyFile").append(`
            <tr>
                <td>${item.name} <span class="badge badge-info">Novo</span></td>
                <td></td>
                <td>
                <a class="btn btn-secondary btn-sm" href="${URL.createObjectURL(
                  item.file,
                )}" target="_blank"><i class="fas fa-download"></i></a>
                <button type="button" data-index="${item.index}" data-group="${
          item.group
        }" class="btn btn-danger btn-sm remove_attach_new"  target="_blank"><i class="fas fa-trash"></i></button>
                </td>
             </tr>
        `);
      }
    });
  }

  //remove anexo
  $(document).on("click", ".remove_attach", (e) => {
    let id = $(e.currentTarget).data("id");
    $(".loading_attach").removeClass("d-none");
    $.post(
      base_url + "/event/apartment_inspection_item/attach_delete/" + id,
      {},
      function (response) {
        DefaultAlert("success", "Anexo removido com sucesso");
        rederizaAnexos(response);
      },
    )
      .catch(() => {
        DefaultAlert("error", "Não foi possível remover o anexo");
      })
      .always(() => {
        $(".loading_attach").addClass("d-none");
      });
  });

  //remove anexo novo
  $(document).on("click", ".remove_attach_new", (e) => {
    const index = $(e.currentTarget).attr("data-index");
    const group = $(e.currentTarget).attr("data-group");
    attachs = attachs.filter(
      (item) => !(item.group == group && item.index == index),
    );
    rederizaAnexos(apartment_inspection.atachments);
  });

  
  
  function rederizaAnexos(ref) {
    $("#bodyFile").empty();
    if (attachs.length > 0) {
      let attachs_items = attachs.filter((attach) => attach.ref == ref);
      let html = "";
      attachs_items.forEach((attach, index) => {
        html += `<tr>
          <td>${attach.name}</td>
        <td>
            <button style="float: right;" type="button" class="btn btn-danger btn-sm delete-attach" data-index="${index}" data-toggle="tooltip" data-placement="top" title="Anexos">
            <i class="fas fa-trash"></i>
          </button>
        </td>
        </tr>`;
      });

      $("#bodyFile").html(html);
      $("#file").val(null);
      $("#name").val("");
    }
  }

  $(document).on("click", ".delete-attach", (e) => {
    let ref = $("#apartment_inspection_item_id").val();
    let index = $(e.currentTarget).attr("data-index");
    console.log(index);
    attachs.splice(index, 1);
    console.log(attachs);
    rederizaAnexos(ref);
  });

  $('form[name="form"]').submit(function (event) {
    event.preventDefault();
    $(".overlay").removeClass("d-none");

    let status = null;
    if ($("#status1").is(":checked") == true) {
      status = "yes";
    } else {
      status = "not";
    }

    let type_unit = $("#type_unit").val();
    // let items = [];
    // $("#" + type_unit + ' input[name="register"]').each((index, element) => {
    //   let ref = $(element).attr("data-ref");
    //   let data = {
    //     appreciation: $(element).val(),
    //     ref: ref,
    //     approved: $("#approved-" + ref).val(),
    //     occurrence_id: $("#occurrence-" + ref).val(),
    //   };
    //   items.push(data);
    // });

    // form_data = {
    //   owner: $('#owner').val(),
    //   unit: $('#unit').val(),
    //   inspected_by: $('#inspected_by').val(),
    //   inspection_date: $('#inspection_date').val(),
    //   observation: $('#obs').val(),
    //   approved: status,
    //   items: JSON.stringify(items)
    // };

    formData = new FormData();
    formData.append("owner", $("#owner").val());
    formData.append("unit", $("#unit").val());
    formData.append("inspected_by", $("#inspected_by").val());
    formData.append("inspection_date", $("#inspection_date").val());
    formData.append("observation", $("#obs").val());
    formData.append("approved", status);
    formData.append("type_unit", type_unit);
    formData.append("items", JSON.stringify(apartment_inspections.items));
    
    attachs.forEach((item, index) => {
      formData.append(item.group + "-" + item.index, item.file);
    });


    let route = "/event/apartment_inspection_v2";
    $.ajax({
      url: route,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data, textStatus, jqXHR) {
        DefaultAlert("success", "Anexo enviado com sucesso");
        // rederizaAnexos(data)
        $("#file").val(null);
        $("#name").val("");
        //carrega a lista de anexos
       window.location.replace(base_url + "/event/apartment_inspection_v2");
      },
      error: function (jqXHR, textStatus, errorThrown) {
        DefaultAlert("error", "Não foi possível enviar o anexo");
      },
      complete: function () {
        $(".overlay").addClass("d-none");
      },
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

  $(document).on("click",".filter", (e) => {
    const ref = $(e.currentTarget).attr("data-ref");
    $("#register_ref").val(ref);
    $("#ModalSelectOcurrence").modal("show");
  });

  $("#buttonOccurrence").on("click", () => {
    const ref = $("#register_ref").val();
    $("#occurrence-" + ref).val($("#idOccurence").val());
    $("#link_register_" + ref).attr(
      "href",
      base_url + "/occurrence/list/edit/" + $("#idOccurence").val(),
    );
    $("#link_register_" + ref).removeClass("d-none");
    $("#link_register_" + ref).text($("#idOccurence").val());

    $("#ModalSelectOcurrence").modal("hide");
  });
  $("#idOccurence").select2({
    theme: "bootstrap4",
    ajax: {
      url: base_url + "/helper/get_occurrences",
      dataType: "json",

      data: function (params) {
        var query = {
          term: params.term,
          page: params.page || 1,
        };

        // Query parameters will be ?search=[term]&page=[page]
        return query;
      },
      processResults: function (response) {
        //se a primeira paginacao
        if (response.current_page == 1) {
          data_select = response.data;
        } else {
          data_select = data_select.concat(response.data);
        }

        // Transforms the top-level key of the response object from 'items' to 'results'
        let more_pagination = true;
        //se não tem mais paginas
        if (response.next_page_url == null) {
          more_pagination = false;
        }
        return {
          results: response.data,
          pagination: {
            more: more_pagination,
          },
        };
      },
    },
  });

  data_select = []; // gabiarra para pegar o obj escolhido no select2
  $("#local").select2({
    theme: "bootstrap4",
    ajax: {
      url: base_url + "/helper/get_locals",
      dataType: "json",

      data: function (params) {
        var query = {
          term: params.term,
          page: params.page || 1,
        };

        // Query parameters will be ?search=[term]&page=[page]
        return query;
      },
      processResults: function (response) {
        //se a primeira paginacao
        if (response.current_page == 1) {
          data_select = response.data;
        } else {
          data_select = data_select.concat(response.data);
        }

        // Transforms the top-level key of the response object from 'items' to 'results'
        let more_pagination = true;
        //se não tem mais paginas
        if (response.next_page_url == null) {
          more_pagination = false;
        }
        return {
          results: response.data,
          pagination: {
            more: more_pagination,
          },
        };
      },
    },
  });

  $("#user").select2({
    theme: "bootstrap4",
    ajax: {
      url: base_url + "/helper/get_users",
      dataType: "json",

      data: function (params) {
        var query = {
          term: params.term,
          page: params.page || 1,
        };

        // Query parameters will be ?search=[term]&page=[page]
        return query;
      },
      processResults: function (response) {
        //se a primeira paginacao
        if (response.current_page == 1) {
          data_select = response.data;
        } else {
          data_select = data_select.concat(response.data);
        }

        // Transforms the top-level key of the response object from 'items' to 'results'
        let more_pagination = true;
        //se não tem mais paginas
        if (response.next_page_url == null) {
          more_pagination = false;
        }
        return {
          results: response.data,
          pagination: {
            more: more_pagination,
          },
        };
      },
    },
  });

  // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso.");
  function DefaultAlert(type, msg) {
    Toast.fire({
      icon: type,
      title: msg,
    });
  }
});
