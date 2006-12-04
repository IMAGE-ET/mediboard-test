<form name="editConfigdPplanningOp" action="./index.php?m={{$m}}&amp;a=configure" method="post" onSubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />
<table class="form">
  <tr>
    <th class="category" colspan="6">{{tr}}CFile{{/tr}}</th>
  </tr>
  
  <tr>
    <th>
      <label for="dPfiles[nb_upload_files]" title="{{tr}}config-dPfiles-nb_upload_files{{/tr}}">{{tr}}config-dPfiles-nb_upload_files{{/tr}}</label>  
    </th>
    <td>
      <select title="num" name="dPfiles[nb_upload_files]">
      {{html_options options=$listNbFiles selected=$configFiles.nb_upload_files}}
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