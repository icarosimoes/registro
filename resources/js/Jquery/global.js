var base_url = window.location.origin;
$(document).ready(function() {
  window.notification_lenght = 0; 
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

  refreshNotification()
  
  setInterval(()=>{
    refreshNotification()
  },10000)

  function refreshNotification(){
    let data = {
    //  user_id:$('#btn_notification').data('user') 
    }
    let route = base_url+'/notification'  
    
    $.post(route,data,(response)=>{
      if( window.notification_lenght == 0){
        window.notification_lenght = response.length
      }
        if(window.notification_lenght < response.length){
          window.notification_lenght = response.length
          DefaultAlert('info','Nova notificação !')
        }

        $('#btn_notification_bag').text(response.length)
       $('#menu_notification').text(response.length)
       
    })
  }

  const menu_notification = $('.bags_notification').siblings()
  $(menu_notification[0]).append('<i class="badge badge-danger" id="menu_notification">0</i>')


  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  }); 
 
  function DefaultAlert(type, msg){
    Toast.fire({
        icon: type,
        title: msg
      })
}
});

