<form name="editConfigEtiquettes" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    {{assign var="class" value="CChambre"}}
    {{mb_include module=system template=inc_config_str var=tag}}
    {{assign var="class" value="CLit"}}
    {{mb_include module=system template=inc_config_str var=tag}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>