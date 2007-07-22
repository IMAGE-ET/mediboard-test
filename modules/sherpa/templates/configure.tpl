<h1>Configuration de {{$m}}</h1>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  
  <!-- CSpObjectHandler --> 
  {{assign var=col value="object_handlers"}}
  {{assign var=class value="CSpObjectHandler"}}
  <tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    <th>
      <label for="{{$col}}[{{$class}}]" title="{{tr}}config-{{$col}}-{{$class}}{{/tr}}">
        {{tr}}config-{{$col}}-{{$class}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="bool" name="{{$col}}[{{$class}}]">
        <option value="0" {{if 0 == @$dPconfig.$col.$class}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if 1 == @$dPconfig.$col.$class}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>  
    
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

{{assign var=dsn value=sherpa}}
{{include file="../../system/templates/configure_dsn.tpl"}}
  
