<table class="tbl main">
  <tr>
    <th>Type de données</th>
    <th>Quantité</th>
    <th>Dernière mise à jour</th>
  </tr>
  {{foreach from=$result item=curr_result key=class}}
  <tr>
    <td>{{tr}}{{$class}}{{/tr}}</td>
    <td>{{$curr_result.Rows}}</td>
    <td>{{$curr_result.Update_time|date_format:"%d/%m/%Y %Hh%M"}}</td>
  </tr>
  {{/foreach}}
</table>