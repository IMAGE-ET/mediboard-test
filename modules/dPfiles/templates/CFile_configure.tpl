{{assign var=class value=CFile}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />
    
  {{mb_include module=system template=inc_config_str var=upload_directory size=40}}

  {{assign var=class value=""}}
  {{mb_include module=system template=inc_config_str var=upload_max_filesize maxlength=4 size=2}}
  {{mb_include module=system template=inc_config_str var=extensions_yoplet}}
  {{mb_include module=system template=inc_config_str var=yoplet_upload_url}}
  {{mb_include module=system template=inc_config_str var=yoplet_upload_path}}
  {{assign var=class value="CFile"}}
  {{mb_include module=system template=inc_config_bool var=merge_to_pdf}}
  
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
