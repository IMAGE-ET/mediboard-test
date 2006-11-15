<form name="editConfigdPplanningOp" action="./index.php?m={{$m}}&amp;a=configure" method="post" onSubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />
<table class="form">
  <tr>
    <th>
      <label for="dPImeds[url]" title="{{tr}}config-dPImeds-url{{/tr}}">{{tr}}config-dPImeds-url{{/tr}}</label>  
    </th>
    <td>
      <input type="text" name="dPImeds[url]" value="{{$configurl}}"/>
    </td>
  </tr>  
    
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>