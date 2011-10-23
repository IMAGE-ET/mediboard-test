{{assign var=m value=system}}
{{assign var=class value=CMessage}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{mb_include module=system template=inc_config_str var=default_email_from}}
  {{mb_include module=system template=inc_config_str var=default_email_to}}
  
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>

{{mb_include module=system template=inc_config_exchange_source source=$message_smtp}}