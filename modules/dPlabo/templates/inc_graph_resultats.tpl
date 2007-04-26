{{* $Id$ *}}

{{if !$prescriptionItem->_id}}
<table class="form">
    <th class="title">
      Veuillez sélectioner un examen
    </th>
  </tr>
</table>
{{else}}
{{assign var="examen" value=$prescriptionItem->_ref_examen_labo}}
{{assign var="patient" value=$prescriptionItem->_ref_prescription_labo->_ref_patient}}
{{if $examen->type == "num"}}
<div id="resultGraph" style="text-align: center;">
  <img alt="Graph des résultats" 
    src='?m=dPlabo&amp;a=graph_resultats&amp;suppressHeaders=1&amp;patient_id={{$patient->_id}}&amp;examen_id={{$examen->_id}}&amp;time={{$time}}' 
    height="250"
    style="margin: 5px auto"
  />
</div>
{{/if}}

<table class="tbl">
  <tr>
    <th colspan="3" class="title">Résultats '{{$examen->_view}}'</th>
  </tr>
  <tr>
    <th>Valeur</th>
    <th>Résultat au</th>
    <th>Prescrit le</th>
  </tr>

  {{foreach from=$siblingItems item="_item"}}
  <tbody class="hoverable">
    <tr {{if $_item->_id == $prescriptionItem->_id}}class="selected"{{/if}}>
      {{if $_item->date}}
      <td {{if $_item->commentaire}}rowspan="2"{{/if}}>
        {{assign var=msgClass value=""}}
        {{if $examen->type == "num"}}
          {{if $_item->_hors_limite}}
          {{assign var=msgClass value="warning"}}
          {{else}}
          {{assign var=msgClass value="message"}}
          {{/if}}
        {{/if}}
        
        <div class="{{$msgClass}}">
        {{if $examen->type == "bool"}}
        {{tr}}bool.{{$_item->resultat}}{{/tr}}
        {{else}}
        {{$_item->resultat}} 
        {{/if}}
        {{mb_value object=$examen field=unite}}
        </div>
      </td>
      <td>{{mb_value object=$_item field=date}}</td>
      {{else}}
      <td colspan="2" style="text-align: center">
        <em>Aucun résultat</em>
      </td>
      {{/if}}
      <td>{{mb_value object=$_item->_ref_prescription_labo field=date format="%d/%m/%Y"}}</td>
    </tr>
    {{if $_item->commentaire}}
    <tr {{if $_item->_id == $prescriptionItem->_id}}class="selected"{{/if}}>
      <td class="text" colspan="2">{{mb_value object=$_item field=commentaire}}</td>
    </tr>
    {{/if}}
  </tbody>
  {{/foreach}}
</table>
{{/if}}
