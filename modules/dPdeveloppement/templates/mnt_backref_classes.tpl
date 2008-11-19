<form action="?" name="mntBackRef" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  <label for="class_name" title="Veuillez sélectionner une classe">Choix de la classe</label>
  <select name="class_name" onchange="submit();">
    <option value=""{{if !$class_name}} selected="selected"{{/if}}>&mdash; Toutes les classes</option>
    {{foreach from=$list_class_names item=curr_class_name}}
    <option value="{{$curr_class_name}}"{{if $class_name==$curr_class_name}} selected="selected"{{/if}}>{{$curr_class_name}} - {{tr}}{{$curr_class_name}}{{/tr}}</option>
    {{/foreach}}
  </select>
</form>

<table class="tbl">
  <tr>
    <th>Alerte</th>
    <th>Nom</th>
    <th>Classe</th>
    <th>Champ</th>
    <th>Traduction</th>
  </tr>

{{foreach from=$list_selected_classes item=curr_class}}
  {{if $list_suggestions.$curr_class|@count}}
 	<tr>
    <th colspan="100" class="title">
      {{if $list_suggestions.$curr_class|@count}}
     	<button id="suggestion-{{$curr_class}}-trigger" class="edit" style="float: left;">{{tr}}Suggestion{{/tr}}</button>
      {{/if}}
      {{$curr_class}} ({{tr}}{{$curr_class}}{{/tr}}) 
    </th>
  </tr>
  {{if $list_suggestions.$curr_class|@count||($list_backspecs.$curr_class|@count&&$list_selected_classes|@count==1)}}
  <tr id="suggestion-{{$curr_class}}">
    <td colspan="100">
      <script type="text/javascript">new PairEffect('suggestion-{{$curr_class}}');</script>
      <pre>{{$list_suggestions.$curr_class}}</pre>
    </td>
  </tr>
  {{/if}}
  
  {{foreach from=$list_backspecs.$curr_class item=bs key=key}}
  <tr>
    {{if array_key_exists($key,$list_check_results.$curr_class.excess)}}
      <td class="error">BackRef en trop</td>
    {{else}}
      <td></td>
    {{/if}}
    <td>{{$bs->name}}</td>
    <td>{{$bs->class}}</td>
    <td>{{$bs->field}}</td>
    <td>{{$list_locales.$curr_class.$key}}</td>
  </tr>
  {{/foreach}}
  
  {{foreach from=$list_check_results.$curr_class.missing item=missing}}
  <tr>
    <td class="warning">BackRef manquante</td>
    <td></td>
    <td>{{$missing->className}}</td>
    <td colspan="2">{{$missing->fieldName}}</td>
  </tr>
  {{/foreach}}
  {{/if}}
{{/foreach}}
</table>