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


  //salva
  $('#btn_save').on('click', function (e) {

    let route = base_url + '/event/audit_report';
    let data = {
      date: $('#date').val(),
      occupation:  converteMoedaFloat($('#occupation').val()),
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
     
    }
    $.post(route, data, function (result) {
      DefaultAlert('success', 'Relatório de auditoria salvo com sucesso!');
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