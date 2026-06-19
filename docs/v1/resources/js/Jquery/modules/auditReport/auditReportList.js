
var base_url = window.location.origin;


$(function () {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    $('.btn_delete').on('click', function (e) {

        $('#ModalDelete').modal('show');
        $('#id').val($(this).data('id'));

    })

    $('#btnDelete').on('click', function (e) {
        let route = base_url + '/event/audit_report/' + $('#id').val();
        let data = {
            _method: 'DELETE',
            id: $('#id').val(),
        }
        $.post(route, data, function (result) {

            $('#ModalDelete').modal('hide');
            DefaultAlert('success', 'Relatório de auditoria excluído!');
            // volta para a tela de listagem
            setTimeout(function () {
                window.location.reload();
            }, 1000);

        })
    })

    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }
});   