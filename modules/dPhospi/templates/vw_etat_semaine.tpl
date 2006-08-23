<table class="tbl">
  <tr>
    <th class="title">Date</th>
    <th class="title">Nombre d'admissions non placées</th>
  </tr>
  {{foreach from=$list key=date item=sejour}}
  <tr>
    <td>{{$date|date_format:"%a %d %b %Y"}}</td>
    <td>{{$sejour|@count}} admission(s)</td>
  </tr>
  {{/foreach}}
</table>