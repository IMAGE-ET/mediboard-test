<form name="editCFactureEtablissement-config" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    <tr>
      <th class="title" colspan="2">Général</th>
    </tr>
    <tr>
      <th class="category" colspan="2">{{tr}}CFactureEtablissement{{/tr}}</th>
    </tr>
    {{assign var=class value=CFactureEtablissement}}
    {{mb_include module=system template=inc_config_bool var=use_temporary_bill}}
    {{mb_include module=system template=inc_config_bool var=use_auto_cloture}}
    
    <tr>
      <th class="category" colspan="2">{{tr}}CFactureCabinet{{/tr}}</th>
    </tr>
    {{assign var=class value=CFactureCabinet}}
    {{mb_include module=system template=inc_config_bool var=use_auto_cloture}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>