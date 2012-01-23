<tr>
<td> {{$process_id}} </td>
<td> {{$_process.port}} </td>
<td>  
  {{if !array_key_exists("ps_name", $_process)}} 
    <img class="status" id="{{$uid}}" data-id="{{$process_id}}" src="images/icons/status_red.png" title="Processus absent"/>
  {{else}}
    <img class="status" id="{{$uid}}" data-id="{{$process_id}}" src="images/icons/status_green.png" title="{{$_process.ps_name}}"/> <em>{{$_process.ps_name}}</em>
  {{/if}} 
</td>
<td>
  {{assign var=suffix value=""}}
  {{if $_process.launched_rel.count > 1}}
    {{assign var=suffix value="s"}}
  {{/if}}
  <label title='{{$_process.launched|date_format:$conf.datetime}}'>
    {{$_process.launched_rel.count}} {{tr}} {{$_process.launched_rel.unit}}{{$suffix}} {{/tr}}
  </label>
</td>
<td>
  <button class="trash" onclick="MLLPServer.trash('{{$process_id}}', '{{$uid}}')" type="button">{{tr}}Delete{{/tr}}</button>
  <button class="cancel" onclick="MLLPServer.action('{{$_process.port}}', '{{$process_id}}', '{{$uid}}', 'stop')" type="button">{{tr}}Stop{{/tr}}</button>
  <button class="change" onclick="MLLPServer.action('{{$_process.port}}', '{{$process_id}}', '{{$uid}}', 'restart')" type="button">{{tr}}Restart{{/tr}}</button>
  <button class="tick" onclick="MLLPServer.action('{{$_process.port}}', '{{$process_id}}', '{{$uid}}', 'test')" type="button">{{tr}}Test{{/tr}}</button>
</td>
<td >
  <button class="tick notext" onclick="MLLPServer.action('{{$_process.port}}', '{{$process_id}}', '{{$uid}}', 'stats')" type="button">Statistiques</button>
  <div id="stats_{{$uid}}" style="display: inline-block;">  </div> 
</td>
</tr>