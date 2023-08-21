var base_url = window.location.origin;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    
    $('form[name="form"]').submit(function(event) {

        event.preventDefault();
        
        frequency_adm = getFrequncyAdm() 
        
        

        
        console.log(frequency_adm)
         


        return false;
        $('.overlay').removeClass('d-none');
 

        let status =null 
        if($("#status1").is(":checked")==true){
            status = 'liberado';
        }else{
            status = 'bloqueado';
        } 
        
        const  occurrences_id= [] 
        $('input[name="occurrences_id"]').each((index,element)=>{
            occurrences_id.push($(element).val())
        })

        const valuation = [] 
        $('select[name="item"]').each((index,element)=>{
            valuation.push($(element).val())
            
        })  
        const register = [] 
        $('input[name="register"]').each((index,element)=>{
            register.push($(element).val())
            
        })  
        
        form_data = {
            date:$("#date").val(),
            local_id:$("#local").val(),
            user_id:$("#user").val(),
            status:status,
            maid:$("#maid").val(),
            obs:$("#obs").val(),
            valuation:valuation,
            register:register,
            occurrences_id:occurrences_id
        };

        let route  = '/event/check_suite'
        $.post(route,form_data,(response)=>{
            DefaultAlert("success", 'Salvo com sucesso !');   
            window.location.replace(base_url + "/event/check_suite");
        }).catch(()=>{
            DefaultAlert("error", 'Não foi possivel salvar');   
        }).always(()=>{
            $('.overlay').addClass('d-none');
        })
    });

    //adicionar frequecia deto adm
    $('#btn_add_frequency').on('click',()=>{
     let html = `<tr>
                    <td>ADM</td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_role"></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_total"></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_absent"></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_effective"></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_obs"></td>
                </tr>`; 
     
      $('#body_frequency_adm').append(html)
    })

     
    function getFrequncyAdm (){
      const freq_adm_roles =  $('.freq_adm_role') 
         const freq_adm_totals =  $('.freq_adm_total')
         const freq_adm_absents =  $('.freq_adm_absent')
         const freq_adm_effectives =  $('.freq_adm_effective')
         const freq_adm_obs =  $('.freq_adm_obs')
         
         
         let frequency_adm = []
         freq_adm_roles.each((index,element) => {
          
          const item = {
            role: $(element).val(), 
            total: $(freq_adm_totals[index]).val(),
            absent:$(freq_adm_absents[index]).val(),
            effective:$(freq_adm_effectives[index]).val(),
            obs:$(freq_adm_obs[index]).val(),
           }  

           frequency_adm.push(item)
         });
         return frequency_adm
     }

     

    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

   
   
});