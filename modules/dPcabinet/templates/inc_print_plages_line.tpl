    {{if $curr_consult->premiere}}
      {{assign var=consult_background value="background-color:#faa;"}}
    {{elseif $curr_consult->derniere}}
      {{assign var=consult_background value="background-color:#faf;"}}
    {{else}}
      {{assign var=consult_background value=""}}
    {{/if}}
    
    {{if $categorie->_id}}
    <td rowspan="2" style="{{$consult_background}}">
      {{mb_include module=cabinet template=inc_icone_categorie_consult categorie=$categorie alt=$categorie->nom_categorie
        title=$categorie->nom_categorie}}
    </td>
    {{/if}}
    
    {{if $curr_consult->patient_id}}
    {{assign var=patient value=$curr_consult->_ref_patient}}
    <td rowspan="2" style="{{$consult_background}}">
      {{$patient->_view}}
    </td>
    
      {{if $filter->_coordonnees}}
      <td rowspan="2" class="text" style="{{$consult_background}}">
        {{mb_value object=$patient field=adresse}}
        <br />
        {{mb_value object=$patient field=cp}} 
        {{mb_value object=$patient field=ville}}
      </td>
      
      <td rowspan="2" style="{{$consult_background}}">
        {{mb_value object=$patient field=tel}}
        <br />
        {{mb_value object=$patient field=tel2}}
      </td>
      {{elseif $filter->_telephone}}
        <td rowspan="2" style="{{$consult_background}}">
          {{mb_value object=$patient field=tel}}
          <br />
          {{mb_value object=$patient field=tel2}}
        </td>
      {{/if}}
    <td rowspan="2" style="text-align: center; {{$consult_background}}">
      {{$patient->_age}}
      {{if $patient->_annees != "??"}}
        <br />
        ({{mb_value object=$patient field="naissance"}})
      {{/if}}
    </td>
    {{if $show_lit}}
      <td rowspan="2" style="{{$consult_background}}">
        {{$patient->_ref_curr_affectation}}
      </td>
    {{/if}}
    
    {{else}}
    <td rowspan="2" colspan="{{if $filter->_coordonnees}}4{{else}}2{{/if}}" style="{{$consult_background}}">
      [PAUSE]
    </td>
    {{/if}}
    
    {{assign var=consult_anesth value=$curr_consult->_ref_consult_anesth}}
    <td {{if !$consult_anesth->operation_id}}rowspan="2"{{/if}} class="text" style="{{$consult_background}}">
      {{if $categorie->_id}}
        <div>
          {{mb_include module=cabinet template=inc_icone_categorie_consult categorie=$categorie alt=$categorie->nom_categorie
            title=$categorie->nom_categorie}}
          {{$categorie->nom_categorie}}
        </div>
      {{/if}}
      {{mb_value object=$curr_consult field=motif}}
    </td>
    
    <td {{if !$consult_anesth->operation_id}}rowspan="2"{{/if}} class="text" style="{{$consult_background}}">
      {{mb_value object=$curr_consult field=rques}}
    </td>
    
    <td rowspan="2" style="{{$consult_background}}">
      {{if $curr_consult->duree !=  1}}
      {{$curr_consult->duree}} x 
      {{/if}}
      {{$curr_plage->freq|date_format:"%M"}}min
    </td>
  </tr>
  
  <tr>
    {{* Keep table row out of condition *}}
    {{if $consult_anesth->operation_id}}
    <td colspan="2" class="text" style="{{$consult_background}}">
      <div style="border-left: 4px solid #aaa; padding-left: 5px;">
      {{assign var=operation value=$consult_anesth->_ref_operation}}
  
      Intervention le {{$operation->_datetime|date_format:$conf.date}}
      - Dr {{$operation->_ref_praticien->_view}}<br />
      {{if $operation->libelle}}
        <em>[{{$operation->libelle}}]</em>
        <br />
      {{/if}}
      <!--
      {{foreach from=$operation->_ext_codes_ccam item=curr_code}}
        {{if !$curr_code->_code7}}<strong>{{/if}}
        <small>{{$curr_code->code}} : {{$curr_code->libelleLong}}</small>
        {{if !$curr_code->_code7}}</strong>{{/if}}
        <br/>
      {{/foreach}}
      -->
      </div>
      {{/if}}
    </td>