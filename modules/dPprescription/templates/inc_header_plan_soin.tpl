<tbody {{if !$chapitre}}style="page-break-before: always"{{/if}} {{if !$no_class}}class="header"{{/if}}>
{{if $patient->_id}}
<tr>
  <td colspan="1000">
    <table style="width: 100%"> 
      <tr>
        <td style="border:none;">
	      IPP: {{$patient->_IPP}}<br />
	      <strong>{{$patient->_view}}</strong>
	    </td>
	    <td style="border:none;">
	      Age {{$patient->_age}}{{if $patient->_age != "??"}} ans{{/if}}<br />
	      Poids {{$poids}}{{if $poids}} kg{{/if}}
	    </td>
	    <td style="border:none;">
	      Début du séjour: {{$sejour->_entree|date_format:$dPconfig.datetime}}<br />
	      {{if $sejour->_ref_curr_affectation->_id}}
	        Chambre {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}
	      {{/if}}
	    </td>
	    <td style="border:none;">
	      Feuille de soin du {{$prescription->_date_plan_soin|date_format:"%d/%m/%Y"}}
	    </td>
	    </tr>
     </table>
  </td>
</tr>
{{/if}}

{{if $patient->_id}}
	<tr>
	{{if $name != "Médicaments"}}
	  <th colspan="1000">{{tr}}{{$name}}{{/tr}}</th>
	{{/if}}
	{{if $chapitre == "inj" || $chapitre == "perf" || $chapitre == "med"}}
	  <th colspan="1000">{{tr}}CPrescriptionLineMedicament._chapitre.{{$chapitre}}{{/tr}}</th>
	{{/if}}
	{{if $chapitre == "all_med"}}
	  <th colspan="1000">Tous les médicaments</th>
	{{/if}}
	</tr>
{{/if}}

<tr>
  <th colspan="2" class="title" style="width: 7cm">Prescription</th>
  <th rowspan="2" class="title" style="width: 1cm">Prescripteur</th>
  <th rowspan="2" style="width: 5px"></th>
  {{foreach from=$tabHours key=_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      <th colspan="{{if $moment_journee == 'soir'}}{{$soir|@count}}{{/if}}
				   				 {{if $moment_journee == 'nuit'}}{{$nuit|@count}}{{/if}}
				     			 {{if $moment_journee == 'matin'}}{{$matin|@count}}{{/if}}"
				  class="title" style="width: 5cm;">{{$moment_journee}} du {{$_date|date_format:"%d/%m/%Y"}}</th>
    {{/foreach}}
  {{/foreach}}
</tr>
<tr>
  <th class="title" style="width: 4cm">Produit</th>
  <th class="title" style="width: 3cm">Posologie</th>
  {{foreach from=$tabHours key=_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date_reelle item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
		      <th>
		        {{$_hour}}h
		      </th>
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</tr>
</tbody>