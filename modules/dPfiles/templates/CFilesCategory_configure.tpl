{{assign var=class value=CFilesCategory}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />

  {{mb_include module=system template=inc_config_bool var=show_empty}}
    
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
