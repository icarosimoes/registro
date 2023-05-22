$(document).ready(function() {
  
  $('#filter').on('click', () => {

    if ($('#card_filter').attr('data-visible') == 'true') {
        //escodido
        $('#card_filter').attr('data-visible', 'false')
        $('#card_filter').hide()
    } else {
        //visible
        $('#card_filter').attr('data-visible', 'true')
        $('#card_filter').show()
    }
  })
  

});