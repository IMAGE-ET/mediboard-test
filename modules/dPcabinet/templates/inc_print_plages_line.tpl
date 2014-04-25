{{if $curr_consult->premiere}}
  {{assign var=consult_background value="background-color:#faa;"}}
{{elseif $curr_consult->derniere}}
  {{assign var=consult_background value="background-color:#faf;"}}
{{else}}
  {{assign var=consult_background value=""}}
{{/if}}

{{if $categorie->_id}}
  <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="{{$consult_background}}">
    {{mb_include module=cabinet template=inc_icone_categorie_consult
      categorie=$categorie
    }}
  </td>
{{/if}}
    
{{if $curr_consult->patient_id}}
  {{assign var=patient value=$curr_consult->_ref_patient}}
  <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="{{$consult_background}}" class="text">
    {{$patient}}
    {{if $filter->_print_ipp && $patient->_IPP}}
      [{{$patient->_IPP}}]
    {{/if}}
  </td>

  {{if $filter->_coordonnees}}
    <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} class="text" style="{{$consult_background}}">
      {{mb_value object=$patient field=adresse}}
      <br />
      {{mb_value object=$patient field=cp}}
      {{mb_value object=$patient field=ville}}
    </td>

    <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="{{$consult_background}}">
      {{mb_value object=$patient field=tel}}
      <br />
      {{mb_value object=$patient field=tel2}}
    </td>
  {{elseif $filter->_telephone}}
    <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="{{$consult_background}}">
      {{mb_value object=$patient field=tel}}
      <br />
      {{mb_value object=$patient field=tel2}}
    </td>
  {{/if}}
  <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="text-align: center; {{$consult_background}}">
    {{$patient->_age}}
    {{if $patient->_annees != "??"}}
      <br />
      ({{mb_value object=$patient field="naissance"}})
    {{/if}}
  </td>
  {{if $show_lit}}
    <td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="{{$consult_background}}">
      {{$patient->_ref_curr_affectation}}
    </td>
  {{/if}}
{{else}}
  <td colspan="{{math equation='x-5' x=$main_colspan}}" style="{{$consult_background}}">
    [PAUSE]
  </td>
{{/if}}

<td class="text" style="{{$consult_background}}">
  {{if $categorie->_id}}
    <div>
      {{mb_include module=cabinet template=inc_icone_categorie_consult
        categorie=$categorie
        display_name=true
      }}
    </div>
  {{/if}}
  {{mb_value object=$curr_consult field=motif}}
</td>

<td class="text" style="{{$consult_background}}">
  {{mb_value object=$curr_consult field=rques}}
</td>

<td {{if $consult_anesth->operation_id}}rowspan="2"{{/if}} style="{{$consult_background}}">
  {{if $curr_consult->duree !=  1}}
  {{$curr_consult->duree}} x
  {{/if}}
  {{$curr_plage->freq|date_format:"%M"}}min
</td>

{{if $consult_anesth->operation_id}}
  </tr>
  <tr>
    {{* Keep table row out of condition *}}
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
    </td>
{{/if}}