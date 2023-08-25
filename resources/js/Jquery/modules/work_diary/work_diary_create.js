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
        
        $('.overlay').removeClass('d-none');
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
              window.location.replace(base_url + "/event/work_diary");
            }
        }).catch(()=>{
             DefaultAlert("error", 'Não foi possivel salvar');   
        }).always(()=>{
             $('.overlay').addClass('d-none');
        })

        
    });


    //quantidade select
      const options_qtd =`
        <option>0</option>
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
        <option>6</option>
        <option>7</option>
        <option>8</option>
        <option>9</option>
        <option>10</option>
        <option>11</option>
        <option>12</option>
        <option>13</option>
        <option>14</option>
        <option>15</option>
        <option>16</option>
        <option>17</option>
        <option>18</option>
        <option>19</option>
        <option>20</option>
        <option>21</option>
        <option>22</option>
        <option>23</option>
        <option>24</option>
        <option>25</option>
        <option>26</option>
        <option>27</option>
        <option>28</option>
        <option>29</option>
        <option>30</option>
        <option>31</option>
        <option>32</option>
        <option>33</option>
        <option>34</option>
        <option>35</option>
        <option>36</option>
        <option>37</option>
        <option>38</option>
        <option>39</option>
        <option>40</option>
        <option>41</option>
        <option>42</option>
        <option>43</option>
        <option>44</option>
        <option>45</option>
        <option>46</option>
        <option>47</option>
        <option>48</option>
        <option>49</option>
        <option>50</option>
      `

    //carregamento padrão 

    admDafault = [
      'Engenheiro  Civil',
      'Téc. de Edificações',
      'Estagiário de Engenharia',
      'Tec. de Segurança ',
      'Aux. de Almoxarifado',
      'Encarregado Administrativo Financeiro',
      'Cabo de Turma Carpintaria',
      'Vigia',
      'Auxiliar de Ferramentaria',
      'Estagiário de Tec de Seg.'
    ];

    admDafault.forEach((item,index) => {
      index = 'AD'+index
      let html = `<tr id="row-${index}">
     
                    <td><input value="${item}" type="text"  class="form-control form-control-sm freq_adm_role"></td>
                    <td><input  id="total-${index}" type="text" readonly  value="0"  class="form-control form-control-sm  freq_adm_total"></td>
                    <td><select id="absent-${index}" class="form-control form-control-sm freq_adm_absent">${options_qtd}</select></td>
                    <td><select id="effective-${index}" class="form-control form-control-sm freq_adm_effective">${options_qtd}</select></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_obs"></td>
                    <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_freq_adm "><i class="fas fa-trash "></i></button></td>

                </tr>`; 
     
      $('#body_frequency_adm').append(html)
    })
    
    $(document).on('change','.freq_adm_absent',(e)=>{
      let id = $(e.currentTarget).attr('id')
      let idRow = id.split('-')[1]
      calcTotal(idRow)  

    })
    $(document).on('change','.freq_adm_effective',(e)=>{
      let id = $(e.currentTarget).attr('id')
      let idRow = id.split('-')[1]
      calcTotal(idRow)  

    })
    $(document).on('change','.freq_prod_absent',(e)=>{
      let id = $(e.currentTarget).attr('id')
      let idRow = id.split('-')[1]
      calcTotal(idRow)  

    })
    $(document).on('change','.freq_prod_effective',(e)=>{
      let id = $(e.currentTarget).attr('id')
      let idRow = id.split('-')[1]
      calcTotal(idRow)  

    })
    $(document).on('change','.sub_absent',(e)=>{
      let id = $(e.currentTarget).attr('id')
      let idRow = id.split('-')[1]
      calcTotal(idRow)  

    })
    $(document).on('change','.sub_effective',(e)=>{
      let id = $(e.currentTarget).attr('id')
      let idRow = id.split('-')[1]
      calcTotal(idRow)  

    })
    function calcTotal(idRow){
      const absent =  $('#absent-'+idRow).val() // ausente
      const effective =  $('#effective-'+idRow).val() // efetivo
      const total = effective - absent
      
      if(Math.sign(total) == -1){ //verifica o total é um numero negativo 
        DefaultAlert('error','O campo AUSENTE deve ser menor ou igual ao campo EFETIVO')
        $('#absent-'+idRow).addClass('is-invalid')
        $('#btn_submit').attr('disabled',true)
        return false
      }else{
        $('#absent-'+idRow).removeClass('is-invalid')  
        $('#btn_submit').attr('disabled',false)
        $('#total-'+idRow).val(effective - absent)
        calcTotalAmountAdm();//calc totai adm
        calcTotalAmountProd()// calc totais de producao
        calcTotalAmountSub() //calc totais sub
      } 
   
    }

    function calcTotalAmountAdm(){
      sumTotal = 0
      sumAbsent =0
      sumEffective = 0 
      
      $('.freq_adm_total').each((index,item)=>{
        sumTotal += parseInt($(item).val())
      })
      
      $('.freq_adm_absent').each((index,item)=>{
        sumAbsent += parseInt($(item).val())
      })

      $('.freq_adm_effective').each((index,item)=>{
        sumEffective += parseInt($(item).val())
      })

      $('#sumTotalAdm').text(sumTotal)
      $('#sumAbsentAdm').text(sumAbsent)
      $('#sumEffectiveAdm').text(sumEffective)
    }

    function calcTotalAmountProd(){
      sumTotal = 0
      sumAbsent =0
      sumEffective = 0 
      
      $('.freq_prod_total').each((index,item)=>{
        sumTotal += parseInt($(item).val())
      })
      
      $('.freq_prod_absent').each((index,item)=>{
        sumAbsent += parseInt($(item).val())
      })

      $('.freq_prod_effective').each((index,item)=>{
        sumEffective += parseInt($(item).val())
      })

      $('#sumTotalProd').text(sumTotal)
      $('#sumAbsentProd').text(sumAbsent)
      $('#sumEffectiveProd').text(sumEffective)
    }


    function calcTotalAmountSub(){
      sumTotal = 0
      sumAbsent =0
      sumEffective = 0 
      
      $('.sub_total').each((index,item)=>{
        sumTotal += parseInt($(item).val())
      })
      
      $('.sub_absent').each((index,item)=>{
        sumAbsent += parseInt($(item).val())
      })

      $('.sub_effective').each((index,item)=>{
        sumEffective += parseInt($(item).val())
      })

      $('#sumTotalSub').text(sumTotal)
      $('#sumAbsentSub').text(sumAbsent)
      $('#sumEffectiveSub').text(sumEffective)
    }

    // carregamento padra producao
    
    prodDafault = [
    'Carpinteiro',
    'Eletricista',	
    'Encanador',	
    'Pedreiro',	
    'Ajudante Prático Eletricista',
    'Ajudante Prático Carpintaria',
    'Ajudante Prático Pedreiro',
    'Ajudante Comum',
    'Operador de Guincho',
    'Operador de Grua',
    'Sinaleiro',
    ];
    
    
    prodDafault.forEach((item,index) => {
      index = 'PD'+index
      let html = `<tr id="row-${index}">
                  <td><input value="${item}" type="text" class="form-control form-control-sm freq_prod_role"></td>
                  <td><input id="total-${index}" readonly type="text" value="0" class="form-control form-control-sm  mask freq_prod_total"></td>
                  <td><select id="absent-${index}" class="form-control form-control-sm freq_prod_absent">${options_qtd}</select></td>
                  <td><select id="effective-${index}" class="form-control form-control-sm freq_prod_effective">${options_qtd}</select></td>
                  <td><input type="text" class="form-control form-control-sm freq_prod_obs"></td>
                  <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_freq_prod "><i class="fas fa-trash "></i></button></td>
                </tr>`; 
     
                $('#body_frequency_prod').append(html)
    })
    
    
    //adicionar frequecia deto adm
    $('#btn_add_frequency').on('click',()=>{
      
     const timestamp = new Date().getTime();

     let html = `<tr id="row-${timestamp}">
     
                    <td><input type="text"  class="form-control form-control-sm freq_adm_role"></td>
                    <td><input  id="total-${timestamp}" readonly type="text"  value="0"  class="form-control form-control-sm mask freq_adm_total"></td>
                    <td><select id="absent-${timestamp}" class="form-control form-control-sm freq_adm_absent">${options_qtd}</select></td>
                    <td><select id="effective-${timestamp}" class="form-control form-control-sm freq_adm_effective">${options_qtd}</select></td>
                    <td><input type="text" class="form-control form-control-sm freq_adm_obs"></td>
                    <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_freq_adm "><i class="fas fa-trash "></i></button></td>

                </tr>`; 
     
      $('#body_frequency_adm').append(html)
      
      $(".mask").maskMoney({
        allowNegative: false,
        allowZero: true,
        thousands: '',
        decimal: ',',
        affixesStay: false,
        precision:0
    });
      // $(".mask").mask("999999999999");
    })
    
    //REMOVE LINHA FREQ ADM
    $(document).on('click','.remove_freq_adm',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
    }) 


    //ADD LINHA FREQ PROD
    $('#btn_add_frequency_prod').on('click',()=>{
      
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     
                     <td><input type="text" class="form-control form-control-sm freq_prod_role"></td>
                     <td><input id="total-${timestamp}" readonly type="text" value="0" class="form-control form-control-sm  mask freq_prod_total"></td>
                     <td><select id="absent-${timestamp}" class="form-control form-control-sm freq_prod_absent">${options_qtd}</select></td>
                    <td><select  id="effective-${timestamp}" class="form-control form-control-sm freq_prod_effective">${options_qtd}</select></td>
                     <td><input type="text" class="form-control form-control-sm freq_prod_obs"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_freq_prod "><i class="fas fa-trash "></i></button></td>
                 </tr>`; 
      
       $('#body_frequency_prod').append(html)
       $(".mask").maskMoney({
        allowNegative: false,
        allowZero: true,
        thousands: '',
        decimal: ',',
        affixesStay: false,
        precision:0
    });
     })
     
     //REMOVE LINHA FREQ ADM
    $(document).on('click','.remove_freq_prod',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
    })


     $('#btn_add_sub').on('click',()=>{
      
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     <td><input type="text" class="form-control form-control-sm sub_company"></td>
                     <td><input type="text" class="form-control form-control-sm sub_role"></td>
                     <td><input id="total-${timestamp}" readonly type="text" value="0" class="form-control form-control-sm  sub_total"></td>
                     <td><select id="absent-${timestamp}" class="form-control form-control-sm sub_absent">${options_qtd}</select></td>
                     <td><select id="effective-${timestamp}" class="form-control form-control-sm sub_effective">${options_qtd}</select></td>
                     <td><input type="text" class="form-control form-control-sm sub_obs"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_sub "><i class="fas fa-trash "></i></button></td>
                 </tr>`; 
      
       $('#body_sub').append(html)
    //    $(".mask").maskMoney({
    //     allowNegative: false,
    //     allowZero: true,
    //     thousands: '',
    //     decimal: ',',
    //     affixesStay: false,
    //     precision:0
    // });
     })

     //REMOVE LINHA SUB
    $(document).on('click','.remove_sub',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
    })
     
     $('#btn_add_equipament').on('click',()=>{
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     <td><input type="text" class="form-control form-control-sm equipament_supply"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_description"></td>
                     <td><input type="date" required class="form-control form-control-sm equipament_start"></td>
                     <td><input type="date" required class="form-control form-control-sm equipament_end"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_service"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td>
                 </tr>`; 
      
       $('#body_equipament').append(html)
     })
 
    //REMOVE LINHA SUB
    $(document).on('click','.remove_equipament',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
    })
     



    $('#btn_add_activity').on('click',()=>{
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     <td><input value="Produção" type="text" class="form-control form-control-sm activity_sector"></td>
                     <td><input type="text" class="form-control form-control-sm activity_team"></td>
                     <td><input type="text" class="form-control form-control-sm activity_register"></td>
                     <td><input type="text" class="form-control form-control-sm activity_description"></td>
                     <td><input type="file" class="form-control form-control-sm activity_attachment"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_activity "><i class="fas fa-trash "></i></button></td>
                  </tr>`; 
      
       $('#body_activity').append(html)
     })
     
    //REMOVE LINHA ACTIVITY
    $(document).on('click','.remove_activity',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
    })
     
     $('#btn_add_obs').on('click',()=>{
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     <td><input value="Operacional" type="text" class="form-control form-control-sm obs_sector"></td>
                     <td><input type="text" class="form-control form-control-sm obs_description"></td>
                     <td><input type="text" class="form-control form-control-sm obs_register"></td>
                     <td><input type="text" class="form-control form-control-sm obs_obs"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_obs "><i class="fas fa-trash "></i></button></td>
                  </tr>`; 
      
       $('#body_obs').append(html)
     })
 
    //REMOVE LINHA OBS
    $(document).on('click','.remove_obs',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
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
       team: $(activity_teams[index]).val(),
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