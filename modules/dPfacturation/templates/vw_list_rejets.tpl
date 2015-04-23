<table class="tbl">
  <tr>
    <th colspan="6">{{tr}}CFactureRejet{{/tr}}</th>
  </tr>
  <tr>
    <th>{{mb_label class=CFactureRejet field=date}}</th>
    <th>{{mb_label class=CFactureRejet field=name_assurance}}</th>
    <th>{{mb_label class=CFactureRejet field=num_facture}}</th>
    <th>{{mb_label class=CFactureRejet field=file_name}}</th>
    <th>{{mb_label class=CFactureRejet field=statut}}</th>
    <th>{{mb_label class=CFactureRejet field=motif_rejet}}</th>
  </tr>
  {{foreach from=$rejets item=_rejet}}
    <tr>
      <td>{{mb_value object=$_rejet field=date}}</td>
      <td>{{mb_value object=$_rejet field=name_assurance}}</td>
      <td>{{mb_value object=$_rejet field=num_facture}}</td>
      <td>{{mb_value object=$_rejet field=file_name}}</td>
      <td>{{mb_value object=$_rejet field=statut}}</td>
      <td class="text">{{mb_value object=$_rejet field=motif_rejet}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="6">{{tr}}CFactureRejet.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
