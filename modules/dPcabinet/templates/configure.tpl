<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  <tr>
    <th class="category" colspan="6">{{tr}}CPlageconsult{{/tr}}</th>
  </tr>
  
  {{assign var="pcTr" value="config-dPcabinet-CPlageconsult"}}
  {{assign var="pcName" value="config-dPcabinet-CPlageconsult"}}
  <tr>
    <th>
      <label for="{{$pcName}}[hours_start]" title="{{tr}}{{$pcTr}}-hours_start{{/tr}}">{{tr}}{{$pcTr}}-hours_start{{/tr}}</label>  
    </th>
    <td>
      <select class="num" name="{{$pcName}}[hours_start]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $pcConfig.hours_start}} selected="selected" {{/if}}>{{$_hour|string_format:"%02d"}}</option>
      {{/foreach}}
      </select>
    </td>

    <th>
      <label for="{{$pcName}}[hours_stop]" title="{{tr}}{{$pcTr}}-hours_stop{{/tr}}">{{tr}}{{$pcTr}}-hours_stop{{/tr}}</label>  
    </th>
    <td>
      <select class="num" name="{{$pcName}}[hours_stop]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $pcConfig.hours_stop}} selected="selected" {{/if}}>{{$_hour|string_format:"%02d"}}</option>
      {{/foreach}}
      </select>
    </td>

    <th>
      <label for="{{$pcName}}[minutes_interval]" title="{{tr}}{{$pcTr}}-minutes_interval{{/tr}}">{{tr}}{{$pcTr}}-minutes_interval{{/tr}}</label>  
    </th>
    <td>
      <select class="num" name="{{$pcName}}[minutes_interval]">
      {{foreach from=$intervals item=_interval}}
        <option value="{{$_interval}}" {{if $_interval == $pcConfig.minutes_interval}} selected="selected" {{/if}}>{{$_interval|string_format:"%02d"}}</option>
      {{/foreach}}
      </select>
    </td>
  </tr>  
    
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>