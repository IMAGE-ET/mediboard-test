    {{if $categorie->_id}}
    <td rowspan="2" style="{{if $curr_consult->premiere}}background-color:#eaa;{{/if}}">
      <img src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" alt="{{$categorie->nom_categorie}}" title="{{$categorie->nom_categorie}}" />
    </td>
    {{/if}}
    
    {{if $curr_consult->patient_id}}
    {{assign var=patient value=$curr_consult->_ref_patient}}
    <td rowspan="2">
      {{$patient->_view}}
    </td>
    
      {{if $coordonnees}}
      <td rowspan="2" class="text">
        {{mb_value object=$patient field=adresse}}
        <br />
        {{mb_value object=$patient field=cp}} 
        {{mb_value object=$patient field=ville}}
      </td>
      
      <td rowspan="2">
        {{mb_value object=$patient field=tel}}
        <br />
        {{mb_value object=$patient field=tel2}}
      </td>
      {{/if}}

    <td rowspan="2" style="text-align: center; ">
      {{$patient->_age}} ans
      {{if $patient->_age != "??"}}
        <br />
        ({{mb_value object=$patient field="naissance"}})
      {{/if}}
    </td>
    
    {{else}}
    <td rowspan="2" colspan="{{if $coordonnees}}4{{else}}2{{/if}}">
      [PAUSE]
    </td>
    {{/if}}
    
    {{assign var=consult_anesth value=$curr_consult->_ref_consult_anesth}}
    <td {{if !$consult_anesth->operation_id}}rowspan="2"{{/if}} class="text">
      {{mb_value object=$curr_consult field=motif}}
    </td>
    
    <td {{if !$consult_anesth->operation_id}}rowspan="2"{{/if}} class="text">
      {{mb_value object=$curr_consult field=rques}}
    </td>
    
    <td rowspan="2">
      {{if $curr_consult->duree !=  1}}
      {{$curr_consult->duree}} x 
      {{/if}}
      {{$curr_plage->freq|date_format:"%M"}}min
    </td>
  </tr>
  
  <tr>
    {{* Keep table row out of condition *}}
    {{if $consult_anesth->operation_id}}
    <td colspan="2" class="text">
      <div style="border-left: 4px solid #aaa; padding-left: 5px;">
      {{assign var=operation value=$consult_anesth->_ref_operation}}
  
      Intervention le {{$operation->_datetime|date_format:$conf.date}}
      - Dr {{$operation->_ref_praticien->_view}}<br />
      {{if $operation->libelle}}
        <em>[{{$operation->libelle}}]</em>
        <br />
      {{/if}}
      {{foreach from=$operation->_ext_codes_ccam item=curr_code}}
        {{if !$curr_code->_code7}}<strong>{{/if}}
        <small>{{$curr_code->code}} : {{$curr_code->libelleLong}}</small>
        {{if !$curr_code->_code7}}</strong>{{/if}}
        <br/>
      {{/foreach}}
      </div>
      {{/if}}
    </td>