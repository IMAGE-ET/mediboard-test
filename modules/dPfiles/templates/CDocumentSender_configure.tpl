{{assign var=class value=CDocumentSender}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />
    
  {{assign var=class value=""}}
  {{mb_include module=system template=inc_config_enum var=system_sender values="|CEcDocumentSender|CMedinetSender|CDMPSender|CDocumentSourceSender"}}
  {{assign var=class value=CDocumentSender}}
  {{mb_include module=system template=inc_config_str var=auto_max_load}}
  {{mb_include module=system template=inc_config_str var=auto_max_send}}

  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
