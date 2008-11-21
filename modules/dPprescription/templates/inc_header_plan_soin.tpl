<tbody style="page-break-before: always" {{if !$no_class}}class="header"{{/if}} style="border-top: 2px solid black;">
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
	      Début du séjour: {{$sejour->_entree|date_format:"%d/%m/%Y à %Hh%M"}}<br />
	      {{if $sejour->_ref_curr_affectation->_id}}
	        Chambre {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}
	      {{/if}}
	    </td>
	    <td style="border:none;">
	      Feuille de soin du {{$date|date_format:"%d/%m/%Y"}}
	    </td>
	    </tr>
     </table>
  </td>
</tr>
{{/if}}
<tr>
  <th colspan="1000">
    {{if $name != "Médicament"}}
      {{tr}}{{$name}}{{/tr}}
    {{else}}
      {{$name}}
    {{/if}}
  </th>
</tr>
<tr>
  <th colspan="2" class="title" style="width: 7cm">Prescription</th>
  <th rowspan="2" class="title" style="width: 1cm">Prescripteur</th>
  <th rowspan="2" style="width: 5px"></th>
  {{foreach from=$dates item=date}}
  <th colspan="{{$tabHours.$date|@count}}" class="title" style="width: 5cm; border-right: 1px solid black; border-left: 1px solid black;">{{$date|date_format:"%d/%m/%Y"}}</th>
  {{/foreach}}
</tr>
<tr>
  <th class="title" style="width: 4cm">Produit</th>
  <th class="title" style="width: 3cm">Posologie</th>
  {{foreach from=$dates item=date }}
    {{foreach from=$tabHours.$date item=_hour name=foreach_date}}
      <th style="{{if $smarty.foreach.foreach_date.first}}border-left: 1px solid black;{{/if}}
                 {{if $smarty.foreach.foreach_date.last}}border-right: 1px solid black;{{/if}}">
        {{$_hour}}h
      </th>
    {{/foreach}}
  {{/foreach}}
</tr>
</tbody>