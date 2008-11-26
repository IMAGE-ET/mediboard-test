<!-- $Id$ -->

<table class="tbl">
  <tr class="clear">
    <th colspan="6">
      <a href="#" onclick="window.print()">
        Rapport du {{mb_value object=$filter field=_date_min}}
        {{if $filter->_date_min != $filter->_date_max}}
        au {{mb_value object=$filter field=_date_max}}
        {{/if}}
      </a>
    </th>
  </tr>
  
  {{foreach from=$listPlage item=curr_plage}}
  <tr class="clear">
    <td colspan="10">
      <h2>
      	{{mb_value object=$curr_plage field=date}}
      	- 
      	Dr {{$curr_plage->_ref_chir->_view}}
      </h2>
    </td>
  </tr>
  
  <tr>
    <th rowspan="2" colspan="2"><b>Heure</b></th>
    <th {{if $coordonnees}}colspan="4"{{else}}colspan="2"{{/if}}><b>Patient</b></th>
    <th colspan="3"><b>Consultation</b></th>
  </tr>
  
  <tr>
    <th>Nom / Prénom</th>
    {{if $coordonnees}}
    <th>Adresse</th>
    <th>Tel</th>
    {{/if}}
    <th>Age</th>
    <th>Motif</th>
    <th>Remarques</th>
    <th>Durée</th>
  </tr>
  
  {{foreach from=$curr_plage->_ref_consultations item=curr_consult}}
  <tbody class="hoverable">
  
  <tr>
    {{assign var=categorie value=$curr_consult->_ref_categorie}}
    <td rowspan="2" {{if !$categorie->_id}}colspan="2"{{/if}} style="text-align: center; {{if $curr_consult->premiere}}background-color:#eaa;{{/if}}">
      {{mb_value object=$curr_consult field=heure}}
		</td>

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

    <td rowspan="2">
      {{$patient->_age}} ans
      {{if $patient->_age != "??"}}
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
	
	    Intervention le {{$operation->_datetime|date_format:$dPconfig.date}}
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
  </tr>
  
  </tbody>
    
  {{foreachelse}}
  <tr>
  	<td colspan="10">
  	  <em>{{tr}}CConsultation.none{{/tr}}</em>
  	</td>
  </tr>
  {{/foreach}}
  
  {{foreachelse}}
  <tr>
  	<td>
  	  <em>{{tr}}CPlageconsult.none{{/tr}}</em>
  	</td>
  </tr>
  {{/foreach}}
</table>

<