var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get("status")

    if (status == null) {
        $('#status').val(0)
    } else {
        $('#status').val(status)
    }

    $('#filter').on('click', () => {

        if ($('#card_filter').attr('data-visible') == 'true') {
            //escodido
            $('#card_filter').attr('data-visible', 'false')
            $('#card_filter').hide()
        } else {
            //visible
            $('#card_filter').attr('data-visible', 'true')
            $('#card_filter').show()

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
                        if (response.current_page == 1) {
                            data_select = response.data
                        } else {
                            data_select = data_select.concat(response.data)
                        }

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let more_pagination = true;
                        //se não tem mais paginas
                        if (response.next_page_url == null) {
                            more_pagination = false
                        }
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
            $('#sector').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: base_url + '/helper/get_sectors',
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
                        if (response.current_page == 1) {
                            data_select = response.data
                        } else {
                            data_select = data_select.concat(response.data)
                        }

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let more_pagination = true;
                        //se não tem mais paginas
                        if (response.next_page_url == null) {
                            more_pagination = false
                        }
                        return {
                            results: response.data,
                            pagination: {
                                "more": more_pagination
                            }
                        }
                    }
                }
            });

        }

    })





});