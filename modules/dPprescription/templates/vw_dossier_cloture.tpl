<table class="tbl">
<tr>

  <th class="title" colspan="2">
  <div style="float: right">{{$dateTime}}</div>
  Dossier clôturé
  </th>
</tr>
<tr>
  <td>
    {{$sejour->_ref_patient->_view}}
  </td>
  <td>
  </td>
</tr>
<tr>
  <th>Libelle</th>
  <th>Quantite: administration</th>
</tr>
{{foreach from=$dossier key=date item=lines_by_cat}}
  <tr>
    <th colspan="2">{{$date}}</th>
  </tr>
  {{foreach from=$lines_by_cat key=chap item=lines}}
  <tr>
    <td colspan="2">
      <strong>{{$chap}}</strong>
    </td>
  </tr>
    {{foreach from=$lines key=line_id item=administrations}}
      {{if $chap == "medicament"}}
        {{assign var=line value=$lines_med.$line_id}}
      {{else}}
        {{assign var=line value=$lines_elt.$line_id}}
      {{/if}}
      <tr>
        <td>
          {{$line->_view}}
        </td>
        <td>  
          {{foreach from=$administrations key=quantite item=_administrations_by_quantite}}
            {{$quantite}} 
            {{if $line->_class_name == "CPrescriptionLineMedicament"}}
              {{$line->_ref_produit->libelle_unite_presentation}}
            {{else}}
              {{$line->_unite_prise}}
            {{/if}}: 
            {{foreach from=$_administrations_by_quantite item=_administration}}
            {{$_administration->dateTime|date_format:"%Hh%M"}}
            {{/foreach}}
            <br />
          {{/foreach}}
        </td>  
        </tr>
    {{/foreach}} 
  {{/foreach}}
{{/foreach}}
</table>