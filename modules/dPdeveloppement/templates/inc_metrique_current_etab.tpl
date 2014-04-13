<table class="tbl main">
  <tr>
    <th width="50%">Type de données</th>
    <th width="50%">Quantité</th>
  </tr>
  {{foreach from=$res_current_etab item=curr_res key=field_res}}
  <tr>
    <td>{{tr}}{{$field_res}}{{/tr}}</td>
    <td>{{$curr_res|integer}}</td>
  </tr>
  {{/foreach}}
</table>