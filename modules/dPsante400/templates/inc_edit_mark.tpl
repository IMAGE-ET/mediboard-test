<form name="Edit" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_triggermark_aed" />
<input type="hidden" name="mark_id" value="{{$mark->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">

<tr>
  {{if $mark->_id}}
  <th class="title modify" colspan="2">
    {{mb_include module=system template=inc_object_history object=$mark}}
    {{tr}}CTriggerMark-title-modify{{/tr}} '{{$mark}}'
  </th>
  {{else}}
  <th class="title" colspan="2">
    {{tr}}CTriggerMark-title-create{{/tr}}
  </th>
  {{/if}}
  
  <tr>
    <th>{{mb_label object=$mark field=trigger_class}}</th>
    <td>
      <select name="trigger_class" class="{{$mark->_props.trigger_class}}">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        {{foreach from=$trigger_classes item=_class}}
        <option value="{{$_class}}" {{if $_class == $mark->trigger_class}}selected="selected"{{/if}}>
          {{tr}}{{$_class}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$mark field=trigger_number}}</th>
    <td>{{mb_field object=$mark field=trigger_number}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$mark field=mark}}</th>
    <td>{{mb_field object=$mark field=mark}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$mark field=done}}</th>
    <td>{{mb_field object=$mark field=done}}</td>
  </tr>

        
  <tr>
    <td class="button" colspan="2">
    {{if $mark->_id}}
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {
        typeName: 'La Marque',
        objName: '{{$mark->_view|smarty:nodefaults|JSAttribute}}'
      })">
        {{tr}}Delete{{/tr}}
      </button>
    {{else}}
      <button class="submit" type="submit"	>{{tr}}Create{{/tr}}</button>
    {{/if}}
    </td>
  </tr>

</table>

</form>