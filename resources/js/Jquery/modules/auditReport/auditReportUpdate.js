var base_url = window.location.origin;

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$(function () {

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

  $(".mask_float").maskMoney({
    allowNegative: false,
    allowZero: true,
    thousands: '',
    decimal: ',',
    affixesStay: false,
    precision: 2
  });

  $(".mask_integer").maskMoney({
    allowNegative: false,
    allowZero: true,
    thousands: '',
    decimal: ',',
    affixesStay: false,
    precision: 0
  });


  let dataTable1 = [];
  $('#addTable1').on('click', function () {
    dataTable1.push({
      reserve:'',
      name:'',
      pax:'',
    });
    
    
    renderizeTable1();

  });
  //edit table1
  $(document).on('keyup', '.edit_table1', function () {
    let index = $(this).data('index');
    let column = $(this).data('column');
    let value = $(this).val();
    dataTable1[index][column] = value;
    
  });

  function renderizeTable1() {
    $('#table1').html(''); // Limpa o conteúdo atual da tabela
    dataTable1.forEach(function (item, index) {
      let linha = '<tr>';
      linha += `<td><input data-column="reserve" data-index="${index}" value="${item.reserve}" type="text" class="form-control edit_table1 " ></td>`;
      linha += `<td><input data-column="name" data-index="${index}" value="${item.name}" type="text" class="form-control edit_table1 "  ></td>`;
      linha += `<td><input data-column="pax" data-index="${index}" value="${item.pax}" type="text" class="form-control edit_table1 "  ></td>`;
      linha += '</tr>';

      $('#table1').append(linha);
    });
  }


  let dataTable2 = [];
  $('#addTable2').on('click', function () {
    dataTable2.push({
      name:'',
      pax:'',
    });
    
    
    renderizeTable2();

  });
  //edit table2
  $(document).on('keyup', '.edit_table2', function () {
    let index = $(this).data('index');
    let column = $(this).data('column');
    let value = $(this).val();
    dataTable2[index][column] = value;
    
  });

  function renderizeTable2() {
    $('#table2').html(''); // Limpa o conteúdo atual da tabela
    dataTable2.forEach(function (item, index) {
      let linha = '<tr>';
        linha += `<td><input data-column="name" data-index="${index}" value="${item.name}" type="text" class="form-control edit_table2 "  ></td>`;
        linha += `<td><input data-column="pax" data-index="${index}" value="${item.pax}" type="text" class="form-control edit_table2 "  ></td>`;
        linha += '</tr>';

      $('#table2').append(linha);
    });
  }

  let dataTable3 = [];
  $('#addTable3').on('click', function () {
    dataTable3.push({
      reserve:'',
      name:'',
      pax:'',
    });
    
   
    renderizeTable3();

  });
  //edit table3
  $(document).on('keyup', '.edit_table3', function () {
    let index = $(this).data('index');
    let column = $(this).data('column');
    let value = $(this).val();
    dataTable3[index][column] = value;
    
  });

  function renderizeTable3() {
    $('#table3').html(''); // Limpa o conteúdo atual da tabela
    dataTable3.forEach(function (item, index) {
      let linha = '<tr>';
      linha += `<td><input data-column="reserve" data-index="${index}" value="${item.reserve}" type="text" class="form-control edit_table3 " ></td>`;
      linha += `<td><input data-column="name" data-index="${index}" value="${item.name}" type="text" class="form-control edit_table3 "  ></td>`;
      linha += `<td><input data-column="pax" data-index="${index}" value="${item.pax}" type="text" class="form-control edit_table3 "  ></td>`;
      linha += '</tr>';

      $('#table3').append(linha);
    });
  }







  //salva
  $('#btn_save').on('click', function (e) {

    let route = base_url + '/event/audit_report';
    let data = {
      date: $('#date').val(),
      occupation: converteMoedaFloat($('#occupation').val()),
      average_daily: $('#average_daily').val(),
      guests: $('#guests').val(),
      uh: $('#uh').val(),
      maintenance_apartment: $('#maintenance_apartment').val(),
      cleaning: $('#cleaning').val(),
      walk_in: $('#walk_in').val(),
      obs: $('#obs').val(),
      AB: $('#AB').val(),
      reception: $('#reception').val(),
      reservations: $('#reservations').val(),
      governance: $('#governance').val(),
      maintenance: $('#maintenance').val(),
      ti: $('#ti').val(),
      security: $('#security').val(),
      dataTable1: JSON.stringify(dataTable1),
      dataTable2: JSON.stringify(dataTable2),
      dataTable3: JSON.stringify(dataTable3),
    }
    $.post(route, data, function (result) {

      DefaultAlert('success', 'Relatório de auditoria salvo com sucesso!');
      // volta para a tela de listagem
              window.location.href = base_url + '/event/audit_report';
      
    })




  })

  function number_to_price(v) {
    if (v == 0) { return '0,00'; }
    v = parseFloat(v);
    v = v.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    v = v.split('.').join('*').split(',').join('.').split('*').join(',');
    return v;
  }
  function number_to_price3(v) {
    if (v == 0) { return '0,000'; }
    v = parseFloat(v);
    v = v.toFixed(3).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    v = v.split('.').join('*').split(',').join('.').split('*').join(',');
    return v;
  }
  function converteMoedaFloat(valor) {
    if (valor === "") {
      valor = 0;
    } else {
      valor = valor.replace(".", "");
      valor = valor.replace(",", ".");
      valor = parseFloat(valor);
    }
    return valor;
  }
  function DefaultAlert(type, msg) {
    Toast.fire({
      icon: type,
      title: msg
    })
  }

});