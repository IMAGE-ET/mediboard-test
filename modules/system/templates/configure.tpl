<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  {{assign var="category" value="ui"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$category}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="currency_symbol"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="hide_confidential"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="locale_warn"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="locale_alert"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="debug"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="readonly"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="category" value="formats"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$category}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="date"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>
    
  {{assign var="var" value="time"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>
    
  {{assign var="var" value="datetime"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>
    
  {{assign var="var" value="longdate"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="type_telephone"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select name="{{$m}}[{{$var}}]">
        <option value="france" {{if $dPconfig.$m.$var == "france"}} selected="selected" {{/if}}>France</option>
        <option value="suisse" {{if $dPconfig.$m.$var == "suisse"}} selected="selected" {{/if}}>Suisse</option>
      </select>
    </td>
  </tr>

  {{assign var="category" value="system"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$category}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="reverse_proxy"}}
  <tr>
    <th>
      <label for="{{$var}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>
        
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>