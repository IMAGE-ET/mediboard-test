<form name="Filter" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id=0{{if $dialog}}&amp;object_class={{$filter->object_class}}&amp;object_id={{$filter->object_id}}{{/if}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />


<table class="form">
  <tr>
    <th class="category" colspan="6">
      {{$marks|@count}} {{tr}}CTriggerMark{{/tr}} 
      {{if $marks|@count != $count}}
      sur {{$count}}
      {{/if}}
      {{tr}}found{{/tr}}
    </th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=trigger_class}}</th>
    <td>
      <select name="trigger_class" class="str">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        {{foreach from=$trigger_classes item=_class}}
        <option value="{{$_class}}" {{if $_class == $filter->trigger_class}}selected="selected"{{/if}}>
          {{tr}}{{$_class}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=trigger_number}}</th>
    <td>{{mb_field object=$filter field=trigger_number canNull=true}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=mark}}</th>
    <td>{{mb_field object=$filter field=mark canNull=true}}</td>
    <th>{{mb_label object=$filter field=done}}</th>
    <td>{{mb_field object=$filter field=done typeEnum=select defaultOption="Tous" canNull=true}}</td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="search" type="submit">{{tr}}Search{{/tr}}</button>
    </td>
  </tr>
</table>

</form>


