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
        });
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