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
        
        const frequencyAdm = getFrequencyAdm() 
        const frequencyProd =  getFrequencyProd()
        const sub =  getSub()
        const equipament = getEquipment()  
        const activity = getActivity()
        const obs = getObs()
        
         


        //$('.overlay').removeClass('d-none');
        let status =null 
              
         let form_data = new FormData()
         form_data.append('frequency_adm',JSON.stringify(frequencyAdm));
         form_data.append('frequency_prod',JSON.stringify(frequencyProd));
         form_data.append('sub',JSON.stringify(sub));
         form_data.append('equipament',JSON.stringify(equipament));
         form_data.append('activity',JSON.stringify(activity));
         form_data.append('obs',JSON.stringify(obs));
            
         //carrega os anexos atividades
         let count = 0
         $('.activity_attachment').each((index,element)=>{
           form_data.append('activity_attachment-'+count, $(element).prop('files')[0]); 
           count++
         })
               
         let route  = '/event/work_diary'

        $.ajax({
            url: route, // Url do lado server que vai receber o arquivo
            data: form_data,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
              DefaultAlert("success", 'Salvo com sucesso !');   
              //window.location.replace(base_url + "/event/check_suite");
            }
        }).catch(()=>{
             DefaultAlert("error", 'Não foi possivel salvar');   
        }).always(()=>{
             $('.overlay').addClass('d-none');
        })

        
    });

    //adicionar frequecia deto adm
    $('#btn_add_frequency').on('click',()=>{
     let html = `<tr>
                    
                    <td><input type="text"  class="form-control form-control-sm freq_adm_role"></td>
                    <td><input type="text"   class="form-control form-control-sm mask freq_adm_total"></td>
                    <td><input type="text" class="form-control form-control-sm mask freq_adm_absent"></td>
                    <td><input type="text" class="form-control form-control-sm mask freq_adm_effective"></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_obs"></td>
                </tr>`; 
     
      $('#body_frequency_adm').append(html)
      $(".mask").mask("999999999999");
    })

    $('#btn_add_frequency_prod').on('click',()=>{
      
      let html = `<tr>
                     
                     <td><input type="text" class="form-control form-control-sm freq_prod_role"></td>
                     <td><input type="text" class="form-control form-control-sm  mask freq_prod_total"></td>
                     <td><input type="text" class="form-control form-control-sm mask freq_prod_absent"></td>
                     <td><input type="text" class="form-control form-control-sm mask freq_prod_effective"></td>
                     <td><input type="text" class="form-control form-control-sm freq_prod_obs"></td>
                 </tr>`; 
      
       $('#body_frequency_prod').append(html)
       $(".mask").mask("999999999999");
     })
     
     $('#btn_add_sub').on('click',()=>{
      
      let html = `<tr>
                     <td><input type="text" class="form-control form-control-sm sub_company"></td>
                     <td><input type="text" class="form-control form-control-sm sub_role"></td>
                     <td><input type="text" class="form-control form-control-sm mask sub_total"></td>
                     <td><input type="text" class="form-control form-control-sm mask sub_absent"></td>
                     <td><input type="text" class="form-control form-control-sm mask sub_effective"></td>
                     <td><input type="text" class="form-control form-control-sm sub_obs"></td>
                 </tr>`; 
      
       $('#body_sub').append(html)
       $(".mask").mask("999999999999");
     })
     
     $('#btn_add_equipament').on('click',()=>{
      
      let html = `<tr>
                     <td><input type="text" class="form-control form-control-sm equipament_supply"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_description"></td>
                     <td><input type="date" class="form-control form-control-sm equipament_start"></td>
                     <td><input type="date" class="form-control form-control-sm equipament_end"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_service"></td>
                     
                 </tr>`; 
      
       $('#body_equipament').append(html)
     })
 
     $('#btn_add_activity').on('click',()=>{
      
      let html = `<tr>
                     <td><input type="text" class="form-control form-control-sm activity_sector"></td>
                     <td><input type="text" class="form-control form-control-sm activity_team"></td>
                     <td><input type="text" class="form-control form-control-sm activity_register"></td>
                     <td><input type="text" class="form-control form-control-sm activity_description"></td>
                     <td><input type="file" class="form-control form-control-sm activity_attachment"></td>
                  </tr>`; 
      
       $('#body_activity').append(html)
     })
     
     $('#btn_add_obs').on('click',()=>{
      
      let html = `<tr>
                     <td><input type="text" class="form-control form-control-sm obs_sector"></td>
                     <td><input type="text" class="form-control form-control-sm obs_description"></td>
                     <td><input type="text" class="form-control form-control-sm obs_register"></td>
                     <td><input type="text" class="form-control form-control-sm obs_obs"></td>
                     
                  </tr>`; 
      
       $('#body_obs').append(html)
     })
 
     
    function getFrequencyAdm (){
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

     function getFrequencyProd (){
         const freq_prod_roles =  $('.freq_prod_role') 
         const freq_prod_totals =  $('.freq_prod_total')
         const freq_prod_absents =  $('.freq_prod_absent')
         const freq_prod_effectives =  $('.freq_prod_effective')
         const freq_prod_obs =  $('.freq_prod_obs')
         
         
         let frequency_prod = []
         freq_prod_roles.each((index,element) => {
          
          const item = {
            role: $(element).val(), 
            total: $(freq_prod_totals[index]).val(),
            absent:$(freq_prod_absents[index]).val(),
            effective:$(freq_prod_effectives[index]).val(),
            obs:$(freq_prod_obs[index]).val(),
           }  

           frequency_prod.push(item)
         });
         return frequency_prod
     }
   
     function getSub (){
      const sub_companies =  $('.sub_company') 
      const sub_roles =  $('.sub_role') 
      const sub_totals =  $('.sub_total')
      const sub_absents =  $('.sub_absent')
      const sub_effectives =  $('.sub_effective')
      const sub_obs =  $('.sub_obs')
      
      
      let sub = []
      sub_companies.each((index,element) => {
       
       const item = {
         company: $(element).val(), 
         role: $(sub_roles[index]).val(),
         total: $(sub_totals[index]).val(),
         absent:$(sub_absents[index]).val(),
         effective:$(sub_effectives[index]).val(),
         obs:$(sub_obs[index]).val(),
        }  

        sub.push(item)
      });
      return sub
  }

  function getEquipment(){
    
    const equipament_supplies =  $('.equipament_supply') 
    const equipament_descriptions =  $('.equipament_description') 
    const equipament_starts =  $('.equipament_start')
    const equipament_ends =  $('.equipament_end')
    const equipament_services =  $('.equipament_service')
    
    let equipaments = []
    equipament_supplies.each((index,element) => {
     
     const item = {
       supply: $(element).val(), 
       description: $(equipament_descriptions[index]).val(),
       start: $(equipament_starts[index]).val(),
       end:$(equipament_ends[index]).val(),
       service:$(equipament_services[index]).val(),
       
      }  

      equipaments.push(item)
    });
    return equipaments
  }

  function getActivity(){
    
    const activity_sectors =  $('.activity_sector') 
    const activity_teams =  $('.activity_team') 
    const activity_registers =  $('.activity_register')
    const activity_descriptions =  $('.activity_description')
    //const activity_attachments =  $('.activity_attachment')
    
    let activities = []
    activity_sectors.each((index,element) => {
     
     const item = {
       sector: $(element).val(), 
       description: $(activity_teams[index]).val(),
       register: $(activity_registers[index]).val(),
       description:$(activity_descriptions[index]).val(),
      // attachment:'$(activity_attachments[index]).val()',
      }  

      activities.push(item)
    });
    return activities

  }
  function getObs(){
    
    const obs_sectors =  $('.obs_sector') 
    const obs_descriptions =  $('.obs_description') 
    const obs_registers =  $('.obs_register')
    const obs_obs =  $('.obs_obs')
    
    
    let obs = []
    obs_sectors.each((index,element) => {
     
     const item = {
       sector: $(element).val(), 
       description: $(obs_descriptions[index]).val(),
       register: $(obs_registers[index]).val(),
       obs:$(obs_obs[index]).val(),
       
      }  

      obs.push(item)
    });
    return obs

  }
     

    // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 
    function DefaultAlert(type, msg) {
        Toast.fire({
            icon: type,
            title: msg
        })
    }

   
   
});