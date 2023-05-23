var base_url = window.location.origin;
$(document).ready(function() {
  
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
       $('#btn_notification_bag').text(response.length)
    })
  }

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  }); 
});

