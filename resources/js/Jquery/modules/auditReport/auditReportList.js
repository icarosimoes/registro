$(document).ready(function () {
    var base_url = window.location.origin;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
   
    $(".mask").maskMoney({
        allowNegative: false,
        allowZero: true,
        thousands: '',
        decimal: ',',
        affixesStay: false,
        precision:0
    });


  });   