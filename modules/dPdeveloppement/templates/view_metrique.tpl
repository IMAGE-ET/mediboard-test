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
    <td>
      {{assign var=relative value=$curr_result.Update_relative}}
      <label title="{{$curr_result.Update_time|date_format:$dPconfig.datetime}}">
      	{{$relative.count}} {{tr}}{{$relative.unit}}{{if $relative.count > 1}}s{{/if}}{{/tr}}
      </label>
    </td>
  </tr>
  {{/foreach}}
</table>