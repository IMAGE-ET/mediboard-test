{{if $can->admin}}
  <a href="?m=dPcompteRendu&amp;a=aides_export_csv&amp;suppressHeaders=1&amp;owner={{$owner}}&amp;object_class={{$filter_class}}{{foreach from=$aides item=_aide}}&amp;id[]={{$_aide->_id}}{{/foreach}}" 
     target="_blank"
     class="button hslip">Exporter au format CSV</a>
  
  <a href="#1" onclick="return popupImport('{{$owner->_guid}}');" class="button hslip">Importer un fichier CSV</a>
{{/if}}

<table class="tbl">
<tr>
  <th>{{mb_title class=CAideSaisie field=class}}</th>
  <th>{{mb_title class=CAideSaisie field=field}}</th>
  <th>{{mb_title class=CAideSaisie field=depend_value_1}}</th>
  <th>{{mb_title class=CAideSaisie field=depend_value_2}}</th>
  <th>{{mb_title class=CAideSaisie field=name}}</th>
  <th>{{mb_title class=CAideSaisie field=text}}</th>
</tr>

{{foreach from=$aides item=_aide}}
<tr {{if $_aide->_id == $aide->_id}}class="selected"{{/if}}>
  {{assign var="aide_id" value=$_aide->aide_id}}
  {{assign var="href" value="?m=$m&tab=$tab&aide_id=$aide_id"}}
  {{assign var="class" value=$_aide->class}}
  {{assign var="field" value=$_aide->field}}
  <td><a href="{{$href}}">{{tr}}{{$class}}{{/tr}}</a></td>
  <td><a href="{{$href}}">{{$field}}</a></td>
  <td>
    <a href="{{$href}}">
    {{if $_aide->_depend_field_1}}
      {{if $_aide->depend_value_1}}
        {{tr}}{{$class}}.{{$_aide->_depend_field_1}}.{{$_aide->depend_value_1}}{{/tr}}
      {{else}}
        {{tr}}None{{/tr}}
      {{/if}}
    {{else}}
      &mdash;
    {{/if}}
    </a>
  </td>
  <td>
    <a href="{{$href}}">
    {{if $_aide->_depend_field_2}}
      {{if $_aide->depend_value_2}}
        {{tr}}{{$class}}.{{$_aide->_depend_field_2}}.{{$_aide->depend_value_2}}{{/tr}}
      {{else}}
        {{tr}}None{{/tr}}
      {{/if}}
    {{else}}
      &mdash;
    {{/if}}
    </a>
  </td>
  <td class="text"><a href="{{$href}}">{{mb_value object=$_aide field=name}}</a></td>
  <td class="text">{{mb_value object=$_aide field=text}}</td>
</tr>
{{foreachelse}}
<tr>
  <td colspan="10"><em>{{tr}}CAideSaisie.none{{/tr}}</em></td>
</tr>
{{/foreach}}

</table>