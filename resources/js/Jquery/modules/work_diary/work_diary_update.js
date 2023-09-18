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

    //carregar os dados 

    const load_shift_time =  JSON.parse($('#load_shift_time').val())
    const load_frequency_adm =  JSON.parse($('#load_frequency_adm').val())
    const load_frequency_prod =  JSON.parse($('#load_frequency_prod').val())
    const load_sub =  JSON.parse($('#load_sub').val())
    const load_equipament =  JSON.parse($('#load_equipament').val())
    const load_activity =  JSON.parse($('#load_activity').val())
    const load_obs =  JSON.parse($('#load_obs').val())
    
      


    // carrega Turno/Tempo 
     load_shift_time.forEach((item,index) => {
      const shift  = $('.'+item.shift)
      
      $(shift[0]).val(item.clear)
      $(shift[1]).val(item.cloudy)
      $(shift[2]).val(item.rain)
      $(shift[3]).val(item.impractical)
      
     })
     
          //<td><input type="text" class="form-control form-control-sm mask freq_adm_total" ></td>
                        // <td><input type="text" class="form-control form-control-sm mask freq_adm_absent" value="${element.absent}"></td>
                        // <td><input type="text" class="form-control form-control-sm mask freq_adm_effective" value="${element.effective}"></td>
    load_frequency_adm.forEach((element,index) => {
      index =  'A'+index  
      let html = `<tr id="row-${index}">
                        <td><input type="text" class="form-control form-control-sm freq_adm_role" value="${element.role}"></td>
                        <td><input  id="total-${index}" readonly type="text"  value="${element.total}"  class="form-control form-control-sm mask freq_adm_total"></td>
                        <td><select id="absent-${index}"   class="form-control   form-control-sm freq_adm_absent">${options_qtd}</select></td>
                        <td><select id="effective-${index}" value="${element.effective}" class="form-control form-control-sm freq_adm_effective">${options_qtd}</select></td>
                        <td><input type="text" class="form-control form-control-sm freq_adm_obs" value="${element.obs}"></td>
                        <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                    </tr>`; 
     
                    
      $('#body_frequency_adm').append(html)
      $('#absent-'+index).val(element.absent)              
      $('#effective-'+index).val(element.effective)              
      
    });
    calcTotalAmountAdm();//calc totai adm
    

    load_frequency_prod.forEach((element,index) => {
        index =  'P'+index
        let html = `<tr id="row-${index}">
                     <td><input type="text" class="form-control form-control-sm freq_prod_role" value="${element.role}" ></td>
                     <td><input id="total-${index}" readonly type="text" value="${element.total}" class="form-control form-control-sm  mask freq_prod_total"></td>
                     <td><select id="absent-${index}" class="form-control form-control-sm freq_prod_absent">${options_qtd}</select></td>
                    <td><select  id="effective-${index}" class="form-control form-control-sm freq_prod_effective">${options_qtd}</select></td>
                     <td><input type="text" class="form-control form-control-sm freq_prod_obs" value="${element.obs}"></td>
                     <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                 </tr>`; 
      
       $('#body_frequency_prod').append(html)
       $('#absent-'+index).val(element.absent)              
       $('#effective-'+index).val(element.effective)
       
    })
   calcTotalAmountProd()// calc totais de producao
   
    load_sub.forEach((element,index) => {
       index =  'S'+index
       let html = `<tr id="row-${index}">
                     <td><input type="text" class="form-control form-control-sm sub_company" value="${element.company}"></td>
                     <td><input type="text" class="form-control form-control-sm sub_role" value="${element.role}"></td>
                     <td><input id="total-${index}" readonly type="text" value="${element.total}" class="form-control form-control-sm  mask sub_total"></td>
                     <td><select id="absent-${index}" class="form-control form-control-sm sub_absent">${options_qtd}</select></td>
                    <td><select  id="effective-${index}" class="form-control form-control-sm sub_effective">${options_qtd}</select></td>
                     <td><input type="text" class="form-control form-control-sm sub_obs" value="${element.obs}"></td>
                     <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                   </tr>`; 
      
       $('#body_sub').append(html)
       
      $('#absent-'+index).val(element.absent)              
      $('#effective-'+index).val(element.effective)        

       
    })
    calcTotalAmountSub() //calc totais sub 
    
    load_equipament.forEach((element,index) => {
      index =  'E'+index
      let html = `<tr id="row-${index}" >
                     <td><input type="text" class="form-control form-control-sm equipament_supply" value="${element.supply}"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_description" value="${element.description}"></td>
                     <td><input type="date" class="form-control form-control-sm equipament_start" value="${element.start}"></td>
                     <td><input type="date" class="form-control form-control-sm equipament_end" value="${element.end}"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_service" value="${element.service}"></td>
                     <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                 </tr>`; 
      
       $('#body_equipament').append(html)
    })
    
    load_activity.forEach((element,index) => {
        index = 'Q'+index  
      let html = `<tr id="row-${index}">
                     <td>
                        <input type="hidden" class="form-control form-control-sm activity_id" value="${element.id}">
                        <input type="text" class="form-control form-control-sm activity_sector" value="${element.sector}">
                     </td>
                     <td><input type="text" class="form-control form-control-sm activity_team" value="${element.team}"></td>
                     <td><input type="text" class="form-control form-control-sm activity_description" value="${element.description}"></td>
                     <td>
                        <div class="input-group ">
                        <input type="file" id="file" class="form-control form-control-sm activity_attachment">
                        <div class="input-group-append">
                            <a target="_blank" href="${base_url+'/event/work_diary/download_activity/'+element.id}" class="btn btn-secondary form-control-sm ${(element.attachment ==null?'disabled':'' )}"><i class="fas fa-download"></i></a>
                        </div>
                        </div>
                    </td>
                    <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                    <td class="">
                      <button data-count="${index}" type='button' class="btn btn-secondary btn-sm filter "><i class="fas fa-filter"></i></button>
                      <input type="hidden" class="activity_occurrences_id" id="item-${index}" value="${element.occurrence_id}">
                      <a href="${base_url}/occurrence/list/edit/${element.occurrence_id}" style="width:50px" class="btn btn-sm btn-success ${(element.occurrence_id?'':'d-none')} show_occurence_id"><i class="far fa-registered">${element.occurrence_id}</i></a>
                     </td>
                    </tr>`; 
      
       $('#body_activity').append(html)
    })

    load_obs.forEach((element,index) => {
    
    index = 'O'+index  
    let html = `<tr id="row-${index}">
                     <td><input type="text" class="form-control form-control-sm obs_sector" value="${element.sector}"></td>
                     <td><input type="text" class="form-control form-control-sm obs_description" value="${element.description}"></td>
                     <td><input type="text" class="form-control form-control-sm obs_register" value="${element.register}"></td>
                     <td><input type="text" class="form-control form-control-sm obs_obs" value="${element.obs}"></td>
                     <td class="text-right"><button data-count="${index}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                  </tr>`; 
      
       $('#body_obs').append(html)
    })
    ///////////////////////////////////
    
    $('form[name="form"]').submit(function(event) {

        event.preventDefault();
        
        const shiftTime = getShiftTime()
        const frequencyAdm = getFrequencyAdm() 
        const frequencyProd =  getFrequencyProd()
        const sub =  getSub()
        const equipament = getEquipment()  
        const activity = getActivity()
        const obs = getObs()
        
        
         


        //$('.overlay').removeClass('d-none');
        let status =null 
              
         let form_data = new FormData()
         form_data.append('date',$('#date').val());
         form_data.append('shift_time',JSON.stringify(shiftTime));
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
         const id = $('#id').val()    
         let route  = '/event/work_diary/'+id
         form_data.append('_method','PUT');
        $.ajax({
            url: route, 
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
     
     
    })

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
     })
     
     $('#btn_add_equipament').on('click',()=>{
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     <td><input  type="text" class="form-control form-control-sm equipament_supply"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_description"></td>
                     <td><input type="date" required class="form-control form-control-sm equipament_start"></td>
                     <td><input type="date" required class="form-control form-control-sm equipament_end"></td>
                     <td><input type="text" class="form-control form-control-sm equipament_service"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td>
                 </tr>`; 
      
       $('#body_equipament').append(html)
     })

     //REMOVE LINHA EQUIPAMENT
    $(document).on('click','.remove_equipament',(e)=>{
      const count = $(e.currentTarget).attr('data-count')
      $('#row-'+count).remove()
    }) 
 
     $('#btn_add_activity').on('click',()=>{
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}">
                     <td>
                     <input type="hidden" class="form-control form-control-sm activity_id" value="">
                     <input type="text" value="Produção" class="form-control form-control-sm activity_sector">
                     </td>
                     <td><input type="text" class="form-control form-control-sm activity_team"></td>
                     <td><input type="text" class="form-control form-control-sm activity_description"></td>
                     <td>
                        <div class="input-group ">
                          <input type="file" id="file" class="form-control form-control-sm activity_attachment">
                          <div class="input-group-append">
                              <a target="_blank" href="" class="btn btn-secondary form-control-sm disabled"><i class="fas fa-download"></i></a>
                          </div>
                        </div>
                     </td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                     <td class="">
                      <button data-count="${timestamp}" type='button' class="btn btn-secondary btn-sm filter "><i class="fas fa-filter"></i></button>
                      <input type="hidden" class="activity_occurrences_id" id="item-${timestamp}" >
                      <a    class="btn btn-sm btn-success d-none show_occurence_id" style="width:50px"> <i class="far fa-registered"></i></a>
                     </td>
                  </tr>`; 
      
       $('#body_activity').append(html)
     })
     
     $('#btn_add_obs').on('click',()=>{
      const timestamp = new Date().getTime();
      let html = `<tr id="row-${timestamp}" >
                     <td><input type="text" value="Operacional" class="form-control form-control-sm obs_sector"></td>
                     <td><input type="text" class="form-control form-control-sm obs_description"></td>
                     <td><input type="text" class="form-control form-control-sm obs_register"></td>
                     <td><input type="text" class="form-control form-control-sm obs_obs"></td>
                     <td class="text-right"><button data-count="${timestamp}" type='button' class="btn btn-danger btn-sm remove_equipament "><i class="fas fa-trash "></i></button></td> 
                  </tr>`; 
      
       $('#body_obs').append(html)
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

    $("#btnNext").click(function () {
      var name = $("#titleExport").val();
      const id = $("#id").val();
      if (!name) {
          name = "Indefinido";
      }
      $("#btnExport").prop('href', base_url + "/event/work_diary/export_pdf/"+id+"/" + name);
      $("#btnNext").addClass('d-none');
      $("#btnExport").removeClass('d-none');
      $("#titleExport").attr('disabled', true);
  });

  $("#btnExport").on('click',()=>{
      $("#titleExport").val('')
      $("#titleExport").attr('disabled', false);
      $("#btnNext").removeClass('d-none');
      $("#btnExport").addClass('d-none');
  })






  $(document).on('click','.filter',(e)=>{
     const item = $(e.currentTarget).attr('data-count')
     $('#buttonOccurrence').attr('data-count',item)
     $('#ModalSelectOcurrence').modal('show')
  })

  $('#buttonOccurrence').on('click',()=>{
    const item = $('#buttonOccurrence').attr('data-count')
    
    if($('#idOccurence').val()){
        $('#item-'+item).val($('#idOccurence').val())    
        const but_occurrence = $('#item-'+item).siblings('.show_occurence_id')[0]
        $(but_occurrence).removeClass('d-none')
        $(but_occurrence).children('i').html($('#idOccurence').val())
    }
    $('#ModalSelectOcurrence').modal('hide')

})


  $('#idOccurence').select2({
    theme: 'bootstrap4',
    ajax: {
      url: base_url+'/helper/get_occurrences',
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
        if (response.current_page == 1){ data_select = response.data }
        else{ data_select = data_select.concat(response.data) }

        // Transforms the top-level key of the response object from 'items' to 'results'
         let more_pagination = true;
         //se não tem mais paginas
         if (response.next_page_url == null){ more_pagination = false }
         return {
             results:response.data,
             pagination: {
                "more": more_pagination
              }
            }
       }
    }
});

    function getShiftTime (){
      const morning =  $('.morning') 
      const afternoon =  $('.afternoon')
      const night =  $('.night')
      

      let shiftTime = [
      {
        shift:'morning',
        clear:$(morning[0]).val(),
        cloudy:$(morning[1]).val(),
        rain:$(morning[2]).val(),
        impractical:$(morning[3]).val()
      },
      {
        shift:'afternoon',
        clear:$(afternoon[0]).val(),
        cloudy:$(afternoon[1]).val(),
        rain:$(afternoon[2]).val(),
        impractical:$(afternoon[3]).val()
      },
      {
        shift:'night',
        clear:$(night[0]).val(),
        cloudy:$(night[1]).val(),
        rain:$(night[2]).val(),
        impractical:$(night[3]).val()
      }

      ]
      

      return shiftTime
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
    
    const activity_ids =  $('.activity_id') 
    const activity_sectors =  $('.activity_sector') 
    const activity_teams =  $('.activity_team') 
    const activity_occurrences_ids =  $('.activity_occurrences_id')
    const activity_descriptions =  $('.activity_description')
    //const activity_attachments =  $('.activity_attachment')
    
    let activities = []
    activity_sectors.each((index,element) => {
     
     const item = {
       id: $(activity_ids[index]).val(),
       sector: $(element).val(), 
       team: $(activity_teams[index]).val(),
       occurrence_id: $(activity_occurrences_ids[index]).val(),
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